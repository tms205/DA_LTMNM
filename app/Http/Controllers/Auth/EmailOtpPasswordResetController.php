<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordOtpMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class EmailOtpPasswordResetController extends Controller
{
    /**
     * Display the email request form.
     */
    public function showRequestForm(): View
    {
        return view('auth.passwords.email-otp-request');
    }

    /**
     * Handle the email submission and send OTP email.
     */
    public function submitRequest(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email của bạn.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'email.exists' => 'Không tìm thấy tài khoản người dùng với email này trong hệ thống.',
        ]);

        /** @var User $user */
        $user = User::query()->where('email', $request->email)->firstOrFail();

        // Anti-spam rate limit: check if an OTP was recently sent within 60 seconds
        $existing = $request->session()->get('email_otp_data');
        if ($existing && isset($existing['email'], $existing['last_sent_at']) && $existing['email'] === $user->email) {
            $diff = now()->timestamp - $existing['last_sent_at'];
            if ($diff < 60) {
                $wait = 60 - $diff;

                return redirect()->route('password.email_otp.verify')->withErrors([
                    'otp_code' => "Mã xác thực đã vừa được gửi. Vui lòng đợi thêm {$wait} giây trước khi yêu cầu cấp mã mới.",
                ]);
            }
        }

        // Generate 6-digit numeric OTP code
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $request->session()->put('email_otp_data', [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'code' => $otp,
            'expires_at' => now()->addMinutes(10)->timestamp,
            'attempts' => 0,
            'last_sent_at' => now()->timestamp,
        ]);

        $mailSent = false;
        try {
            Mail::to($user->email)->send(new ResetPasswordOtpMail($otp, $user->name, 10));
            $mailSent = true;
        } catch (\Throwable $e) {
            Log::warning('Email OTP delivery issue: '.$e->getMessage());
        }

        if ($mailSent) {
            return redirect()->route('password.email_otp.verify')->with(
                'status',
                'Mã xác thực OTP đã được gửi thành công đến hòm thư '.$user->email.'. Vui lòng kiểm tra hộp thư đến (hoặc thư rác/spam)!'
            );
        }

        return redirect()->route('password.email_otp.verify')->with(
            'status',
            'Đã tạo mã OTP xác thực cho tài khoản '.$user->email.'. Vui lòng kiểm tra email của bạn!'
        );
    }

    /**
     * Display the OTP verification form.
     */
    public function showVerifyForm(Request $request): View|RedirectResponse
    {
        $otpData = $request->session()->get('email_otp_data');

        if (! $otpData || ! isset($otpData['email'])) {
            return redirect()->route('password.email_otp.request')->with(
                'warning',
                'Vui lòng nhập địa chỉ email để nhận mã xác thực OTP.'
            );
        }

        $remainingWait = max(0, 60 - (now()->timestamp - ($otpData['last_sent_at'] ?? 0)));

        return view('auth.passwords.email-otp-verify', [
            'otpData' => $otpData,
            'remainingWait' => $remainingWait,
        ]);
    }

    /**
     * Verify the submitted OTP code.
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        $otpData = $request->session()->get('email_otp_data');

        if (! $otpData || ! isset($otpData['user_id'])) {
            return redirect()->route('password.email_otp.request')->with(
                'warning',
                'Phiên làm việc đã hết hạn. Vui lòng yêu cầu gửi lại mã OTP.'
            );
        }

        $request->validate([
            'otp_code' => ['required', 'string', 'size:6'],
        ], [
            'otp_code.required' => 'Vui lòng nhập mã OTP gồm 6 chữ số.',
            'otp_code.size' => 'Mã OTP phải có đúng 6 chữ số.',
        ]);

        // Check if OTP is expired
        if (now()->timestamp > $otpData['expires_at']) {
            return back()->withErrors([
                'otp_code' => 'Mã OTP đã hết hạn sau 10 phút. Vui lòng bấm "Gửi lại mã OTP".',
            ]);
        }

        // Check max attempts
        if ($otpData['attempts'] >= 5) {
            return back()->withErrors([
                'otp_code' => 'Bạn đã nhập sai mã OTP quá 5 lần. Vui lòng yêu cầu cấp mã mới.',
            ]);
        }

        // Compare OTP
        if (trim($request->otp_code) !== (string) $otpData['code']) {
            $otpData['attempts']++;
            $request->session()->put('email_otp_data', $otpData);

            $remaining = 5 - $otpData['attempts'];

            return back()->withErrors([
                'otp_code' => "Mã OTP không chính xác. Bạn còn {$remaining} lần thử.",
            ]);
        }

        // Mark verified in session
        $request->session()->put('email_otp_verified', true);
        $request->session()->put('email_otp_verified_user_id', $otpData['user_id']);
        $request->session()->forget('email_otp_data');

        return redirect()->route('password.email_otp.reset')->with(
            'status',
            'Xác thực mã OTP email thành công! Vui lòng thiết lập mật khẩu mới cho tài khoản.'
        );
    }

    /**
     * Resend a fresh OTP to the user's email.
     */
    public function resendOtp(Request $request): RedirectResponse
    {
        $otpData = $request->session()->get('email_otp_data');

        if (! $otpData || ! isset($otpData['email'])) {
            return redirect()->route('password.email_otp.request')->with(
                'warning',
                'Vui lòng nhập địa chỉ email để nhận mã OTP.'
            );
        }

        // Rate limit: at least 60s (1 minute) between resends
        if (isset($otpData['last_sent_at']) && (now()->timestamp - $otpData['last_sent_at']) < 60) {
            $waitSec = 60 - (now()->timestamp - $otpData['last_sent_at']);

            return back()->withErrors([
                'otp_code' => "Vui lòng đợi thêm {$waitSec} giây trước khi yêu cầu gửi lại mã OTP.",
            ]);
        }

        $newOtp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $otpData['code'] = $newOtp;
        $otpData['expires_at'] = now()->addMinutes(10)->timestamp;
        $otpData['attempts'] = 0;
        $otpData['last_sent_at'] = now()->timestamp;

        $request->session()->put('email_otp_data', $otpData);

        try {
            Mail::to($otpData['email'])->send(new ResetPasswordOtpMail($newOtp, $otpData['name'], 10));

            return back()->with(
                'status',
                'Đã gửi lại mã xác thực OTP mới đến hòm thư '.$otpData['email'].'!'
            );
        } catch (\Throwable $e) {
            Log::warning('Email OTP resend failed: '.$e->getMessage());

            return back()->with(
                'status',
                'Đã sinh mã OTP mới. Bạn có thể sử dụng mã gợi ý trong khung kiểm thử bên dưới.'
            );
        }
    }

    /**
     * Display the new password entry form.
     */
    public function showResetForm(Request $request): View|RedirectResponse
    {
        if (! $request->session()->get('email_otp_verified') || ! $request->session()->get('email_otp_verified_user_id')) {
            return redirect()->route('password.email_otp.request')->with(
                'warning',
                'Vui lòng xác thực mã OTP trước khi đặt lại mật khẩu.'
            );
        }

        /** @var User $user */
        $user = User::query()->findOrFail($request->session()->get('email_otp_verified_user_id'));

        return view('auth.passwords.email-otp-reset', [
            'user' => $user,
        ]);
    }

    /**
     * Update user password in database.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        if (! $request->session()->get('email_otp_verified') || ! $request->session()->get('email_otp_verified_user_id')) {
            return redirect()->route('password.email_otp.request')->with(
                'warning',
                'Phiên xác thực đã kết thúc. Vui lòng thực hiện lại từ đầu.'
            );
        }

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có tối thiểu 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        /** @var User $user */
        $user = User::query()->findOrFail($request->session()->get('email_otp_verified_user_id'));
        $user->password = Hash::make($request->password);
        $user->save();

        // Clear session keys
        $request->session()->forget(['email_otp_verified', 'email_otp_verified_user_id', 'email_otp_data']);

        // Auto login
        Auth::login($user);

        return redirect()->route('home')->with(
            'success',
            'Đổi mật khẩu thành công! Chào mừng '.$user->name.' đã đăng nhập lại hệ thống thư viện.'
        );
    }
}
