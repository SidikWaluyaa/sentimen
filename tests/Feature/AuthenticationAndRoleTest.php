<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationAndRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Shopee Sentimen');
    }

    public function test_admin_can_access_dashboard_and_user_management()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);

        $userMgmtResponse = $this->actingAs($admin)->get('/admin/users');
        $userMgmtResponse->assertStatus(200);
    }

    public function test_analyst_cannot_access_user_management()
    {
        $analyst = User::factory()->create(['role' => 'analyst']);

        $response = $this->actingAs($analyst)->get('/admin/users');
        $response->assertStatus(403);
    }

    public function test_single_prediction_tester_accessible_by_authenticated_user()
    {
        $user = User::factory()->create(['role' => 'analyst']);

        $response = $this->actingAs($user)->get('/prediction/single');
        $response->assertStatus(200);

        $postResponse = $this->actingAs($user)->post('/prediction/single', [
            'ulasan' => 'Makanan enak sekali dan driver sangat cepat mengantar pesanan'
        ]);

        $postResponse->assertStatus(200);
        $postResponse->assertSee('Positif');
    }
}
