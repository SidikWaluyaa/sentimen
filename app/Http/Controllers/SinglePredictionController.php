<?php

namespace App\Http\Controllers;

use App\Services\NLP\SentimentPipeline;
use Illuminate\Http\Request;

class SinglePredictionController
{
    public function index()
    {
        return view('prediction.single', [
            'result' => null,
            'inputUlasan' => '',
        ]);
    }

    public function predict(Request $request, SentimentPipeline $pipeline)
    {
        $request->validate([
            'ulasan' => 'required|string|min:3|max:1000',
        ], [
            'ulasan.required' => 'Teks ulasan tidak boleh kosong.',
            'ulasan.min' => 'Panjang ulasan minimal 3 karakter.',
            'ulasan.max' => 'Panjang ulasan maksimal 1.000 karakter.',
        ]);

        $text = $request->input('ulasan');
        $result = $pipeline->predictSingle($text);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        }

        return view('prediction.single', [
            'result' => $result,
            'inputUlasan' => $text,
        ]);
    }
}
