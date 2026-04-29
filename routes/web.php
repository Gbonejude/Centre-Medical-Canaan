<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\BackOffice\AdminController;
use App\Http\Controllers\BackOffice\Hospital\AppointmentController;
use App\Http\Controllers\BackOffice\Hospital\DoctorController;
use App\Http\Controllers\BackOffice\Hospital\MedicalServiceController;
use App\Http\Controllers\BackOffice\Hospital\SpecialtyController;
use App\Http\Controllers\BackOffice\Notification\NotificationController;
use App\Http\Controllers\BackOffice\ProfileController;
use App\Http\Controllers\BackOffice\UserController;
use App\Http\Controllers\FrontOffice\Auth\AuthGuestController;
use App\Http\Controllers\FrontOffice\PagesController;
use Illuminate\Support\Facades\Route;

// Extract domain from APP_URL
$appUrl = config('app.url');
$domain = parse_url($appUrl, PHP_URL_HOST) ?: $appUrl;

/*
|--------------------------------------------------------------------------
| Front-Office Routes (Patients / Guests)
|--------------------------------------------------------------------------
*/
Route::domain($domain)->group(function () {
    Route::controller(PagesController::class)->name('home.')->group(function () {
        Route::get('/', 'index')->name('index');
    });

    // Patient/Guest Auth
    Route::controller(AuthGuestController::class)->name('auth.guest.')->group(function () {
        Route::get('/login', 'showLoginForm')->name('login.form');
        Route::post('/login', 'login')->name('login');
        Route::get('/register', 'showRegisterForm')->name('register.form');
        Route::post('/register', 'register')->name('register');
        Route::post('/logout', 'logout')->name('logout')->middleware('auth:guest');
    });

    // Front-Office Appointments
    Route::middleware('guest.auth')->group(function () {
        Route::get('/appointments/booking', [AppointmentController::class, 'create'])->name('front.appointments.create');
        Route::post('/appointments/booking', [AppointmentController::class, 'store'])->name('front.appointments.store');
        Route::get('/appointments/{appointment}/edit', [AppointmentController::class, 'editFront'])->name('front.appointments.edit');
        Route::put('/appointments/{appointment}', [AppointmentController::class, 'updateFront'])->name('front.appointments.update');
        Route::patch('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancelFront'])->name('front.appointments.cancel');
        Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroyFront'])->name('front.appointments.destroy');
        Route::get('/my-appointments', [AppointmentController::class, 'mine'])->name('front.appointments.mine');

        // Patient Profile
        Route::get('/profile', [\App\Http\Controllers\FrontOffice\PatientProfileController::class, 'index'])->name('front.profile.index');
        Route::put('/profile', [\App\Http\Controllers\FrontOffice\PatientProfileController::class, 'update'])->name('front.profile.update');
    });
});

/*
|--------------------------------------------------------------------------
| Back-Office Routes (Administration)
|--------------------------------------------------------------------------
*/
Route::group(['domain' => 'admin.'.$domain], function () {

    // Authentification Admin
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('auth.loginForm');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

    Route::controller(PasswordResetController::class)->group(function () {
        Route::get('/forgot-password', 'showForgotForm')->name('auth.password.forgot.form');
        Route::post('/forgot-password', 'sendResetLink')->name('auth.password.forgot');
        Route::get('/reset-password/{token}', 'showResetForm')->name('auth.password.reset.form');
        Route::post('/reset-password', 'resetPassword')->name('auth.password.reset');
    });

    // Routes protégées par authentification Admin
    Route::group(['middleware' => ['auth']], function () {

        Route::get('/', [AdminController::class, 'index'])->name('dashboard.index');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        // Notifications
        Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
        Route::post('/notifications/{id}/markasread', [NotificationController::class, 'markasread'])->name('notifications.markAsRead');
        Route::resource('notifications', NotificationController::class)->except(['show', 'edit']);
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

        // Gestion des Utilisateurs (Admin, Staff, etc.)
        Route::controller(UserController::class)->group(function () {
            Route::resource('users', UserController::class)->only(['index', 'create', 'store']);
            Route::get('/users/{users:uuid}', 'show')->name('users.show');
            Route::get('/users/{users:uuid}/edit', 'edit')->name('users.edit');
            Route::put('/users/{users:uuid}', 'update')->name('users.update');
            Route::patch('users/{users:uuid}/status', [UserController::class, 'updateStatus'])->name('users.updateStatus');
            Route::delete('/users/{users:uuid}', 'destroy')->name('users.destroy');
        });

        // Profil Utilisateur
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'index'])->name('index');
            Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
            Route::put('/update', [ProfileController::class, 'update'])->name('update');
            Route::put('/change-password', [ProfileController::class, 'changePassword'])->name('change-password');
        });

        /*
        |--------------------------------------------------------------------------
        | MODULE HÔPITAL
        |--------------------------------------------------------------------------
        */
        Route::resource('medical-services', MedicalServiceController::class);
        Route::patch('medical-services/{medicalService}/toggle-status', [MedicalServiceController::class, 'toggleStatus'])->name('medical-services.toggle-status');

        Route::resource('specialties', SpecialtyController::class);
        Route::patch('specialties/{specialty}/toggle-status', [SpecialtyController::class, 'toggleStatus'])->name('specialties.toggle-status');

        Route::resource('doctors', DoctorController::class);
        Route::patch('doctors/{doctor}/toggle-availability', [DoctorController::class, 'toggleAvailability'])->name('doctors.toggle-availability');

        Route::resource('patients', \App\Http\Controllers\BackOffice\Hospital\PatientController::class);

        Route::resource('appointments', AppointmentController::class);
        Route::post('appointments/{appointment}/assign', [AppointmentController::class, 'assignDoctor'])->name('appointments.assign');
        Route::post('appointments/{appointment}/confirm', [AppointmentController::class, 'assignDoctor'])->name('appointments.confirm');
        Route::put('appointments/{appointment}/update-status', [AppointmentController::class, 'updateStatus'])->name('appointments.update-status');

        Route::get('availabilities', [\App\Http\Controllers\BackOffice\Hospital\DoctorScheduleController::class, 'index'])->name('schedules.index');
        Route::get('availabilities/{doctor}', [\App\Http\Controllers\BackOffice\Hospital\DoctorScheduleController::class, 'edit'])->name('schedules.edit');
        Route::put('availabilities/{doctor}', [\App\Http\Controllers\BackOffice\Hospital\DoctorScheduleController::class, 'update'])->name('schedules.update');

        Route::get('planning', [\App\Http\Controllers\BackOffice\Hospital\PlanningController::class, 'index'])->name('planning.index');
    });
});
