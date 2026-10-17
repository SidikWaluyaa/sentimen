<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\PredictionResult;
use Illuminate\Http\Request;

class DashboardController
{
    public function index(Request $request)
    {
        $evaluations = Evaluation::with('user')->latest()->get();

        $selectedEvaluationId = $request->query('evaluation_id');
        if ($selectedEvaluationId) {
            $evaluation = Evaluation::find($selectedEvaluationId);
        } else {
            $evaluation = $evaluations->first();
        }

        if (!$evaluation) {
            return view('dashboard', [
                'evaluations' => collect([]),
                'evaluation' => null,
                'predictions' => collect([]),
                'topNegativeWords' => [],
                'filterSentiment' => 'all',
                'searchQuery' => '',
            ]);
        }

        // Query Predictions for selected evaluation
        $query = PredictionResult::where('evaluation_id', $evaluation->id);

        $filterSentiment = $request->query('sentiment', 'all');
        if ($filterSentiment === 'Positif' || $filterSentiment === 'Negatif') {
            $query->where('sentimen_prediksi', $filterSentiment);
        }

        $searchQuery = $request->query('search', '');
        if (!empty($searchQuery)) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('ulasan_asli', 'like', "%{$searchQuery}%")
                  ->orWhere('nama_pengguna', 'like', "%{$searchQuery}%");
            });
        }

        $predictions = $query->latest('id')->paginate(15)->withQueryString();

        // Extract Top Negative Keywords
        $negReviews = PredictionResult::where('evaluation_id', $evaluation->id)
            ->where('sentimen_prediksi', 'Negatif')
            ->pluck('ulasan_bersih');

        $wordCounts = [];
        foreach ($negReviews as $cleanText) {
            $words = explode(' ', $cleanText);
            foreach ($words as $w) {
                $w = trim($w);
                if (strlen($w) > 2) {
                    $wordCounts[$w] = ($wordCounts[$w] ?? 0) + 1;
                }
            }
        }
        arsort($wordCounts);
        $topNegativeWords = array_slice($wordCounts, 0, 15, true);

        return view('dashboard', [
            'evaluations' => $evaluations,
            'evaluation' => $evaluation,
            'predictions' => $predictions,
            'topNegativeWords' => $topNegativeWords,
            'filterSentiment' => $filterSentiment,
            'searchQuery' => $searchQuery,
        ]);
    }
}
