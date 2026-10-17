<?php

namespace Tests\Feature;

use App\Mail\ResetPasswordOtpMail;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailOtpPasswordResetTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_can_view_email_otp_request_page(): void
    {
        $response = $this->get(route('password.email_otp.request'));

        $response->assertOk()
            ->assertSee('Khôi phục mật khẩu qua Email');
    }

    public function test_submitting_registered_email_sends_mail_and_redirects_to_verify(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'receiver@example.com',
            'name' => 'Người Thử Nghiệm',
        ]);

        $response = $this->post(route('password.email_otp.request.post'), [
            'email' => 'receiver@example.com',
        ]);

        $response->assertRedirect(route('password.email_otp.verify'));
        $response->assertSessionHas('email_otp_data');

        Mail::assertSent(ResetPasswordOtpMail::class, function (ResetPasswordOtpMail $mail) use ($user) {
            return $mail->hasTo($user->email) && strlen($mail->otpCode) === 6;
        });
    }

    public function test_submitting_unregistered_email_fails_validation(): void
    {
        $response = $this->post(route('password.email_otp.request.post'), [
            'email' => 'notfound@example.com',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_cannot_access_verify_page_without_session_data(): void
    {
        $response = $this->get(route('password.email_otp.verify'));

        $response->assertRedirect(route('password.email_otp.request'));
    }

    public function test_can_view_verify_page_with_valid_session_data(): void
    {
        $user = User::factory()->create([
            'email' => 'receiver@example.com',
        ]);

        $sessionData = [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'code' => '123456',
            'expires_at' => now()->addMinutes(10)->timestamp,
            'attempts' => 0,
            'last_sent_at' => now()->timestamp,
        ];

        $response = $this->withSession(['email_otp_data' => $sessionData])
            ->get(route('password.email_otp.verify'));

        $response->assertOk()
            ->assertSee('Xác thực mã OTP')
            ->assertSee('receiver@example.com');
    }

    public function test_submitting_wrong_otp_returns_error_and_increments_attempts(): void
    {
        $user = User::factory()->create();

        $sessionData = [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'code' => '654321',
            'expires_at' => now()->addMinutes(10)->timestamp,
            'attempts' => 0,
            'last_sent_at' => now()->timestamp,
        ];

        $response = $this->withSession(['email_otp_data' => $sessionData])
            ->post(route('password.email_otp.verify.post'), [
                'otp_code' => '111111',
            ]);

        $response->assertSessionHasErrors(['otp_code']);
        $this->assertSame(1, session('email_otp_data')['attempts']);
    }

    public function test_submitting_expired_otp_fails(): void
    {
        $user = User::factory()->create();

        $sessionData = [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'code' => '654321',
            'expires_at' => now()->subMinute()->timestamp,
            'attempts' => 0,
            'last_sent_at' => now()->subMinutes(11)->timestamp,
        ];

        $response = $this->withSession(['email_otp_data' => $sessionData])
            ->post(route('password.email_otp.verify.post'), [
                'otp_code' => '654321',
            ]);

        $response->assertSessionHasErrors(['otp_code']);
    }

    public function test_submitting_valid_otp_authorizes_reset(): void
    {
        $user = User::factory()->create();

        $sessionData = [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'code' => '888999',
            'expires_at' => now()->addMinutes(10)->timestamp,
            'attempts' => 0,
            'last_sent_at' => now()->timestamp,
        ];

        $response = $this->withSession(['email_otp_data' => $sessionData])
            ->post(route('password.email_otp.verify.post'), [
                'otp_code' => '888999',
            ]);

        $response->assertRedirect(route('password.email_otp.reset'));
        $this->assertTrue(session('email_otp_verified'));
        $this->assertSame($user->id, session('email_otp_verified_user_id'));
    }

    public function test_cannot_resend_otp_email_before_60_seconds(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'receiver@example.com',
            'name' => 'Học Viên',
        ]);

        $sessionData = [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'code' => '123456',
            'expires_at' => now()->addMinutes(5)->timestamp,
            'attempts' => 1,
            'last_sent_at' => now()->subSeconds(30)->timestamp,
        ];

        $response = $this->withSession(['email_otp_data' => $sessionData])
            ->post(route('password.email_otp.resend'));

        $response->assertSessionHasErrors(['otp_code']);
        Mail::assertNothingSent();
    }

    public function test_can_resend_otp_email(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'receiver@example.com',
            'name' => 'Học Viên',
        ]);

        $sessionData = [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'code' => '123456',
            'expires_at' => now()->addMinutes(5)->timestamp,
            'attempts' => 1,
            'last_sent_at' => now()->subSeconds(65)->timestamp,
        ];

        $response = $this->withSession(['email_otp_data' => $sessionData])
            ->post(route('password.email_otp.resend'));

        $response->assertRedirect();
        Mail::assertSent(ResetPasswordOtpMail::class);
    }

    public function test_reset_password_updates_database_and_authenticates_user(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword123'),
        ]);

        $response = $this->withSession([
            'email_otp_verified' => true,
            'email_otp_verified_user_id' => $user->id,
        ])->post(route('password.email_otp.reset.post'), [
            'password' => 'newpassword888',
            'password_confirmation' => 'newpassword888',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Hash::check('newpassword888', $user->fresh()->password));
        $this->assertNull(session('email_otp_verified'));
    }
}
