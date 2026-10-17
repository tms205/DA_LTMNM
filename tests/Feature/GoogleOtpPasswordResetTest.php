<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\GoogleAuthenticatorService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GoogleOtpPasswordResetTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_can_view_google_otp_request_page(): void
    {
        $response = $this->get(route('password.google.request'));
        $response->assertOk()
            ->assertSee('Khôi phục mật khẩu qua OTP');
    }

    public function test_submitting_registered_email_stores_session_and_redirects_to_verify(): void
    {
        $user = User::factory()->create([
            'email' => 'testuser@gmail.com',
            'google2fa_secret' => 'JBSWY3DPEHPK3PXP',
        ]);

        $response = $this->post(route('password.google.request.post'), [
            'email' => 'testuser@gmail.com',
        ]);

        $response->assertRedirect(route('password.google.verify'))
            ->assertSessionHas('google_otp_reset_user_id', $user->id);
    }

    public function test_submitting_non_existent_email_fails_validation(): void
    {
        $response = $this->post(route('password.google.request.post'), [
            'email' => 'nonexistent@gmail.com',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_can_view_verify_page_when_session_is_present(): void
    {
        $secret = 'JBSWY3DPEHPK3PXP';
        $user = User::factory()->create([
            'email' => 'testuser@gmail.com',
            'google2fa_secret' => $secret,
        ]);

        $response = $this->withSession(['google_otp_reset_user_id' => $user->id])
            ->get(route('password.google.verify'));

        $response->assertOk()
            ->assertSee('Xác thực mã bảo mật OTP')
            ->assertSee('testuser@gmail.com')
            ->assertDontSeeText($secret)
            ->assertDontSee('api.qrserver.com');
    }

    public function test_user_without_google_2fa_cannot_start_google_otp_reset(): void
    {
        $user = User::factory()->create([
            'email' => 'without2fa@gmail.com',
            'google2fa_secret' => null,
        ]);

        $response = $this->post(route('password.google.request.post'), [
            'email' => $user->email,
        ]);

        $response->assertSessionHasErrors(['email'])
            ->assertSessionMissing('google_otp_reset_user_id');
        $this->assertNull($user->fresh()->google2fa_secret);
    }

    public function test_verify_page_redirects_to_request_if_session_is_missing(): void
    {
        $response = $this->get(route('password.google.verify'));
        $response->assertRedirect(route('password.google.request'));
    }

    public function test_verification_stops_if_google_2fa_was_disabled_after_starting_reset(): void
    {
        $user = User::factory()->create(['google2fa_secret' => null]);

        $response = $this->withSession(['google_otp_reset_user_id' => $user->id])
            ->post(route('password.google.verify.post'), [
                'otp_code' => '123456',
            ]);

        $response->assertRedirect(route('password.google.request'))
            ->assertSessionHasErrors(['email'])
            ->assertSessionMissing('google_otp_reset_user_id');
    }

    public function test_verifying_valid_google_otp_promotes_session_to_reset_form(): void
    {
        $gaService = new GoogleAuthenticatorService;
        $secret = $gaService->generateSecret();
        $code = $gaService->getCode($secret);

        $user = User::factory()->create([
            'google2fa_secret' => $secret,
        ]);

        $response = $this->withSession(['google_otp_reset_user_id' => $user->id])
            ->post(route('password.google.verify.post'), [
                'otp_code' => $code,
            ]);

        $response->assertRedirect(route('password.google.reset'))
            ->assertSessionHas('google_otp_verified_user_id', $user->id)
            ->assertSessionMissing('google_otp_reset_user_id');
    }

    public function test_verifying_wrong_otp_returns_error(): void
    {
        $gaService = new GoogleAuthenticatorService;
        $secret = $gaService->generateSecret();

        $user = User::factory()->create([
            'google2fa_secret' => $secret,
        ]);

        $response = $this->withSession(['google_otp_reset_user_id' => $user->id])
            ->post(route('password.google.verify.post'), [
                'otp_code' => '999999',
            ]);

        $response->assertSessionHasErrors(['otp_code']);
    }

    public function test_google_otp_verification_is_rate_limited(): void
    {
        $user = User::factory()->create([
            'google2fa_secret' => (new GoogleAuthenticatorService)->generateSecret(),
        ]);

        for ($attempt = 0; $attempt < 6; $attempt++) {
            $this->withSession(['google_otp_reset_user_id' => $user->id])
                ->post(route('password.google.verify.post'), ['otp_code' => '999999'])
                ->assertSessionHasErrors(['otp_code']);
        }

        $this->withSession(['google_otp_reset_user_id' => $user->id])
            ->post(route('password.google.verify.post'), ['otp_code' => '999999'])
            ->assertTooManyRequests();
    }

    public function test_can_reset_password_after_otp_verification(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('initial-pass-123'),
        ]);

        $response = $this->withSession(['google_otp_verified_user_id' => $user->id])
            ->post(route('password.google.reset.post'), [
                'password' => 'brand-new-secret-888',
                'password_confirmation' => 'brand-new-secret-888',
            ]);

        $response->assertRedirect(route('login'))
            ->assertSessionHas('success')
            ->assertSessionMissing('google_otp_verified_user_id');

        $this->assertTrue(Hash::check('brand-new-secret-888', $user->fresh()->password));
    }
}
