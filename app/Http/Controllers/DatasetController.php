<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\PredictionResult;
use App\Services\Dataset\ExcelReaderService;
use App\Services\NLP\SentimentPipeline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatasetController
{
    public function create()
    {
        return view('datasets.upload');
    }

    public function store(Request $request, ExcelReaderService $reader, SentimentPipeline $pipeline)
    {
        $request->validate([
            'judul_dataset' => 'required|string|max:255',
            'file_dataset' => 'required|file|mimes:xlsx,csv,txt|max:20480',
        ], [
            'judul_dataset.required' => 'Judul dataset wajib diisi.',
            'file_dataset.required' => 'Berkas dataset wajib diunggah.',
            'file_dataset.mimes' => 'Format berkas harus berupa .xlsx atau .csv.',
            'file_dataset.max' => 'Ukuran berkas maksimal 20 MB.',
        ]);

        $uploadedFile = $request->file('file_dataset');
        $fileName = $uploadedFile->getClientOriginalName();
        $filePath = $uploadedFile->getRealPath();
        $extension = $uploadedFile->getClientOriginalExtension();

        try {
            $rawRows = $reader->readFile($filePath, $extension);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal membaca berkas: ' . $e->getMessage());
        }

        if (empty($rawRows)) {
            return back()->withInput()->with('error', 'Berkas kosong atau tidak memiliki data.');
        }

        // Verify required columns 'ulasan' and 'rating'
        $firstRow = $rawRows[0];
        if (!array_key_exists('ulasan', $firstRow) || !array_key_exists('rating', $firstRow)) {
            return back()->withInput()->with('error', "Format data tidak valid! Dataset wajib memiliki kolom bernama 'ulasan' dan 'rating'.");
        }

        $validRows = [];
        $evaluationPairs = [];
        $positifCount = 0;
        $negatifCount = 0;

        foreach ($rawRows as $item) {
            $rating = isset($item['rating']) && is_numeric($item['rating']) ? (int)$item['rating'] : null;
            $ulasan = trim($item['ulasan'] ?? '');

            // Aturan filter: lewati rating 3 dan ulasan kosong
            if ($rating === null || $rating === 3 || $ulasan === '') {
                continue;
            }

            $sentimenAktual = ($rating <= 2) ? 'Negatif' : 'Positif';

            // NLP Preprocessing & Machine Learning Inference
            $predResult = $pipeline->predictSingle($ulasan);
            $prediksi = $predResult['prediction'];

            if ($prediksi === 'Positif') {
                $positifCount++;
            } else {
                $negatifCount++;
            }

            $isCorrect = ($sentimenAktual === $prediksi);
            $evaluationPairs[] = ['actual' => $sentimenAktual, 'predicted' => $prediksi];

            $validRows[] = [
                'id_review' => $item['id_review'] ?? (string)Str::uuid(),
                'nama_pengguna' => $item['nama_pengguna'] ?? 'Pengguna',
                'ulasan_asli' => $ulasan,
                'ulasan_bersih' => $predResult['cleaned_text'],
                'rating' => $rating,
                'sentimen_aktual' => $sentimenAktual,
                'sentimen_prediksi' => $prediksi,
                'is_correct' => $isCorrect,
                'relevan_shopeefood' => isset($item['relevan_shopeefood']) && (int)$item['relevan_shopeefood'] === 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (empty($validRows)) {
            return back()->withInput()->with('error', 'Tidak ada baris data valid setelah filter (rating 3 diabaikan).');
        }

        $metrics = $pipeline->calculateMetrics($evaluationPairs);

        DB::beginTransaction();
        try {
            $evaluation = Evaluation::create([
                'user_id' => $request->user()->id,
                'judul_dataset' => $request->input('judul_dataset'),
                'file_name' => $fileName,
                'total_ulasan' => count($validRows),
                'total_positif' => $positifCount,
                'total_negatif' => $negatifCount,
                'akurasi' => $metrics['accuracy'],
                'presisi' => $metrics['precision'],
                'recall' => $metrics['recall'],
                'f1_score' => $metrics['f1'],
            ]);

            $chunks = array_chunk($validRows, 500);
            foreach ($chunks as $chunk) {
                foreach ($chunk as &$row) {
                    $row['evaluation_id'] = $evaluation->id;
                }
                PredictionResult::insert($chunk);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan ke database: ' . $e->getMessage());
        }

        return redirect()->route('dashboard', ['evaluation_id' => $evaluation->id])
            ->with('success', sprintf('Dataset "%s" berhasil diproses! %d ulasan dianalisis (Akurasi: %.1f%%).', $evaluation->judul_dataset, count($validRows), $metrics['accuracy'] * 100));
    }

    public function destroy(Request $request, Evaluation $evaluation)
    {
        if (!$request->user()->isAdmin()) {
            abort(403, 'Hanya Administrator yang berhak menghapus riwayat evaluasi dataset.');
        }

        $judul = $evaluation->judul_dataset;
        $evaluation->delete();

        return redirect()->route('dashboard')->with('success', "Dataset '{$judul}' berhasil dihapus.");
    }
}
