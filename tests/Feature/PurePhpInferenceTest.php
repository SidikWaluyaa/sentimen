<?php

namespace Tests\Feature;

use App\Services\NLP\SentimentPipeline;
use Tests\TestCase;

class PurePhpInferenceTest extends TestCase
{
    public function test_pure_php_inference_predicts_positive_sentiment()
    {
        $pipeline = new SentimentPipeline();
        $result = $pipeline->predictSingle('makanan sangat enak driver ramah dan cepat sampai');

        $this->assertEquals('Positif', $result['prediction']);
        $this->assertGreaterThan(0.5, $result['confidence']);
        $this->assertNotEmpty($result['cleaned_text']);
    }

    public function test_pure_php_inference_predicts_negative_sentiment()
    {
        $pipeline = new SentimentPipeline();
        $result = $pipeline->predictSingle('aplikasi jelek banget pesanan dibatalkan sepihak driver tidak mau antar rugi uang saya');

        $this->assertEquals('Negatif', $result['prediction']);
        $this->assertGreaterThan(0.5, $result['confidence']);
        $this->assertNotEmpty($result['cleaned_text']);
    }

    public function test_metrics_calculation_is_accurate()
    {
        $pipeline = new SentimentPipeline();

        $rows = [
            ['actual' => 'Positif', 'predicted' => 'Positif'], // TP
            ['actual' => 'Positif', 'predicted' => 'Positif'], // TP
            ['actual' => 'Negatif', 'predicted' => 'Negatif'], // TN
            ['actual' => 'Negatif', 'predicted' => 'Positif'], // FP
        ];

        $metrics = $pipeline->calculateMetrics($rows);

        // Accuracy: (2 TP + 1 TN) / 4 = 0.75
        $this->assertEquals(0.75, $metrics['accuracy']);
        // Precision: 2 TP / (2 TP + 1 FP) = 2/3 = 0.6667
        $this->assertEquals(0.6667, $metrics['precision']);
        // Recall: 2 TP / (2 TP + 0 FN) = 1.0
        $this->assertEquals(1.0, $metrics['recall']);
    }
}
