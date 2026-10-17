<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PredictionResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id',
        'id_review',
        'nama_pengguna',
        'ulasan_asli',
        'ulasan_bersih',
        'rating',
        'sentimen_aktual',
        'sentimen_prediksi',
        'is_correct',
        'relevan_shopeefood',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'relevan_shopeefood' => 'boolean',
    ];

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }
}
