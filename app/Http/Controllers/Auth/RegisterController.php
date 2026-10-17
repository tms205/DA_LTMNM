<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RegistrationOtpMail;
use App\Models\User;
use App\Services\GoogleAuthenticatorService;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array<string, mixed>  $data
     */
    protected function validator(array $data): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'so_dien_thoai' => ['nullable', 'string', 'regex:/^(0[3|5|7|8|9])[0-9]{8}$/'],
            'ngay_sinh' => ['nullable', 'date', 'before_or_equal:today'],
            'dia_chi' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Vui lòng nhập họ và tên của bạn.',
            'name.max' => 'Họ và tên không được vượt quá 255 ký tự.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'email.unique' => 'Địa chỉ email này đã được sử dụng.',
            'so_dien_thoai.regex' => 'Số điện thoại không đúng định dạng di động Việt Nam (gồm 10 số, bắt đầu bằng 03, 05, 07, 08, 09).',
            'ngay_sinh.date' => 'Ngày sinh không đúng định dạng ngày tháng.',
            'ngay_sinh.before_or_equal' => 'Ngày sinh không thể lớn hơn ngày hiện tại.',
            'dia_chi.max' => 'Địa chỉ không được vượt quá 255 ký tự.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có tối thiểu 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);
    }

    /**
     * Handle a registration request for the application.
     * Initiates OTP / Google 2FA verification step.
     */
    public function register(Request $request, GoogleAuthenticatorService $gaService): RedirectResponse
    {
        $this->validator($request->all())->validate();

        // Anti-spam rate limit: check if registration OTP was sent within last 60 seconds
        $existing = $request->session()->get('pending_registration');
        if ($existing && isset($existing['email'], $existing['email_otp_last_sent_at']) && $existing['email'] === $request->email) {
            $diff = now()->timestamp - $existing['email_otp_last_sent_at'];
            if ($diff < 60) {
                $wait = 60 - $diff;

                return redirect()->route('register.verify')->withErrors([
                    'otp_code' => "Mã xác thực đã vừa được gửi đến email của bạn. Vui lòng đợi thêm {$wait} giây trước khi yêu cầu gửi lại.",
                ]);
            }
        }

        $emailOtp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $gaSecret = $gaService->generateSecret();

        $request->session()->put('pending_registration', [
            'name' => $request->name,
            'email' => $request->email,
            'so_dien_thoai' => $request->so_dien_thoai,
            'ngay_sinh' => $request->ngay_sinh,
            'dia_chi' => $request->dia_chi,
            'password' => Hash::make($request->password),
            'email_otp' => $emailOtp,
            'email_otp_expires_at' => now()->addMinutes(10)->timestamp,
            'email_otp_attempts' => 0,
            'email_otp_last_sent_at' => now()->timestamp,
            'google2fa_secret' => $gaSecret,
        ]);

        try {
            Mail::to($request->email)->send(new RegistrationOtpMail($emailOtp, $request->name, 10));
        } catch (\Throwable $e) {
            Log::warning('Registration email delivery issue: '.$e->getMessage());
        }

        return redirect()->route('register.verify')->with(
            'status',
            'Vui lòng nhập mã OTP gửi về Email hoặc quét mã QR Google Authenticator để kích hoạt tài khoản!'
        );
    }

    /**
     * Show the registration verification form.
     */
    public function showVerifyForm(Request $request, GoogleAuthenticatorService $gaService): View|RedirectResponse
    {
        $pending = $request->session()->get('pending_registration');

        if (! $pending) {
            return redirect()->route('register');
        }

        $qrCodeUrl = $gaService->getQrCodeUrl($pending['email'], $pending['google2fa_secret']);
        $secret = $pending['google2fa_secret'];
        $remainingWait = max(0, 60 - (now()->timestamp - ($pending['email_otp_last_sent_at'] ?? 0)));

        return view('auth.register-verify', compact('pending', 'qrCodeUrl', 'secret', 'remainingWait'));
    }

    /**
     * Complete user registration after verifying OTP code.
     */
    public function verify(Request $request, GoogleAuthenticatorService $gaService): RedirectResponse
    {
        $pending = $request->session()->get('pending_registration');

        if (! $pending) {
            return redirect()->route('register')->with(
                'warning',
                'Phiên đăng ký đã kết thúc. Vui lòng đăng ký lại.'
            );
        }

        $request->validate([
            'method' => ['required', 'in:email,google'],
            'otp_code' => ['required', 'digits:6'],
        ], [
            'otp_code.required' => 'Vui lòng nhập mã xác thực gồm 6 chữ số.',
            'otp_code.digits' => 'Mã OTP phải bao gồm chính xác 6 chữ số.',
        ]);

        $verified = false;
        $enable2Fa = false;

        if ($request->method === 'email') {
            if (now()->timestamp > $pending['email_otp_expires_at']) {
                return back()->withErrors([
                    'otp_code' => 'Mã OTP email đã hết hạn sau 10 phút. Vui lòng bấm "Gửi lại mã OTP".',
                ]);
            }

            if ($pending['email_otp_attempts'] >= 5) {
                return back()->withErrors([
                    'otp_code' => 'Bạn đã nhập sai mã OTP quá 5 lần. Vui lòng thử phương thức khác hoặc bấm gửi lại.',
                ]);
            }

            if (trim($request->otp_code) === (string) $pending['email_otp']) {
                $verified = true;
            } else {
                $pending['email_otp_attempts']++;
                $request->session()->put('pending_registration', $pending);
                $remaining = 5 - $pending['email_otp_attempts'];

                return back()->withErrors([
                    'otp_code' => "Mã OTP email không đúng. Bạn còn {$remaining} lần thử.",
                ]);
            }
        } elseif ($request->method === 'google') {
            if ($gaService->verifyCode($pending['google2fa_secret'], $request->otp_code)) {
                $verified = true;
                $enable2Fa = true;
            } else {
                return back()->withErrors([
                    'otp_code' => 'Mã xác thực từ ứng dụng Google Authenticator không chính xác hoặc đã hết hạn.',
                ]);
            }
        }

        if (! $verified) {
            return back()->withErrors(['otp_code' => 'Xác thực không thành công.']);
        }

        // Create the user in database
        $user = User::create([
            'name' => $pending['name'],
            'email' => $pending['email'],
            'so_dien_thoai' => $pending['so_dien_thoai'],
            'ngay_sinh' => $pending['ngay_sinh'],
            'dia_chi' => $pending['dia_chi'],
            'password' => $pending['password'],
            'role' => 'user',
            'google2fa_secret' => $enable2Fa ? $pending['google2fa_secret'] : null,
        ]);

        $request->session()->forget('pending_registration');
        Auth::login($user);

        return redirect($this->redirectTo)->with(
            'success',
            'Chúc mừng '.$user->name.'! Tài khoản độc giả của bạn đã được xác thực và kích hoạt thành công.'
        );
    }

    /**
     * Resend the registration OTP email.
     */
    public function resendOtp(Request $request): RedirectResponse
    {
        $pending = $request->session()->get('pending_registration');

        if (! $pending) {
            return redirect()->route('register');
        }

        // Rate limit: at least 60s (1 minute) between resends
        if (isset($pending['email_otp_last_sent_at']) && (now()->timestamp - $pending['email_otp_last_sent_at']) < 60) {
            $wait = 60 - (now()->timestamp - $pending['email_otp_last_sent_at']);

            return back()->withErrors([
                'otp_code' => "Vui lòng đợi thêm {$wait} giây trước khi yêu cầu gửi lại mã OTP.",
            ]);
        }

        $newOtp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $pending['email_otp'] = $newOtp;
        $pending['email_otp_expires_at'] = now()->addMinutes(10)->timestamp;
        $pending['email_otp_attempts'] = 0;
        $pending['email_otp_last_sent_at'] = now()->timestamp;

        $request->session()->put('pending_registration', $pending);

        try {
            Mail::to($pending['email'])->send(new RegistrationOtpMail($newOtp, $pending['name'], 10));

            return back()->with('status', 'Đã gửi lại mã OTP kích hoạt mới đến '.$pending['email'].'!');
        } catch (\Throwable $e) {
            Log::warning('Resend registration OTP failed: '.$e->getMessage());

            return back()->with('status', 'Đã tạo lại mã OTP mới!');
        }
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array<string, mixed>  $data
     */
    protected function create(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'so_dien_thoai' => $data['so_dien_thoai'] ?? null,
            'ngay_sinh' => $data['ngay_sinh'] ?? null,
            'dia_chi' => $data['dia_chi'] ?? null,
            'role' => 'user',
            'password' => Hash::make($data['password']),
        ]);
    }
}
