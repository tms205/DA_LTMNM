<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\GoogleAuthenticatorService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_cannot_view_profile(): void
    {
        $response = $this->get(route('profile.edit'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Nguyễn Độc Giả',
            'so_dien_thoai' => '0901234567',
            'dia_chi' => 'TP.HCM',
            'ngay_sinh' => '2000-01-01',
        ]);

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertOk()
            ->assertSee('Nguyễn Độc Giả')
            ->assertSee('0901234567')
            ->assertSee('TP.HCM');
    }

    public function test_user_can_update_profile_info(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Tên Mới Sau Cập Nhật',
            'so_dien_thoai' => '0987654321',
            'dia_chi' => 'Quận 1, TP. Hồ Chí Minh',
            'ngay_sinh' => '1998-05-12',
        ]);

        $response->assertRedirect(route('profile.edit', ['tab' => 'info']));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Tên Mới Sau Cập Nhật',
            'so_dien_thoai' => '0987654321',
            'dia_chi' => 'Quận 1, TP. Hồ Chí Minh',
        ]);
        $this->assertSame('1998-05-12', $user->fresh()->ngay_sinh?->format('Y-m-d'));
    }

    public function test_profile_update_validates_vietnamese_phone_number(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Tên Hợp Lệ',
            'so_dien_thoai' => '123456', // Invalid phone
        ]);

        $response->assertSessionHasErrors(['so_dien_thoai']);
    }

    public function test_user_can_change_password_with_correct_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password-123'),
        ]);

        $response = $this->actingAs($user)->put(route('profile.updatePassword'), [
            'current_password' => 'old-password-123',
            'password' => 'new-password-456',
            'password_confirmation' => 'new-password-456',
        ]);

        $response->assertRedirect(route('profile.edit', ['tab' => 'password']))
            ->assertSessionHas('success');

        $this->assertTrue(Hash::check('new-password-456', $user->fresh()->password));
    }

    public function test_user_cannot_change_password_with_wrong_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password-123'),
        ]);

        $response = $this->actingAs($user)->put(route('profile.updatePassword'), [
            'current_password' => 'wrong-current-password',
            'password' => 'new-password-456',
            'password_confirmation' => 'new-password-456',
        ]);

        $response->assertSessionHasErrors(['current_password']);
        $this->assertTrue(Hash::check('old-password-123', $user->fresh()->password));
    }

    public function test_user_can_activate_google_2fa_with_valid_otp(): void
    {
        $gaService = new GoogleAuthenticatorService;
        $secret = $gaService->generateSecret();
        $validCode = $gaService->getCode($secret);

        $user = User::factory()->create(['google2fa_secret' => null]);

        $response = $this->actingAs($user)
            ->withSession(['google2fa_pending_secret' => $secret])
            ->post(route('profile.toggleGoogle2fa'), [
                'otp_code' => $validCode,
            ]);

        $response->assertRedirect(route('profile.edit', ['tab' => '2fa']))
            ->assertSessionHas('success')
            ->assertSessionMissing('google2fa_pending_secret');

        $this->assertSame($secret, $user->fresh()->google2fa_secret);
    }

    public function test_user_cannot_activate_google_2fa_with_invalid_otp(): void
    {
        $gaService = new GoogleAuthenticatorService;
        $secret = $gaService->generateSecret();

        $user = User::factory()->create(['google2fa_secret' => null]);

        $response = $this->actingAs($user)
            ->withSession(['google2fa_pending_secret' => $secret])
            ->post(route('profile.toggleGoogle2fa'), [
                'otp_code' => '000000',
            ]);

        $response->assertSessionHasErrors(['otp_code']);
        $this->assertNull($user->fresh()->google2fa_secret);
    }

    public function test_pending_google_2fa_secret_remains_same_across_profile_reload(): void
    {
        $user = User::factory()->create(['google2fa_secret' => null]);

        $firstResponse = $this->actingAs($user)->get(route('profile.edit', ['tab' => '2fa']));
        $firstSecret = $firstResponse->viewData('secret');
        $secondResponse = $this->get(route('profile.edit', ['tab' => '2fa']));

        $this->assertIsString($firstSecret);
        $this->assertSame($firstSecret, $secondResponse->viewData('secret'));
        $secondResponse->assertSessionHas('google2fa_pending_secret', $firstSecret);
    }

    public function test_user_cannot_activate_google_2fa_with_a_tampered_secret(): void
    {
        $gaService = new GoogleAuthenticatorService;
        $pendingSecret = $gaService->generateSecret();
        $tamperedSecret = $gaService->generateSecret();
        $tamperedCode = $gaService->getCode($tamperedSecret);
        $user = User::factory()->create(['google2fa_secret' => null]);

        $response = $this->actingAs($user)
            ->withSession(['google2fa_pending_secret' => $pendingSecret])
            ->post(route('profile.toggleGoogle2fa'), [
                'secret' => $tamperedSecret,
                'otp_code' => $tamperedCode,
            ]);

        $response->assertSessionHasErrors(['otp_code'])
            ->assertSessionHas('google2fa_pending_secret', $pendingSecret);
        $this->assertNull($user->fresh()->google2fa_secret);
    }

    public function test_active_google_2fa_does_not_render_secret_or_current_otp(): void
    {
        $gaService = new GoogleAuthenticatorService;
        $secret = $gaService->generateSecret();
        $currentOtp = $gaService->getCode($secret);
        $user = User::factory()->create(['google2fa_secret' => $secret]);

        $response = $this->actingAs($user)->get(route('profile.edit', ['tab' => '2fa']));

        $response->assertOk()
            ->assertDontSeeText($secret)
            ->assertDontSeeText($currentOtp);
    }

    public function test_user_cannot_disable_google_2fa_without_current_credentials(): void
    {
        $secret = (new GoogleAuthenticatorService)->generateSecret();
        $user = User::factory()->create([
            'password' => Hash::make('current-password-123'),
            'google2fa_secret' => $secret,
        ]);

        $response = $this->actingAs($user)->post(route('profile.toggleGoogle2fa'), [
            'action' => 'disable',
        ]);

        $response->assertSessionHasErrors(['current_password', 'otp_code']);
        $this->assertSame($secret, $user->fresh()->google2fa_secret);
    }

    public function test_user_can_disable_google_2fa_with_current_password_and_otp(): void
    {
        $gaService = new GoogleAuthenticatorService;
        $secret = $gaService->generateSecret();
        $user = User::factory()->create([
            'password' => Hash::make('current-password-123'),
            'google2fa_secret' => $secret,
        ]);

        $response = $this->actingAs($user)->post(route('profile.toggleGoogle2fa'), [
            'action' => 'disable',
            'current_password' => 'current-password-123',
            'otp_code' => $gaService->getCode($secret),
        ]);

        $response->assertRedirect(route('profile.edit', ['tab' => '2fa']))
            ->assertSessionHas('info');
        $this->assertNull($user->fresh()->google2fa_secret);
    }
}
