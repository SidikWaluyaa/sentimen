<?php

namespace App\Services\NLP;

use Illuminate\Support\Facades\Storage;
use RuntimeException;

class SentimentPipeline
{
    protected IndonesianTextPreprocessor $preprocessor;
    protected ?TfidfVectorizer $vectorizer = null;
    protected ?NaiveBayesClassifier $classifier = null;
    protected array $modelData = [];

    public function __construct()
    {
        $this->preprocessor = new IndonesianTextPreprocessor();
        $this->loadModel();
    }

    protected function loadModel(): void
    {
        $path = storage_path('app/ml/ml_model.json');
        if (!file_exists($path)) {
            // Check fallback in root
            if (file_exists(base_path('ml_model.json'))) {
                $path = base_path('ml_model.json');
            } else {
                throw new RuntimeException("Model weights file ml_model.json not found in storage/app/ml/ or project root.");
            }
        }

        $json = file_get_contents($path);
        $this->modelData = json_decode($json, true);

        if (!$this->modelData || !isset($this->modelData['vocabulary'], $this->modelData['idf'])) {
            throw new RuntimeException("Invalid model weights format in ml_model.json.");
        }

        $this->vectorizer = new TfidfVectorizer($this->modelData);
        $this->classifier = new NaiveBayesClassifier($this->modelData);
    }

    public function getPreprocessor(): IndonesianTextPreprocessor
    {
        return $this->preprocessor;
    }

    /**
     * Run prediction for a single review.
     */
    public function predictSingle(string $text): array
    {
        $cleaned = $this->preprocessor->preprocess($text);
        if ($cleaned === '') {
            return [
                'raw_text' => $text,
                'cleaned_text' => '',
                'prediction' => 'Negatif',
                'confidence' => 0.5,
                'probabilities' => ['Negatif' => 0.5, 'Positif' => 0.5],
            ];
        }

        $vector = $this->vectorizer->transform($cleaned);
        $result = $this->classifier->predict($vector);

        return [
            'raw_text' => $text,
            'cleaned_text' => $cleaned,
            'prediction' => $result['label'],
            'confidence' => $result['confidence'],
            'probabilities' => $result['probabilities'],
        ];
    }

    /**
     * Calculate evaluation metrics: Accuracy, Precision, Recall, F1-Score, and Confusion Matrix.
     */
    public function calculateMetrics(array $rows): array
    {
        // $rows format: array of ['actual' => 'Positif'|'Negatif', 'predicted' => 'Positif'|'Negatif']
        $tp = 0; // Actual Positif, Predicted Positif
        $fp = 0; // Actual Negatif, Predicted Positif
        $tn = 0; // Actual Negatif, Predicted Negatif
        $fn = 0; // Actual Positif, Predicted Negatif

        $total = count($rows);
        if ($total === 0) {
            return [
                'accuracy' => 0.0,
                'precision' => 0.0,
                'recall' => 0.0,
                'f1' => 0.0,
                'tp' => 0, 'fp' => 0, 'tn' => 0, 'fn' => 0,
            ];
        }

        foreach ($rows as $row) {
            $actual = $row['actual'];
            $predicted = $row['predicted'];

            if ($actual === 'Positif' && $predicted === 'Positif') {
                $tp++;
            } elseif ($actual === 'Negatif' && $predicted === 'Positif') {
                $fp++;
            } elseif ($actual === 'Negatif' && $predicted === 'Negatif') {
                $tn++;
            } elseif ($actual === 'Positif' && $predicted === 'Negatif') {
                $fn++;
            }
        }

        $accuracy = ($total > 0) ? ($tp + $tn) / $total : 0.0;
        $precision = ($tp + $fp > 0) ? $tp / ($tp + $fp) : 0.0;
        $recall = ($tp + $fn > 0) ? $tp / ($tp + $fn) : 0.0;
        $f1 = ($precision + $recall > 0) ? 2 * ($precision * $recall) / ($precision + $recall) : 0.0;

        return [
            'accuracy' => round($accuracy, 4),
            'precision' => round($precision, 4),
            'recall' => round($recall, 4),
            'f1' => round($f1, 4),
            'tp' => $tp,
            'fp' => $fp,
            'tn' => $tn,
            'fn' => $fn,
            'total' => $total,
        ];
    }
}
