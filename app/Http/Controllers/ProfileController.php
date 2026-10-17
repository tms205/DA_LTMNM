<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Services\GoogleAuthenticatorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the profile edit page with 3 tabs: Personal Info, Password, Google 2FA.
     */
    public function edit(Request $request, GoogleAuthenticatorService $gaService): View
    {
        $user = $request->user();
        $secret = null;
        $qrCodeUrl = null;

        if (! $user->hasGoogle2Fa()) {
            $secret = $request->session()->get('google2fa_pending_secret');

            if (! is_string($secret) || $secret === '') {
                $secret = $gaService->generateSecret();
                $request->session()->put('google2fa_pending_secret', $secret);
            }

            $qrCodeUrl = $gaService->getQrCodeUrl($user->email, $secret);
        }

        return view('profile.edit', compact('user', 'secret', 'qrCodeUrl'));
    }

    /**
     * Update the user's personal profile information.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->update($request->validated());

        return redirect()->route('profile.edit', ['tab' => 'info'])
            ->with('success', 'Thông tin cá nhân đã được cập nhật thành công!');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'current_password.current_password' => 'Mật khẩu hiện tại không chính xác.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có tối thiểu 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        $user = $request->user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.edit', ['tab' => 'password'])
            ->with('success', 'Mật khẩu của bạn đã được thay đổi thành công!');
    }

    /**
     * Enable or disable Google Authenticator 2FA.
     */
    public function toggleGoogle2fa(Request $request, GoogleAuthenticatorService $gaService): RedirectResponse
    {
        $user = $request->user();

        if ($request->string('action')->toString() === 'disable') {
            $request->validate([
                'current_password' => ['required', 'current_password'],
                'otp_code' => ['required', 'digits:6'],
            ], [
                'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
                'current_password.current_password' => 'Mật khẩu hiện tại không chính xác.',
                'otp_code.required' => 'Vui lòng nhập mã OTP hiện tại.',
                'otp_code.digits' => 'Mã OTP phải bao gồm chính xác 6 chữ số.',
            ]);

            if (! $user->hasGoogle2Fa() || ! $gaService->verifyCode($user->google2fa_secret, $request->string('otp_code')->toString())) {
                return redirect()->route('profile.edit', ['tab' => '2fa'])
                    ->withErrors(['otp_code' => 'Mã OTP Google Authenticator không chính xác hoặc đã hết hạn.']);
            }

            $user->google2fa_secret = null;
            $user->save();
            $request->session()->forget('google2fa_pending_secret');

            return redirect()->route('profile.edit', ['tab' => '2fa'])
                ->with('info', 'Đã hủy liên kết xác thực Google Authenticator cho tài khoản của bạn.');
        }

        $request->validate([
            'otp_code' => ['required', 'digits:6'],
        ], [
            'otp_code.required' => 'Vui lòng nhập mã OTP 6 số để xác thực kích hoạt.',
            'otp_code.digits' => 'Mã OTP phải bao gồm chính xác 6 chữ số.',
        ]);

        $secret = $request->session()->get('google2fa_pending_secret');

        if (! is_string($secret) || $secret === '') {
            return redirect()->route('profile.edit', ['tab' => '2fa'])
                ->withErrors(['otp_code' => 'Phiên kích hoạt đã hết hạn. Vui lòng quét mã QR mới.']);
        }

        if (! $gaService->verifyCode($secret, $request->string('otp_code')->toString())) {
            return redirect()->route('profile.edit', ['tab' => '2fa'])
                ->withErrors(['otp_code' => 'Mã OTP Google Authenticator không chính xác hoặc đã hết hạn. Vui lòng thử lại.'])
                ->withInput();
        }

        $user->google2fa_secret = $secret;
        $user->save();
        $request->session()->forget('google2fa_pending_secret');

        return redirect()->route('profile.edit', ['tab' => '2fa'])
            ->with('success', 'Kích hoạt bảo mật xác thực Google Authenticator (OTP) thành công!');
    }
}
