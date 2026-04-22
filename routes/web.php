<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Livewire\SignupWizard;
use Illuminate\Support\Facades\Route;

// â”€â”€ Public routes â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

Route::get('/', fn() => redirect()->route('login'));

// PHP Info
Route::get('/phpinfo', function () {
    phpinfo();
})->name('phpinfo');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/forgot-password',  [ForgotPasswordController::class, 'showLinkRequestForm'])
         ->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
         ->name('password.email');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])
         ->name('password.reset');
    Route::post('/reset-password',  [ForgotPasswordController::class, 'reset'])
         ->name('password.update');

    Route::get('/signup', SignupWizard::class)->name('signup');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// â”€â”€ WhatsApp Webhook (public â€” Meta verifies it) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
Route::get('/webhook/whatsapp',  [\App\Http\Controllers\Whatsapp\WhatsappWebhookController::class, 'verify']);
Route::post('/webhook/whatsapp', [\App\Http\Controllers\Whatsapp\WhatsappWebhookController::class, 'handle']);

// â”€â”€ Authenticated gym routes â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
Route::middleware(['auth', 'gym.active'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Members
    Route::resource('members', \App\Http\Controllers\MemberController::class);
    Route::get('/members/{member}/plans',           [\App\Http\Controllers\MemberController::class, 'plans'])->name('members.plans');
    Route::post('/members/{member}/plans',          [\App\Http\Controllers\MemberController::class, 'assignPlan'])->name('members.plans.assign');
    Route::patch('/members/{member}/freeze',        [\App\Http\Controllers\MemberController::class, 'freeze'])->name('members.freeze');
    Route::patch('/members/{member}/assign-trainer',  [\App\Http\Controllers\MemberController::class, 'assignTrainer'])->name('members.assign-trainer');
    Route::patch('/members/{member}/assign-diet-plan',        [\App\Http\Controllers\MemberController::class, 'assignDietPlan'])->name('members.assign-diet-plan');
    Route::patch('/members/{member}/assign-trainer-schedule', [\App\Http\Controllers\MemberController::class, 'assignTrainerSchedule'])->name('members.assign-trainer-schedule');
    // Trainers
    Route::resource('trainers', \App\Http\Controllers\TrainerController::class);
    Route::post('/trainers/{trainer}/schedules',           [\App\Http\Controllers\TrainerController::class, 'scheduleStore'])->name('trainers.schedule.store');
    Route::delete('/trainers/{trainer}/schedules/{schedule}', [\App\Http\Controllers\TrainerController::class, 'scheduleDestroy'])->name('trainers.schedule.destroy');

    // Classes
    Route::resource('classes', \App\Http\Controllers\GymClassController::class);

    // Plans (membership)
    Route::resource('plans', \App\Http\Controllers\PlanController::class);

    // Attendance
    Route::get('/attendance',                  [\App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/search-members',   [\App\Http\Controllers\AttendanceController::class, 'searchMembers'])->name('attendance.search-members');
    Route::post('/attendance/check-in',        [\App\Http\Controllers\AttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::patch('/attendance/{attendance}/check-out', [\App\Http\Controllers\AttendanceController::class, 'checkOut'])->name('attendance.check-out');

    // Finance
    Route::get('/finance',                [\App\Http\Controllers\Finance\FinanceController::class, 'index'])->name('finance.index');
    Route::resource('expenses',           \App\Http\Controllers\Finance\ExpenseController::class);
    Route::resource('invoices',           \App\Http\Controllers\Finance\InvoiceController::class);
    Route::get('/invoices/{invoice}/pdf', [\App\Http\Controllers\Finance\InvoiceController::class, 'pdf'])->name('invoices.pdf');
    Route::post('/payments',              [\App\Http\Controllers\Finance\InvoiceController::class, 'storePayment'])->name('payments.store');

    // Workouts
    Route::resource('workout-sequences', \App\Http\Controllers\Workout\WorkoutSequenceController::class);
    Route::resource('workout-plans',      \App\Http\Controllers\Workout\WorkoutPlanController::class);
    Route::resource('workout-activities', \App\Http\Controllers\Workout\WorkoutActivityController::class);
    Route::resource('workout-categories', \App\Http\Controllers\Workout\WorkoutCategoryController::class);

    // Diet Plans
    Route::resource('diet-plans', \App\Http\Controllers\DietPlanController::class);
    Route::post('/diet-plans/{dietPlan}/meals',        [\App\Http\Controllers\DietPlanController::class, 'mealStore'])->name('diet-plans.meals.store');
    Route::delete('/diet-plans/{dietPlan}/meals/{meal}', [\App\Http\Controllers\DietPlanController::class, 'mealDestroy'])->name('diet-plans.meals.destroy');

    // AI Diet Plans (premium module)
    Route::middleware('module:ai_diet_plans')->group(function () {
        Route::post('/diet-plans/generate-ai', [\App\Http\Controllers\DietPlanController::class, 'generateAi'])->name('diet-plans.generate-ai');
    });

    // AI Workout Plans (premium module)
    Route::middleware('module:ai_workout_plans')->group(function () {
        Route::post('/workout-plans/generate-ai', [\App\Http\Controllers\Workout\WorkoutPlanController::class, 'generateAi'])->name('workout-plans.generate-ai');
    });

    // Events
    Route::resource('events',      \App\Http\Controllers\EventController::class);
    Route::resource('event-types', \App\Http\Controllers\EventTypeController::class);
    Route::get('/events/calendar-data', [\App\Http\Controllers\EventController::class, 'calendarData'])->name('events.calendar-data');

    // Health stats
    Route::resource('body-stats',      \App\Http\Controllers\Health\BodyStatController::class);
    Route::resource('progress-photos', \App\Http\Controllers\Health\ProgressPhotoController::class);

    // Lockers
    Route::resource('lockers', \App\Http\Controllers\LockerController::class);

    // Notice Board
    Route::resource('notices', \App\Http\Controllers\NoticeController::class);

    // Contact Diary
    Route::resource('contact-diary', \App\Http\Controllers\ContactDiaryController::class);

    // WhatsApp (premium module)
    Route::middleware('module:whatsapp_updates')->group(function () {
        Route::resource('whatsapp/campaigns', \App\Http\Controllers\Whatsapp\WhatsappCampaignController::class)->names('whatsapp.campaigns');
        Route::get('/whatsapp/logs',          [\App\Http\Controllers\Whatsapp\WhatsappLogController::class, 'index'])->name('whatsapp.logs');
    });

    // Reports
    Route::get('/reports/members',  [\App\Http\Controllers\ReportController::class, 'members'])->name('reports.members');
    Route::get('/reports/revenue',  [\App\Http\Controllers\ReportController::class, 'revenue'])->name('reports.revenue');
    Route::get('/reports/trainers', [\App\Http\Controllers\ReportController::class, 'trainers'])->name('reports.trainers');

    // System Configuration
    Route::resource('categories',    \App\Http\Controllers\CategoryController::class);
    Route::resource('finance-types', \App\Http\Controllers\Finance\FinanceTypeController::class);

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/',          [\App\Http\Controllers\SettingsController::class, 'index'])->name('index');
        Route::post('/profile',  [\App\Http\Controllers\SettingsController::class, 'updateProfile'])->name('profile');
        Route::post('/password', [\App\Http\Controllers\SettingsController::class, 'updatePassword'])->name('password');
        Route::post('/general',  [\App\Http\Controllers\SettingsController::class, 'updateGeneral'])->name('general');
        Route::post('/company',  [\App\Http\Controllers\SettingsController::class, 'updateCompany'])->name('company');
        Route::post('/email',    [\App\Http\Controllers\SettingsController::class, 'updateEmail'])->name('email');
        Route::post('/payment',  [\App\Http\Controllers\SettingsController::class, 'updatePayment'])->name('payment');
        Route::post('/whatsapp', [\App\Http\Controllers\SettingsController::class, 'updateWhatsapp'])->name('whatsapp');
        Route::post('/openai',   [\App\Http\Controllers\SettingsController::class, 'updateOpenai'])->name('openai');
    });

    // Payment gateway callbacks
    Route::post('/payment/razorpay/webhook', [\App\Http\Controllers\Payment\RazorpayController::class, 'webhook'])->name('payment.razorpay.webhook');
    Route::post('/payment/stripe/webhook',   [\App\Http\Controllers\Payment\StripeController::class, 'webhook'])->name('payment.stripe.webhook');
    Route::get('/payment/razorpay/callback', [\App\Http\Controllers\Payment\RazorpayController::class, 'callback'])->name('payment.razorpay.callback');
});

// â”€â”€ Super Admin routes â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
Route::middleware(['auth', 'role:super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('gyms',    \App\Http\Controllers\SuperAdmin\GymController::class);
    Route::resource('pricing', \App\Http\Controllers\SuperAdmin\PricingController::class);
    Route::get('/settings',    [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'index'])->name('settings');
    Route::post('/settings',   [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'update'])->name('settings.update');
});
