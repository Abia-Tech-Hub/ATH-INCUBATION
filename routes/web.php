<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Admin\SetupController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\ApplyController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\MagicLinkController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\VerifyController;
use Illuminate\Support\Facades\Route;

// Public pages
Route::view('/', 'home')->name('home');
Route::view('/about', 'about')->name('about');
Route::view('/who-can-apply', 'who-can-apply')->name('who-can-apply');
Route::view('/program', 'program')->name('program');
Route::view('/partners', 'partners')->name('partners');
Route::view('/faqs', 'faqs')->name('faqs');
Route::view('/apply', 'apply')->name('apply');

// The application form posts JSON from the page's own JavaScript.
Route::post('/apply', [ApplyController::class, 'store'])
    ->middleware('throttle:10,10')
    ->name('apply.store');

Route::post('/newsletter', [NewsletterController::class, 'store'])
    ->middleware('throttle:5,15')
    ->name('newsletter.store');

// Customer sign-in by emailed link
Route::get('/account', [AccountController::class, 'index'])->name('account');
Route::post('/account/link', [MagicLinkController::class, 'store'])
    ->middleware('throttle:10,15')
    ->name('account.link');
Route::get('/auth/verify', [VerifyController::class, 'show'])->name('auth.verify');
Route::post('/auth/verify', [VerifyController::class, 'store'])
    ->middleware('throttle:20,15')
    ->name('auth.verify.store');

Route::post('/logout', LogoutController::class)->name('logout');

// Admin area
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])
        ->middleware('throttle:20,15')
        ->name('login.store');

    // The MFA pages must not use the staff gate: it would redirect them to themselves.
    Route::middleware('auth')->group(function () {
        Route::get('mfa', [AuthController::class, 'showMfa'])->name('mfa');
        Route::post('mfa', [AuthController::class, 'verifyMfa'])
            ->middleware('throttle:6,5')
            ->name('mfa.verify');
    });

    // Invite redemption (token in the URL), for invited team members
    Route::get('setup', [SetupController::class, 'show'])->name('setup');
    Route::post('setup', [SetupController::class, 'store'])
        ->middleware('throttle:10,15')
        ->name('setup.store');

    // Two-factor setup, allowed before the second factor exists
    Route::middleware('staff:STAFF,1')->group(function () {
        Route::get('security', [SecurityController::class, 'show'])->name('security');
        Route::post('security/start', [SecurityController::class, 'start'])->name('security.start');
        Route::post('security/confirm', [SecurityController::class, 'confirm'])->name('security.confirm');
    });

    Route::middleware('staff')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    });

    Route::middleware('staff:ADMIN')->group(function () {
        Route::post('applications/{application}/status', [DashboardController::class, 'updateStatus'])
            ->name('applications.status');
    });

    Route::middleware('staff:OWNER')->group(function () {
        Route::get('team', [TeamController::class, 'index'])->name('team');
        Route::post('team', [TeamController::class, 'store'])->name('team.store');
        Route::post('team/{user}/remove', [TeamController::class, 'remove'])->name('team.remove');

        Route::get('audit', [AuditController::class, 'index'])->name('audit');
    });
});
