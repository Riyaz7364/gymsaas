<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Livewire\SignupWizard;
use Illuminate\Support\Facades\Route;

// Public routes 

Route::get('/', function () {
    if (auth('member')->check()) {
        return redirect()->route('member.dashboard');
    }

    if (auth('trainer')->check()) {
        return redirect()->route('trainer.dashboard');
    }

    if (auth()->check()) {
        $user = auth()->user();
        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return redirect()->route('super-admin.dashboard');
        }
        if ($user->gym_id && $gym = \App\Models\Gym::find($user->gym_id)) {
            return redirect()->route('gym.dashboard', ['gym' => $gym->slug]);
        }
    }

    return redirect()->route('login');
});

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

// WhatsApp Webhook (public — Meta verifies it)
Route::get('/webhook/whatsapp',  [\App\Http\Controllers\Whatsapp\WhatsappWebhookController::class, 'verify']);
Route::post('/webhook/whatsapp', [\App\Http\Controllers\Whatsapp\WhatsappWebhookController::class, 'handle']);

// Authenticated gym routes with gym access and active checks
Route::scopeBindings()->middleware(['auth', 'gym.active', 'gym.access'])->prefix('/{gym:slug}')->name('gym.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/modules', [\App\Http\Controllers\ModuleController::class, 'index'])->name('modules.index');

    Route::middleware('module:members_management')->group(function () {
        Route::resource('members', \App\Http\Controllers\MemberController::class);
        Route::get('/members/{member}/plans', [\App\Http\Controllers\MemberController::class, 'plans'])->name('members.plans');
        Route::post('/members/{member}/plans', [\App\Http\Controllers\MemberController::class, 'assignPlan'])->name('members.plans.assign');
        Route::patch('/members/{member}/freeze', [\App\Http\Controllers\MemberController::class, 'freeze'])->name('members.freeze');
        Route::patch('/members/{member}/assign-trainer', [\App\Http\Controllers\MemberController::class, 'assignTrainer'])->name('members.assign-trainer');
        Route::patch('/members/{member}/assign-diet-plan', [\App\Http\Controllers\MemberController::class, 'assignDietPlan'])->name('members.assign-diet-plan');
        Route::patch('/members/{member}/assign-trainer-schedule', [\App\Http\Controllers\MemberController::class, 'assignTrainerSchedule'])->name('members.assign-trainer-schedule');
    });

    Route::middleware('module:trainers_management')->group(function () {
        Route::resource('trainers', \App\Http\Controllers\TrainerController::class);
        Route::post('/trainers/{trainer}/schedules', [\App\Http\Controllers\TrainerController::class, 'scheduleStore'])->name('trainers.schedule.store');
        Route::delete('/trainers/{trainer}/schedules/{schedule}', [\App\Http\Controllers\TrainerController::class, 'scheduleDestroy'])->name('trainers.schedule.destroy');
    });

    Route::middleware('module:event_management')->group(function () {
        Route::resource('classes', \App\Http\Controllers\GymClassController::class);
    });

    Route::middleware('module:membership_management')->group(function () {
        Route::resource('plans', \App\Http\Controllers\PlanController::class);
    });

    Route::middleware('module:attendance_management')->group(function () {
        Route::get('/attendance', [\App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/search-members', [\App\Http\Controllers\AttendanceController::class, 'searchMembers'])->name('attendance.search-members');
        Route::post('/attendance/check-in', [\App\Http\Controllers\AttendanceController::class, 'checkIn'])->name('attendance.check-in');
        Route::patch('/attendance/{attendance}/check-out', [\App\Http\Controllers\AttendanceController::class, 'checkOut'])->name('attendance.check-out');
        Route::get('/attendance/generate-qr', [\App\Http\Controllers\AttendanceController::class, 'generateQr'])->name('attendance.generate-qr');
    });

    Route::middleware('module:finance_management')->group(function () {
        Route::get('/finance', [\App\Http\Controllers\Finance\FinanceController::class, 'index'])->name('finance');
        Route::resource('expenses', \App\Http\Controllers\Finance\ExpenseController::class);
        Route::resource('invoices', \App\Http\Controllers\Finance\InvoiceController::class);
        Route::get('/invoices/{invoice}/pdf', [\App\Http\Controllers\Finance\InvoiceController::class, 'pdf'])->name('invoices.pdf');
        Route::post('/payments', [\App\Http\Controllers\Finance\InvoiceController::class, 'storePayment'])->name('payments.store');
    });

    Route::middleware('module:online_payments')->group(function () {
        Route::get('/payments', [\App\Http\Controllers\Finance\PaymentHistoryController::class, 'index'])->name('payments');
        Route::get('/payments/{payment}', [\App\Http\Controllers\Finance\PaymentHistoryController::class, 'show'])->name('payments.show');
        Route::post('/payments/{payment}/retry-transfer', [\App\Http\Controllers\Finance\PaymentHistoryController::class, 'retryTransfer'])->name('payments.transfer');
    });

    Route::middleware('module:workout_management')->group(function () {
        Route::resource('workout-sequences', \App\Http\Controllers\Workout\WorkoutSequenceController::class);
        Route::resource('workout-plans', \App\Http\Controllers\Workout\WorkoutPlanController::class);
        Route::resource('workout-activities', \App\Http\Controllers\Workout\WorkoutActivityController::class);
        Route::resource('workout-categories', \App\Http\Controllers\Workout\WorkoutCategoryController::class);
        Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    });

    Route::middleware('module:diet_management')->group(function () {
        Route::resource('diet-plans', \App\Http\Controllers\DietPlanController::class);
        Route::post('/diet-plans/{dietPlan}/meals', [\App\Http\Controllers\DietPlanController::class, 'mealStore'])->name('diet-plans.meals.store');
        Route::delete('/diet-plans/{dietPlan}/meals/{meal}', [\App\Http\Controllers\DietPlanController::class, 'mealDestroy'])->name('diet-plans.meals.destroy');
        Route::resource('food-categories', \App\Http\Controllers\FoodCategoryController::class);
        Route::resource('food-items', \App\Http\Controllers\FoodItemController::class);
    });

    // AI Diet Plans (premium module)
    Route::middleware(['module:diet_management', 'module:ai_diet_plans'])->group(function () {
        Route::post('/diet-plans/generate-ai', [\App\Http\Controllers\DietPlanController::class, 'generateAi'])->name('diet-plans.generate-ai');
    });

    // AI Workout Plans (premium module)
    Route::middleware(['module:workout_management', 'module:ai_workout_plans'])->group(function () {
        Route::post('/workout-plans/generate-ai', [\App\Http\Controllers\Workout\WorkoutPlanController::class, 'generateAi'])->name('workout-plans.generate-ai');
    });

    Route::middleware('module:event_management')->group(function () {
        Route::resource('events', \App\Http\Controllers\EventController::class);
        Route::resource('event-types', \App\Http\Controllers\EventTypeController::class);
        Route::get('/events/calendar-data', [\App\Http\Controllers\EventController::class, 'calendarData'])->name('events.calendar-data');
    });

    Route::middleware('module:body_progress')->group(function () {
        Route::resource('body-stats', \App\Http\Controllers\Health\BodyStatController::class);
        Route::get('progress-photos', [\App\Http\Controllers\Health\BodyStatController::class, 'gallery'])->name('progress-photos.index');
    });

    Route::middleware('module:locker_management')->group(function () {
        Route::resource('lockers', \App\Http\Controllers\LockerController::class);
    });

    Route::middleware('module:notice_board')->group(function () {
        Route::resource('notices', \App\Http\Controllers\NoticeController::class);
    });

    Route::middleware('module:contact_diary')->group(function () {
        Route::resource('contact-diary', \App\Http\Controllers\ContactDiaryController::class);
    });

    // WhatsApp (premium module)
    Route::middleware('module:whatsapp_updates')->group(function () {
        Route::resource('whatsapp/campaigns', \App\Http\Controllers\Whatsapp\WhatsappCampaignController::class)->names('whatsapp.campaigns');
        Route::get('/whatsapp/logs',          [\App\Http\Controllers\Whatsapp\WhatsappLogController::class, 'index'])->name('whatsapp.logs');
    });

    Route::middleware('module:advanced_reports')->group(function () {
        Route::get('/reports/members', [\App\Http\Controllers\ReportController::class, 'members'])->name('reports.members');
        Route::get('/reports/revenue', [\App\Http\Controllers\ReportController::class, 'revenue'])->name('reports.revenue');
        Route::get('/reports/trainers', [\App\Http\Controllers\ReportController::class, 'trainers'])->name('reports.trainers');
    });

    Route::middleware('module:finance_management')->group(function () {
        Route::resource('finance-types', \App\Http\Controllers\Finance\FinanceTypeController::class);
    });

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
}); // End Gym routes

