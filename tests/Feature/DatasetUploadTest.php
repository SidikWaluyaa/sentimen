<?php

namespace Tests\Feature;

use App\Models\Evaluation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class DatasetUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_analyst_can_upload_csv_dataset_and_calculate_metrics()
    {
        $user = User::factory()->create(['role' => 'analyst']);

        // Create a temporary mock CSV
        $csvContent = "ulasan,rating\n";
        $csvContent .= "makanan sangat enak driver ramah,5\n";
        $csvContent .= "pesanan dibatalkan sepihak driver tidak mau antar rugi,1\n";
        $csvContent .= "biasa saja,3\n"; // Should be filtered out (rating 3)

        $file = UploadedFile::fake()->createWithContent('test_reviews.csv', $csvContent);

        $response = $this->actingAs($user)->post('/datasets/upload', [
            'judul_dataset' => 'Test Batch Evaluation',
            'file_dataset' => $file,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('evaluations', [
            'judul_dataset' => 'Test Batch Evaluation',
            'total_ulasan' => 2, // 3 minus 1 neutral rating
        ]);

        $eval = Evaluation::first();
        $this->assertNotNull($eval);
        $this->assertEquals(2, $eval->predictionResults()->count());
    }
}
