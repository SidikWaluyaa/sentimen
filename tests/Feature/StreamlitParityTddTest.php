<?php

namespace Tests\Feature;

use App\Services\NLP\SentimentPipeline;
use Tests\TestCase;

class StreamlitParityTddTest extends TestCase
{
    /**
     * TDD Test to verify 100% classification parity between Streamlit Python and Laravel Pure PHP.
     */
    public function test_pure_php_matches_streamlit_python_predictions_on_benchmark()
    {
        $benchmarkPath = base_path('tests/fixtures/streamlit_parity_benchmark.json');
        $this->assertFileExists($benchmarkPath, 'Benchmark ground-truth JSON must exist.');

        $benchmarkData = json_decode(file_get_contents($benchmarkPath), true);
        $this->assertNotEmpty($benchmarkData);

        $pipeline = new SentimentPipeline();

        $matchedCount = 0;
        $totalCount = count($benchmarkData);

        foreach ($benchmarkData as $index => $item) {
            $rawText = $item['raw_text'];
            $expectedPred = $item['expected_prediction'];
            $expectedProbaPos = $item['proba_positif'];
            $expectedProbaNeg = $item['proba_negatif'];

            $result = $pipeline->predictSingle($rawText);

            // Assert predicted sentiment matches Streamlit Python
            $this->assertEquals(
                $expectedPred,
                $result['prediction'],
                "Mismatch on sample #{$index}: '{$rawText}' - Expected {$expectedPred}, got {$result['prediction']}"
            );

            // Assert probability is in consistent direction
            if ($expectedPred === 'Positif') {
                $this->assertGreaterThan(
                    $result['probabilities']['Negatif'],
                    $result['probabilities']['Positif'],
                    "Probability direction mismatch for sample #{$index}"
                );
            } else {
                $this->assertGreaterThan(
                    $result['probabilities']['Positif'],
                    $result['probabilities']['Negatif'],
                    "Probability direction mismatch for sample #{$index}"
                );
            }

            $matchedCount++;
        }

        $this->assertEquals($totalCount, $matchedCount, "All {$totalCount} samples must match Streamlit predictions 1:1.");
    }
}
