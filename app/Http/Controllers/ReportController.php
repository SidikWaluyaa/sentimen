<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController
{
    public function exportPdf(Evaluation $evaluation)
    {
        $evaluation->load(['predictionResults' => function ($q) {
            $q->limit(50); // Preview first 50 reviews in PDF for optimal page rendering
        }]);

        $pdf = Pdf::loadView('reports.pdf', [
            'evaluation' => $evaluation,
            'printedAt' => now()->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i:s') . ' WIB',
        ])->setPaper('a4', 'portrait');

        $fileName = 'Laporan_Analisis_Sentimen_' . Str::slug($evaluation->judul_dataset) . '.pdf';
        return $pdf->download($fileName);
    }

    public function exportCsv(Evaluation $evaluation): StreamedResponse
    {
        $fileName = 'Hasil_Prediksi_Sentimen_' . Str::slug($evaluation->judul_dataset) . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($evaluation) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM so Excel opens indonesian characters correctly
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header row
            fputcsv($handle, [
                'ID Review',
                'Nama Pengguna',
                'Ulasan Asli',
                'Ulasan Bersih (Stemmed)',
                'Rating',
                'Sentimen Aktual',
                'Sentimen Prediksi',
                'Sesuai',
                'Relevan ShopeeFood',
            ]);

            $evaluation->predictionResults()->chunk(500, function ($rows) use ($handle) {
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row->id_review,
                        $row->nama_pengguna,
                        $row->ulasan_asli,
                        $row->ulasan_bersih,
                        $row->rating,
                        $row->sentimen_aktual,
                        $row->sentimen_prediksi,
                        $row->is_correct ? 'Ya' : 'Tidak',
                        $row->relevan_shopeefood ? '1' : '0',
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
