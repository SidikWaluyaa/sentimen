<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'judul_dataset',
        'file_name',
        'total_ulasan',
        'total_positif',
        'total_negatif',
        'akurasi',
        'presisi',
        'recall',
        'f1_score',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function predictionResults()
    {
        return $this->hasMany(PredictionResult::class);
    }
}
