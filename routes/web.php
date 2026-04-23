<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\BackOffice\AdminController;
use App\Http\Controllers\BackOffice\Notification\NotificationController;
use App\Http\Controllers\BackOffice\ProfileController;
use App\Http\Controllers\BackOffice\UserController;
use App\Http\Controllers\BackOffice\Hospital\MedicalServiceController;
use App\Http\Controllers\BackOffice\Hospital\SpecialtyController;
use App\Http\Controllers\BackOffice\Hospital\DoctorController;
use App\Http\Controllers\BackOffice\Hospital\AppointmentController;
use App\Http\Controllers\FrontOffice\PagesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Front-Office Routes
|--------------------------------------------------------------------------
*/
Route::domain(config('app.url'))->group(function () {
    Route::controller(PagesController::class)->name('home.')->group(function () {
        Route::get('/', 'index')->name('index');
    });
});

/*
|--------------------------------------------------------------------------
| Back-Office Routes
|--------------------------------------------------------------------------
*/
Route::group(['domain' => 'admin.'.config('app.url')], function () {

    // Authentification
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('auth.loginForm');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

    Route::controller(PasswordResetController::class)->group(function () {
        Route::get('/forgot-password', 'showForgotForm')->name('auth.password.forgot.form');
        Route::post('/forgot-password', 'sendResetLink')->name('auth.password.forgot');
        Route::get('/reset-password/{token}', 'showResetForm')->name('auth.password.reset.form');
        Route::post('/reset-password', 'resetPassword')->name('auth.password.reset');
    });

    // Routes protégées par authentification
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
        | MODULE HÔPITAL (ESGIS GROUPE 6)
        |--------------------------------------------------------------------------
        */

        // Gestion des Services Médicaux
        Route::resource('medical-services', MedicalServiceController::class);
        Route::patch('medical-services/{medicalService}/toggle-status', [MedicalServiceController::class, 'toggleStatus'])->name('medical-services.toggle-status');

        // Gestion des Spécialités
        Route::resource('specialties', SpecialtyController::class);
        Route::patch('specialties/{specialty}/toggle-status', [SpecialtyController::class, 'toggleStatus'])->name('specialties.toggle-status');

        // Gestion des Médecins
        Route::resource('doctors', DoctorController::class);
        Route::patch('doctors/{doctor}/toggle-availability', [DoctorController::class, 'toggleAvailability'])->name('doctors.toggle-availability');

        // Gestion des Patients
        Route::resource('patients', \App\Http\Controllers\BackOffice\Hospital\PatientController::class);

        // Gestion des Rendez-vous
        Route::resource('appointments', AppointmentController::class);
        Route::post('appointments/{appointment}/assign', [AppointmentController::class, 'assignDoctor'])->name('appointments.assign');
        Route::post('appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])->name('appointments.confirm');
        Route::put('appointments/{appointment}/update-status', [AppointmentController::class, 'updateStatus'])->name('appointments.update-status');

        // Planning des Médecins (Disponibilités)
        Route::get('planning', [\App\Http\Controllers\BackOffice\Hospital\DoctorScheduleController::class, 'index'])->name('schedules.index');
        Route::get('planning/{doctor}', [\App\Http\Controllers\BackOffice\Hospital\DoctorScheduleController::class, 'edit'])->name('schedules.edit');
        Route::put('planning/{doctor}', [\App\Http\Controllers\BackOffice\Hospital\DoctorScheduleController::class, 'update'])->name('schedules.update');
    });
});