// â”€â”€ Member routes â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
Route::middleware('guest:trainer')->group(function () {
    Route::get('/trainer/login', [\App\Http\Controllers\TrainerAuthController::class, 'showLoginForm'])->name('trainer.login');
    Route::post('/trainer/login', [\App\Http\Controllers\TrainerAuthController::class, 'login']);
});

Route::middleware('auth:trainer')->name('trainer.')->group(function () {
    Route::post('/trainer/logout', [\App\Http\Controllers\TrainerAuthController::class, 'logout'])->name('logout');
    Route::get('/trainer/dashboard', [\App\Http\Controllers\TrainerAuthController::class, 'dashboard'])->name('dashboard');
    Route::resource('/trainer/workout-plans', \App\Http\Controllers\Trainer\WorkoutPlanController::class);
});

Route::get('/member/login', [\App\Http\Controllers\MemberAuthController::class, 'showLoginForm'])->name('member.login');
Route::post('/member/login', [\App\Http\Controllers\MemberAuthController::class, 'login']);

Route::middleware('auth:member')->group(function () {
    Route::get('/member/dashboard', [\App\Http\Controllers\MemberAuthController::class, 'dashboard'])->name('member.dashboard');
    Route::post('/member/logout', [\App\Http\Controllers\MemberAuthController::class, 'logout'])->name('member.logout');
    Route::post('/member/scan-qr', [\App\Http\Controllers\MemberAuthController::class, 'scanQr'])->name('member.scan-qr');
    Route::post('/member/review-trainer', [\App\Http\Controllers\MemberAuthController::class, 'submitTrainerReview'])->name('member.review-trainer');
});

// â”€â”€ Super Admin routes â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
Route::middleware(['auth', 'role:super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/subscriptions', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('/subscriptions/{subscriber}', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'show'])->name('subscriptions.show');
    Route::resource('gyms',    \App\Http\Controllers\SuperAdmin\GymController::class);
    Route::resource('pricing', \App\Http\Controllers\SuperAdmin\PricingController::class);
    Route::get('/settings',    [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'index'])->name('settings');
    Route::post('/settings',   [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'update'])->name('settings.update');
});
