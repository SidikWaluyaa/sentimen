<?php

namespace Database\Seeders;

use App\Models\Evaluation;
use App\Models\PredictionResult;
use App\Models\User;
use App\Services\Dataset\ExcelReaderService;
use App\Services\NLP\SentimentPipeline;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Default Users (Admin & Analyst)
        $admin = User::firstOrCreate(
            ['email' => 'admin@sentiment.id'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        $analyst = User::firstOrCreate(
            ['email' => 'analyst@sentiment.id'],
            [
                'name' => 'Data Analyst',
                'password' => Hash::make('password123'),
                'role' => 'analyst',
            ]
        );

        // 2. Check for initial dataset excel file
        $filePath = database_path('data/KANDIDAT_VALIDASI_SHOPEEFOOD_FINAL.xlsx');
        if (!file_exists($filePath)) {
            $filePath = base_path('KANDIDAT_VALIDASI_SHOPEEFOOD_FINAL.xlsx');
        }

        if (file_exists($filePath)) {
            $this->command->info("Memuat dan menganalisis dataset 1.403 ulasan ShopeeFood...");

            $reader = new ExcelReaderService();
            $pipeline = new SentimentPipeline();

            $rawRows = $reader->readFile($filePath);
            $this->command->info("Total baris terbaca dari Excel: " . count($rawRows));

            $validRows = [];
            $evaluationPairs = [];
            $positifCount = 0;
            $negatifCount = 0;

            foreach ($rawRows as $item) {
                $rating = isset($item['rating']) && is_numeric($item['rating']) ? (int)$item['rating'] : null;
                $ulasan = trim($item['ulasan'] ?? '');

                // Sesuai aturan skripsi/app.py: Lewati rating 3 (netral) dan ulasan kosong
                if ($rating === null || $rating === 3 || $ulasan === '') {
                    continue;
                }

                $sentimenAktual = ($rating <= 2) ? 'Negatif' : 'Positif';

                // Jalankan NLP & Inferensi
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
                    'id_review' => $item['id_review'] ?? null,
                    'nama_pengguna' => $item['nama_pengguna'] ?? 'Pengguna Shopee',
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

            // Hitung metrik evaluasi
            $metrics = $pipeline->calculateMetrics($evaluationPairs);

            $this->command->info(sprintf(
                "Evaluasi Selesai -> Total: %d, Akurasi: %.2f%%, Presisi: %.2f%%, Recall: %.2f%%, F1: %.2f%%",
                count($validRows),
                $metrics['accuracy'] * 100,
                $metrics['precision'] * 100,
                $metrics['recall'] * 100,
                $metrics['f1'] * 100
            ));

            // Simpan Evaluation Header
            $evaluation = Evaluation::create([
                'user_id' => $admin->id,
                'judul_dataset' => 'Dataset Ulasan ShopeeFood (Validasi Final)',
                'file_name' => 'KANDIDAT_VALIDASI_SHOPEEFOOD_FINAL.xlsx',
                'total_ulasan' => count($validRows),
                'total_positif' => $positifCount,
                'total_negatif' => $negatifCount,
                'akurasi' => $metrics['accuracy'],
                'presisi' => $metrics['precision'],
                'recall' => $metrics['recall'],
                'f1_score' => $metrics['f1'],
            ]);

            // Bulk Insert Prediction Results in chunks of 500
            $chunks = array_chunk($validRows, 500);
            foreach ($chunks as $chunk) {
                foreach ($chunk as &$row) {
                    $row['evaluation_id'] = $evaluation->id;
                }
                PredictionResult::insert($chunk);
            }

            $this->command->info("Seeding data riwayat evaluasi berhasil disimpan ke MySQL!");
        }
    }
}
