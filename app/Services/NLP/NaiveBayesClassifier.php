<?php

namespace App\Services\NLP;

class NaiveBayesClassifier
{
    protected array $classes = [];
    protected array $classLogPrior = [];
    protected array $featureLogProb = [];

    public function __construct(array $modelData)
    {
        $this->classes = $modelData['classes'] ?? ['Negatif', 'Positif'];
        $this->classLogPrior = $modelData['class_log_prior'] ?? [];
        $this->featureLogProb = $modelData['feature_log_prob'] ?? [];
    }

    /**
     * Predict the class and confidence probability from a sparse TF-IDF vector.
     *
     * @param array $tfidfVector Sparse vector [feature_index => weight]
     * @return array ['label' => string, 'confidence' => float, 'probabilities' => array]
     */
    public function predict(array $tfidfVector): array
    {
        $classScores = [];

        foreach ($this->classes as $cIdx => $className) {
            $prior = (float)($this->classLogPrior[$cIdx] ?? 0.0);
            $logLikelihood = 0.0;

            if (isset($this->featureLogProb[$cIdx])) {
                $featureWeights = $this->featureLogProb[$cIdx];
                foreach ($tfidfVector as $featureIdx => $weight) {
                    if (isset($featureWeights[$featureIdx])) {
                        $logLikelihood += ((float)$weight * (float)$featureWeights[$featureIdx]);
                    }
                }
            }

            $classScores[$className] = $prior + $logLikelihood;
        }

        // Find argmax
        arsort($classScores);
        $predictedLabel = array_key_first($classScores) ?? 'Negatif';

        // Calculate softmax probabilities
        $maxScore = max($classScores);
        $expScores = [];
        $sumExp = 0.0;

        foreach ($classScores as $label => $score) {
            $exp = exp($score - $maxScore);
            $expScores[$label] = $exp;
            $sumExp += $exp;
        }

        $probabilities = [];
        foreach ($expScores as $label => $exp) {
            $probabilities[$label] = $sumExp > 0 ? round($exp / $sumExp, 4) : 0.5;
        }

        return [
            'label' => $predictedLabel,
            'confidence' => $probabilities[$predictedLabel] ?? 0.5,
            'probabilities' => $probabilities,
            'scores' => $classScores,
        ];
    }
}
