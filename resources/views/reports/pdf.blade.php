<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Analisis Sentimen ShopeeFood</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            color: #111;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #ee4d2d;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .title {
            font-size: 14pt;
            font-weight: bold;
            color: #ee4d2d;
            margin: 0;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 11pt;
            font-weight: bold;
            color: #333;
            margin: 4px 0 0 0;
            text-transform: uppercase;
        }
        .meta {
            font-size: 9pt;
            color: #666;
            margin-top: 6px;
        }
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            color: #111;
            border-left: 4px solid #ee4d2d;
            padding-left: 8px;
            margin: 20px 0 10px 0;
        }
        .metrics-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .metrics-table th, .metrics-table td {
            border: 1px solid #ddd;
            padding: 8px 12px;
            font-size: 10pt;
        }
        .metrics-table th {
            background-color: #f7f7f7;
            text-align: left;
            width: 35%;
        }
        .metrics-table td {
            font-weight: bold;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th, .data-table td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            font-size: 8.5pt;
            text-align: left;
        }
        .data-table th {
            background-color: #eee;
            font-weight: bold;
        }
        .badge-pos {
            color: #047857;
            font-weight: bold;
        }
        .badge-neg {
            color: #b91c1c;
            font-weight: bold;
        }
        .star-icon {
            width: 10px;
            height: 10px;
            vertical-align: -1px;
            margin-right: 3px;
        }
        .rating-badge {
            white-space: nowrap;
            color: #b45309;
            font-weight: bold;
            font-size: 8.5pt;
        }
        .footer {
            margin-top: 30px;
            font-size: 9pt;
            color: #777;
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1 class="title">LAPORAN HASIL ANALISIS SENTIMEN KELUHAN PENGGUNA</h1>
        <h2 class="subtitle">FITUR PESAN ANTAR MAKANAN APLIKASI SHOPEE</h2>
        <div class="meta">
            Metode: <strong>Multinomial Naive Bayes & TF-IDF</strong> • Dicetak pada: {{ $printedAt }}
        </div>
    </div>

    <!-- Informasi Dataset -->
    <div class="section-title">1. Informasi & Rekapitulasi Dataset</div>
    <table class="metrics-table">
        <tr>
            <th>Judul Dataset</th>
            <td>{{ $evaluation->judul_dataset }}</td>
        </tr>
        <tr>
            <th>Nama Berkas Asli</th>
            <td>{{ $evaluation->file_name ?? '-' }}</td>
        </tr>
        <tr>
            <th>Total Ulasan Dianalisis</th>
            <td>{{ number_format($evaluation->total_ulasan) }} Ulasan</td>
        </tr>
        <tr>
            <th>Distribusi Sentimen Terprediksi</th>
            <td>
                <span class="badge-pos">Positif: {{ $evaluation->total_positif }}</span> |
                <span class="badge-neg">Negatif: {{ $evaluation->total_negatif }}</span>
            </td>
        </tr>
    </table>

    <!-- Performa Model -->
    <div class="section-title">2. Evaluasi Metrik Performa Model Naive Bayes</div>
    <table class="metrics-table">
        <tr>
            <th>Akurasi (Accuracy)</th>
            <td>{{ number_format($evaluation->akurasi * 100, 2) }} %</td>
        </tr>
        <tr>
            <th>Presisi (Precision)</th>
            <td>{{ number_format($evaluation->presisi * 100, 2) }} %</td>
        </tr>
        <tr>
            <th>Recall / Sensitivitas</th>
            <td>{{ number_format($evaluation->recall * 100, 2) }} %</td>
        </tr>
        <tr>
            <th>F1-Score</th>
            <td>{{ number_format($evaluation->f1_score * 100, 2) }} %</td>
        </tr>
    </table>

    <!-- Sampel Ulasan -->
    <div class="section-title">3. Sampel Data Hasil Prediksi Ulasan (50 Baris Pertama)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 16%;">Pengguna</th>
                <th style="width: 8%;">Rating</th>
                <th style="width: 44%;">Isi Ulasan Asli</th>
                <th style="width: 14%;">Sentimen Aktual</th>
                <th style="width: 14%;">Prediksi Model</th>
            </tr>
        </thead>
        <tbody>
            @foreach($evaluation->predictionResults as $idx => $row)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ Str::limit($row->nama_pengguna ?? 'Pengguna', 18) }}</td>
                    <td>
                        @if(!empty($row->rating))
                            <span class="rating-badge">
                                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAqElEQVR4nMWW0Q7DIAhFB+kT20/6Y+tPdry62DSLddoi9qYkvpjce0CgKcUYH8jgXoHOrwgDaKd5N8ADYmT27gp6gIzMfqgCK5iR2aegz/sJ3TSWsBDKXMJC6xMhILJ5/npwJUQyr12Tr4BI4fE3RSMQqWirY+qBSEMztGiWuAegju3VhgZeweRtpBqrnLwTItvdGYjKv4pcYB3XQ00C5Cd9Xcs766lpv01yi99/On2EAAAAAElFTkSuQmCC" class="star-icon" alt="★">{{ $row->rating }}
                            </span>
                        @else
                            <span style="color: #999;">-</span>
                        @endif
                    </td>
                    <td>{{ Str::limit($row->ulasan_asli, 80) }}</td>
                    <td>{{ $row->sentimen_aktual ?? '-' }}</td>
                    <td class="{{ $row->sentimen_prediksi === 'Positif' ? 'badge-pos' : 'badge-neg' }}">
                        {{ $row->sentimen_prediksi }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Laporan ini dihasilkan secara otomatis oleh Sistem Analisis Sentimen Shopee Laravel 12.</p>
    </div>

</body>
</html>
