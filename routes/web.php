<?php

use App\Http\Controllers\Admin\LoanReminderController as AdminLoanReminderController;
use App\Http\Controllers\Admin\PhieuMuonController as AdminPhieuMuonController;
use App\Http\Controllers\Admin\PhieuPhatController as AdminPhieuPhatController;
use App\Http\Controllers\Admin\SachController as AdminSachController;
use App\Http\Controllers\Admin\TacGiaController as AdminTacGiaController;
use App\Http\Controllers\Admin\TheLoaiController as AdminTheLoaiController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\EmailOtpPasswordResetController;
use App\Http\Controllers\Auth\GoogleOtpPasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\PhieuMuonController as UserPhieuMuonController;
use App\Http\Controllers\User\PhieuPhatController as UserPhieuPhatController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/sach/{sach}', [CatalogController::class, 'show'])->name('catalog.show');

Auth::routes();

// Registration Verification (Email OTP & Google Authenticator)
Route::get('/dang-ky/xac-thuc', [RegisterController::class, 'showVerifyForm'])->name('register.verify');
Route::post('/dang-ky/xac-thuc', [RegisterController::class, 'verify'])->name('register.verify.post');
Route::post('/dang-ky/gui-lai-otp', [RegisterController::class, 'resendOtp'])->name('register.resend');

// Email OTP Password Recovery (Gmail SMTP)
Route::prefix('quen-mat-khau-email-otp')->name('password.email_otp.')->group(function (): void {
    Route::get('/', [EmailOtpPasswordResetController::class, 'showRequestForm'])->name('request');
    Route::post('/', [EmailOtpPasswordResetController::class, 'submitRequest'])->name('request.post');
    Route::get('/xac-thuc', [EmailOtpPasswordResetController::class, 'showVerifyForm'])->name('verify');
    Route::post('/xac-thuc', [EmailOtpPasswordResetController::class, 'verifyOtp'])->name('verify.post');
    Route::post('/gui-lai', [EmailOtpPasswordResetController::class, 'resendOtp'])->name('resend');
    Route::get('/dat-lai', [EmailOtpPasswordResetController::class, 'showResetForm'])->name('reset');
    Route::post('/dat-lai', [EmailOtpPasswordResetController::class, 'resetPassword'])->name('reset.post');
});

// Google Authenticator OTP Password Recovery
Route::prefix('quen-mat-khau-otp')->name('password.google.')->group(function (): void {
    Route::get('/', [GoogleOtpPasswordResetController::class, 'showRequestForm'])->name('request');
    Route::post('/', [GoogleOtpPasswordResetController::class, 'submitRequest'])
        ->middleware('throttle:6,1')
        ->name('request.post');
    Route::get('/xac-thuc', [GoogleOtpPasswordResetController::class, 'showVerifyForm'])->name('verify');
    Route::post('/xac-thuc', [GoogleOtpPasswordResetController::class, 'verifyOtp'])
        ->middleware('throttle:6,1')
        ->name('verify.post');
    Route::get('/dat-lai', [GoogleOtpPasswordResetController::class, 'showResetForm'])->name('reset');
    Route::post('/dat-lai', [GoogleOtpPasswordResetController::class, 'resetPassword'])
        ->middleware('throttle:6,1')
        ->name('reset.post');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // User Profile Management & Security
    Route::get('/ho-so', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/ho-so', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/ho-so/mat-khau', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    Route::post('/ho-so/google-2fa', [ProfileController::class, 'toggleGoogle2fa'])->name('profile.toggleGoogle2fa');

    Route::prefix('tai-khoan')->name('user.')->group(function (): void {
        Route::get('/lich-su-muon', [UserPhieuMuonController::class, 'index'])->name('phieu-muons.index');
        Route::get('/lich-su-muon/{phieuMuon}', [UserPhieuMuonController::class, 'show'])->name('phieu-muons.show');
        Route::get('/phieu-phat', [UserPhieuPhatController::class, 'index'])->name('phieu-phats.index');
    });

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function (): void {
        Route::get('nhac-han-tra', [AdminLoanReminderController::class, 'index'])
            ->name('loan-reminders.index');
        Route::post('nhac-han-tra/gui', [AdminLoanReminderController::class, 'store'])
            ->name('loan-reminders.store');
        Route::resource('tac-gias', AdminTacGiaController::class)
            ->parameters(['tac-gias' => 'tacGia']);
        Route::resource('the-loais', AdminTheLoaiController::class)
            ->parameters(['the-loais' => 'theLoai']);
        Route::resource('saches', AdminSachController::class)
            ->parameters(['saches' => 'sach']);
        Route::patch('phieu-muons/{phieuMuon}/tra-sach', [AdminPhieuMuonController::class, 'traSach'])
            ->name('phieu-muons.tra-sach');
        Route::get('phieu-muons/xuat-csv', [AdminPhieuMuonController::class, 'export'])
            ->name('phieu-muons.export');
        Route::resource('phieu-muons', AdminPhieuMuonController::class)
            ->parameters(['phieu-muons' => 'phieuMuon']);
        Route::post('phieu-phats/{phieuPhat}/xac-nhan', [AdminPhieuPhatController::class, 'xacNhanNopPhat'])
            ->name('phieu-phats.xac-nhan');
        Route::resource('phieu-phats', AdminPhieuPhatController::class)
            ->only(['index', 'show', 'destroy'])
            ->parameters(['phieu-phats' => 'phieuPhat']);
        Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::put('users/{user}/ghi-chu', [AdminUserController::class, 'updateNote'])->name('users.update-note');
    });
});
