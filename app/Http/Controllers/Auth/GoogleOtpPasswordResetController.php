<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\GoogleAuthenticatorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class GoogleOtpPasswordResetController extends Controller
{
    /**
     * Show the email input form to initiate Google OTP password reset.
     */
    public function showRequestForm(): View
    {
        return view('auth.passwords.google-otp-request');
    }

    /**
     * Process the user's email and prepare for Google OTP verification.
     */
    public function submitRequest(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email tài khoản.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'email.exists' => 'Không tìm thấy tài khoản nào khớp với địa chỉ email này.',
        ]);

        $user = User::query()->where('email', $request->string('email')->toString())->firstOrFail();

        if (! $user->hasGoogle2Fa()) {
            return back()->withErrors([
                'email' => 'Tài khoản chưa kích hoạt Google Authenticator. Vui lòng dùng phương thức OTP qua email.',
            ])->withInput();
        }

        $request->session()->put('google_otp_reset_user_id', $user->id);

        return redirect()->route('password.google.verify');
    }

    /**
     * Show the OTP input and QR verification page.
     */
    public function showVerifyForm(Request $request): View|RedirectResponse
    {
        $userId = $request->session()->get('google_otp_reset_user_id');

        if (! $userId) {
            return redirect()->route('password.google.request');
        }

        $user = User::query()->findOrFail($userId);

        if (! $user->hasGoogle2Fa()) {
            $request->session()->forget('google_otp_reset_user_id');

            return redirect()->route('password.google.request')->withErrors([
                'email' => 'Tài khoản chưa kích hoạt Google Authenticator.',
            ]);
        }

        return view('auth.passwords.google-otp-verify', compact('user'));
    }

    /**
     * Verify the 6-digit Google Authenticator OTP code.
     */
    public function verifyOtp(Request $request, GoogleAuthenticatorService $gaService): RedirectResponse
    {
        $userId = $request->session()->get('google_otp_reset_user_id');

        if (! $userId) {
            return redirect()->route('password.google.request');
        }

        $user = User::query()->findOrFail($userId);

        if (! $user->hasGoogle2Fa()) {
            $request->session()->forget('google_otp_reset_user_id');

            return redirect()->route('password.google.request')->withErrors([
                'email' => 'Tài khoản chưa kích hoạt Google Authenticator.',
            ]);
        }

        $request->validate([
            'otp_code' => ['required', 'digits:6'],
        ], [
            'otp_code.required' => 'Vui lòng nhập mã OTP 6 số từ Google Authenticator.',
            'otp_code.digits' => 'Mã OTP phải bao gồm chính xác 6 chữ số.',
        ]);

        if (! $gaService->verifyCode($user->google2fa_secret, $request->otp_code)) {
            return redirect()->route('password.google.verify')
                ->withErrors(['otp_code' => 'Mã OTP Google Authenticator không chính xác hoặc đã hết thời gian hiệu lực (mỗi mã tồn tại 30 giây).']);
        }

        // OTP verified successfully: promote session to verified state
        $request->session()->forget('google_otp_reset_user_id');
        $request->session()->put('google_otp_verified_user_id', $user->id);

        return redirect()->route('password.google.reset');
    }

    /**
     * Show form to set the new password after OTP verification.
     */
    public function showResetForm(Request $request): View|RedirectResponse
    {
        $userId = $request->session()->get('google_otp_verified_user_id');

        if (! $userId) {
            return redirect()->route('password.google.request');
        }

        $user = User::query()->findOrFail($userId);

        return view('auth.passwords.google-otp-reset', compact('user'));
    }

    /**
     * Save the new password.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $userId = $request->session()->get('google_otp_verified_user_id');

        if (! $userId) {
            return redirect()->route('password.google.request');
        }

        $user = User::query()->findOrFail($userId);

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có tối thiểu 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        $request->session()->forget('google_otp_verified_user_id');

        return redirect()->route('login')
            ->with('success', 'Khôi phục mật khẩu thành công! Bạn có thể sử dụng mật khẩu mới để đăng nhập ngay bây giờ.');
    }
}
