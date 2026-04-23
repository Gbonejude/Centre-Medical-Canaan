<?php

namespace App\Http\Controllers\BackOffice\Hospital;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalService;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->hasRole('SUPER ADMIN') || $user->hasRole('HOSPITAL ADMIN')) {
            return $this->adminDashboard();
        }

        if ($user->hasRole('DOCTOR')) {
            return $this->doctorDashboard();
        }

        if ($user->hasRole('RECEPTIONIST')) {
            return $this->receptionistDashboard();
        }

        if ($user->hasRole('PATIENT')) {
            return $this->patientDashboard();
        }

        return redirect()->route('dashboard.index');
    }

    protected function adminDashboard()
    {
        return Inertia::render('backoffice/dashboard/index', [
            'stats' => [
                'total_patients' => Patient::count(),
                'total_doctors' => Doctor::count(),
                'total_appointments' => Appointment::count(),
                'pending_appointments' => Appointment::where('status', 'PENDING')->count(),
            ],
            'recent_appointments' => Appointment::with(['patient', 'doctor', 'service'])
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }

    protected function doctorDashboard()
    {
        $user = auth()->user();
        return Inertia::render('backoffice/hospital/doctor_dashboard', [
            'today_appointments' => Appointment::with(['patient', 'service'])
                ->where('doctor_id', $user->id)
                ->where('appointment_date', now()->toDateString())
                ->orderBy('appointment_time')
                ->get(),
            'stats' => [
                'total_appointments' => Appointment::where('doctor_id', $user->id)->count(),
                'completed_this_month' => Appointment::where('doctor_id', $user->id)
                    ->where('status', 'COMPLETED')
                    ->whereMonth('appointment_date', now()->month)
                    ->count(),
            ]
        ]);
    }

    protected function receptionistDashboard()
    {
        return Inertia::render('backoffice/hospital/receptionist_dashboard', [
            'pending_requests' => Appointment::with(['patient', 'service'])
                ->where('status', 'PENDING')
                ->latest()
                ->get(),
            'doctors_on_duty' => Doctor::with('user', 'service')->where('is_available', true)->get(),
        ]);
    }

    protected function patientDashboard()
    {
        return redirect()->route('appointments.mine');
    }
}
