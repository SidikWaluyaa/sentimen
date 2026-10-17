<?php

namespace App\Services\NLP;

class TfidfVectorizer
{
    protected array $vocabulary = [];
    protected array $idf = [];
    protected string $norm = 'l2';

    public function __construct(array $modelData)
    {
        $this->vocabulary = $modelData['vocabulary'] ?? [];
        $this->idf = $modelData['idf'] ?? [];
        $this->norm = $modelData['norm'] ?? 'l2';
    }

    /**
     * Transform a preprocessed text into an L2-normalized TF-IDF sparse vector.
     * Returns an associative array of [feature_index => weight].
     */
    public function transform(string $text): array
    {
        if (trim($text) === '') {
            return [];
        }

        // Tokenize using word boundary
        preg_match_all('/\b[a-z]+\b/', mb_strtolower($text, 'UTF-8'), $matches);
        $tokens = $matches[0] ?? [];

        if (empty($tokens)) {
            return [];
        }

        // 1. Calculate Term Frequencies (TF)
        $termCounts = [];
        foreach ($tokens as $token) {
            if (isset($this->vocabulary[$token])) {
                $idx = (int)$this->vocabulary[$token];
                $termCounts[$idx] = ($termCounts[$idx] ?? 0) + 1;
            }
        }

        if (empty($termCounts)) {
            return [];
        }

        // 2. Multiply TF by IDF
        $tfidfVector = [];
        $sumSquares = 0.0;

        foreach ($termCounts as $idx => $tf) {
            $idfWeight = (float)($this->idf[$idx] ?? 1.0);
            $weight = $tf * $idfWeight;
            $tfidfVector[$idx] = $weight;
            $sumSquares += ($weight * $weight);
        }

        // 3. L2 Normalization
        if ($this->norm === 'l2' && $sumSquares > 0) {
            $l2Norm = sqrt($sumSquares);
            foreach ($tfidfVector as $idx => $val) {
                $tfidfVector[$idx] = $val / $l2Norm;
            }
        }

        return $tfidfVector;
    }

    public function getVocabulary(): array
    {
        return $this->vocabulary;
    }
}
