<?php

namespace Tests\Feature;

use App\Mail\RegistrationOtpMail;
use App\Models\User;
use App\Services\GoogleAuthenticatorService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk()
            ->assertSee('Đăng ký tài khoản độc giả')
            ->assertSee('so_dien_thoai')
            ->assertSee('ngay_sinh')
            ->assertSee('dia_chi');
    }

    public function test_submitting_registration_form_sends_email_otp_and_redirects_to_verify(): void
    {
        Mail::fake();

        $response = $this->post(route('register'), [
            'name' => 'Trần Văn Mới',
            'email' => 'tranvanmoi@example.com',
            'so_dien_thoai' => '0912345678',
            'ngay_sinh' => '2000-01-15',
            'dia_chi' => '123 Đường Cộng Hòa, Q. Tân Bình, TP. HCM',
            'password' => 'matkhau123',
            'password_confirmation' => 'matkhau123',
        ]);

        $response->assertRedirect(route('register.verify'));
        $response->assertSessionHas('pending_registration');

        Mail::assertSent(RegistrationOtpMail::class, function (RegistrationOtpMail $mail) {
            return $mail->hasTo('tranvanmoi@example.com') && strlen($mail->otpCode) === 6;
        });
    }

    public function test_cannot_access_verify_screen_without_pending_registration(): void
    {
        $response = $this->get(route('register.verify'));

        $response->assertRedirect(route('register'));
    }

    public function test_can_view_verify_screen_with_pending_registration(): void
    {
        $sessionData = [
            'name' => 'Trần Văn Mới',
            'email' => 'tranvanmoi@example.com',
            'so_dien_thoai' => '0912345678',
            'ngay_sinh' => '2000-01-15',
            'dia_chi' => 'TP. HCM',
            'password' => bcrypt('matkhau123'),
            'email_otp' => '123456',
            'email_otp_expires_at' => now()->addMinutes(10)->timestamp,
            'email_otp_attempts' => 0,
            'google2fa_secret' => 'JBSWY3DPEHPK3PXP',
        ];

        $response = $this->withSession(['pending_registration' => $sessionData])
            ->get(route('register.verify'));

        $response->assertOk()
            ->assertSee('Xác thực đăng ký tài khoản')
            ->assertSee('tranvanmoi@example.com');
    }

    public function test_verifying_with_valid_email_otp_creates_user_and_authenticates(): void
    {
        $sessionData = [
            'name' => 'Trần Văn Mới',
            'email' => 'tranvanmoi@example.com',
            'so_dien_thoai' => '0912345678',
            'ngay_sinh' => '2000-01-15',
            'dia_chi' => '123 Đường Cộng Hòa, Q. Tân Bình, TP. HCM',
            'password' => bcrypt('matkhau123'),
            'email_otp' => '654321',
            'email_otp_expires_at' => now()->addMinutes(10)->timestamp,
            'email_otp_attempts' => 0,
            'google2fa_secret' => 'JBSWY3DPEHPK3PXP',
        ];

        $response = $this->withSession(['pending_registration' => $sessionData])
            ->post(route('register.verify.post'), [
                'method' => 'email',
                'otp_code' => '654321',
            ]);

        $response->assertRedirect('/home');
        $this->assertAuthenticated();

        $user = User::query()->where('email', 'tranvanmoi@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('Trần Văn Mới', $user->name);
        $this->assertSame('0912345678', $user->so_dien_thoai);
        $this->assertSame('123 Đường Cộng Hòa, Q. Tân Bình, TP. HCM', $user->dia_chi);
        $this->assertSame('2000-01-15', $user->ngay_sinh?->format('Y-m-d'));
        $this->assertSame('user', $user->role);
        $this->assertNull($user->google2fa_secret);
    }

    public function test_verifying_with_valid_google_authenticator_otp_activates_2fa(): void
    {
        $gaService = new GoogleAuthenticatorService;
        $secret = 'JBSWY3DPEHPK3PXP';
        $validCode = $gaService->getCode($secret);

        $sessionData = [
            'name' => 'Lê Thị Bảo Mật',
            'email' => 'lethibaomat@example.com',
            'so_dien_thoai' => '0934567890',
            'ngay_sinh' => '1999-09-09',
            'dia_chi' => 'Hà Nội',
            'password' => bcrypt('matkhau123'),
            'email_otp' => '111222',
            'email_otp_expires_at' => now()->addMinutes(10)->timestamp,
            'email_otp_attempts' => 0,
            'google2fa_secret' => $secret,
        ];

        $response = $this->withSession(['pending_registration' => $sessionData])
            ->post(route('register.verify.post'), [
                'method' => 'google',
                'otp_code' => $validCode,
            ]);

        $response->assertRedirect('/home');
        $this->assertAuthenticated();

        $user = User::query()->where('email', 'lethibaomat@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame($secret, $user->google2fa_secret);
        $this->assertTrue($user->hasGoogle2Fa());
    }

    public function test_verifying_with_wrong_email_otp_fails_and_increments_attempts(): void
    {
        $sessionData = [
            'name' => 'Trần Văn Mới',
            'email' => 'tranvanmoi@example.com',
            'password' => bcrypt('matkhau123'),
            'email_otp' => '654321',
            'email_otp_expires_at' => now()->addMinutes(10)->timestamp,
            'email_otp_attempts' => 0,
            'google2fa_secret' => 'JBSWY3DPEHPK3PXP',
        ];

        $response = $this->withSession(['pending_registration' => $sessionData])
            ->post(route('register.verify.post'), [
                'method' => 'email',
                'otp_code' => '000000',
            ]);

        $response->assertSessionHasErrors(['otp_code']);
        $this->assertSame(1, session('pending_registration')['email_otp_attempts']);
        $this->assertGuest();
    }

    public function test_can_resend_registration_email_otp(): void
    {
        Mail::fake();

        $sessionData = [
            'name' => 'Trần Văn Mới',
            'email' => 'tranvanmoi@example.com',
            'password' => bcrypt('matkhau123'),
            'email_otp' => '111111',
            'email_otp_expires_at' => now()->addMinutes(5)->timestamp,
            'email_otp_attempts' => 2,
            'email_otp_last_sent_at' => now()->subSeconds(65)->timestamp,
            'google2fa_secret' => 'JBSWY3DPEHPK3PXP',
        ];

        $response = $this->withSession(['pending_registration' => $sessionData])
            ->post(route('register.resend'));

        $response->assertRedirect();
        Mail::assertSent(RegistrationOtpMail::class);
    }

    public function test_registration_fails_with_invalid_phone_number(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Trần Văn Mới',
            'email' => 'tranvanmoi@example.com',
            'so_dien_thoai' => '12345',
            'password' => 'matkhau123',
            'password_confirmation' => 'matkhau123',
        ]);

        $response->assertSessionHasErrors(['so_dien_thoai']);
        $this->assertGuest();
    }

    public function test_registration_fails_with_future_birth_date(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Trần Văn Mới',
            'email' => 'tranvanmoi@example.com',
            'ngay_sinh' => now()->addDay()->format('Y-m-d'),
            'password' => 'matkhau123',
            'password_confirmation' => 'matkhau123',
        ]);

        $response->assertSessionHasErrors(['ngay_sinh']);
        $this->assertGuest();
    }

    public function test_registration_fails_with_mismatched_password(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Trần Văn Mới',
            'email' => 'tranvanmoi@example.com',
            'password' => 'matkhau123',
            'password_confirmation' => 'different123',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    public function test_registration_cannot_resend_otp_before_60_seconds(): void
    {
        Mail::fake();

        $sessionData = [
            'name' => 'Người Dùng Test',
            'email' => 'testresend@example.com',
            'so_dien_thoai' => '0912345678',
            'ngay_sinh' => '1995-05-05',
            'dia_chi' => 'TP. HCM',
            'password' => bcrypt('password123'),
            'email_otp' => '123456',
            'email_otp_expires_at' => now()->addMinutes(10)->timestamp,
            'email_otp_attempts' => 0,
            'email_otp_last_sent_at' => now()->subSeconds(25)->timestamp,
            'google2fa_secret' => 'JBSWY3DPEHPK3PXP',
        ];

        $response = $this->withSession(['pending_registration' => $sessionData])
            ->post(route('register.resend'));

        $response->assertSessionHasErrors(['otp_code']);
        Mail::assertNothingSent();
    }

    public function test_registration_can_resend_otp_after_60_seconds(): void
    {
        Mail::fake();

        $sessionData = [
            'name' => 'Người Dùng Test',
            'email' => 'testresend@example.com',
            'so_dien_thoai' => '0912345678',
            'ngay_sinh' => '1995-05-05',
            'dia_chi' => 'TP. HCM',
            'password' => bcrypt('password123'),
            'email_otp' => '123456',
            'email_otp_expires_at' => now()->addMinutes(10)->timestamp,
            'email_otp_attempts' => 0,
            'email_otp_last_sent_at' => now()->subSeconds(65)->timestamp,
            'google2fa_secret' => 'JBSWY3DPEHPK3PXP',
        ];

        $response = $this->withSession(['pending_registration' => $sessionData])
            ->post(route('register.resend'));

        $response->assertRedirect();
        Mail::assertSent(RegistrationOtpMail::class);
    }
}
