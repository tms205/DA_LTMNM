<?php

namespace Tests\Feature\Http\Middleware;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class EnsureUserHasRoleTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_unauthenticated_request_redirects_to_login(): void
    {
        $response = $this->get(route('admin.saches.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_role_is_forbidden_from_admin_routes(): void
    {
        $user = User::factory()->docGia()->create();

        $response = $this->actingAs($user)->get(route('admin.saches.index'));

        $response->assertForbidden();
    }

    public function test_admin_role_can_access_admin_routes(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.saches.index'));

        $response->assertOk()->assertViewIs('admin.saches.index');
    }

    public function test_registration_cannot_assign_admin_role(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Độc giả mới',
            'email' => 'reader@example.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('register.verify'));

        $this->post(route('register.verify.post'), [
            'method' => 'email',
            'otp_code' => session('pending_registration')['email_otp'],
        ])->assertRedirect(route('home'));

        $this->assertDatabaseHas('users', [
            'email' => 'reader@example.com',
            'role' => 'user',
        ]);
    }
}
