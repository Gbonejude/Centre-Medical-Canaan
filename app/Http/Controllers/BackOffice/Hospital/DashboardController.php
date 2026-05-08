<?php

namespace App\Http\Controllers\BackOffice\Hospital;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
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
        $totalPatients = Patient::count();
        $totalDoctors = Doctor::count();
        $totalServices = \App\Models\MedicalService::where('is_active', true)->count();
        $totalAppointments = Appointment::count();
        $pendingAppointments = Appointment::where('status', 'PENDING')->count();

        // --- 2. Graphique Tendance (Derniers 7 jours avec remplissage des zéros) ---
        $startDate = now()->subDays(6)->startOfDay();
        $endDate = now()->endOfDay();
        
        $rawTrends = Appointment::selectRaw('DATE(appointment_date) as date, COUNT(*) as count')
            ->whereBetween('appointment_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $trends = [];
        for ($i = 0; $i < 7; $i++) {
            $date = now()->subDays(6 - $i)->toDateString();
            $trends[] = [
                'date' => $date,
                'count' => $rawTrends[$date] ?? 0
            ];
        }

        // --- 3. Graphique Répartition par Service (Tous les RDV pour avoir de la donnée au début) ---
        $servicesStats = Appointment::selectRaw('medical_services.name as label, COUNT(appointments.id) as count')
            ->join('medical_services', 'appointments.medical_service_id', '=', 'medical_services.id')
            ->groupBy('medical_services.name')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->map(fn($item) => ['label' => $item->label, 'count' => $item->count]);

        // --- 4. Graphique Top Médecins ---
        $topDoctors = Appointment::selectRaw('users.firstname, users.lastname, COUNT(appointments.id) as count')
            ->join('users', 'appointments.doctor_id', '=', 'users.id')
            ->groupBy('users.id', 'users.firstname', 'users.lastname')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->map(fn($item) => [
                'label' => "Dr. {$item->lastname}",
                'count' => $item->count
            ]);

        // --- 5. RDV du Jour (Liste) ---
        $todayAppointments = Appointment::with(['patient', 'doctor', 'medicalService'])
            ->where('appointment_date', now()->toDateString())
            ->orderBy('appointment_time')
            ->get()
            ->map(function ($app) {
                return [
                    'id' => $app->id,
                    'uuid' => $app->uuid,
                    'time' => $app->appointment_time ? substr($app->appointment_time, 0, 5) : '--:--',
                    'patient' => ($app->patient->lastname ?? '') . ' ' . ($app->patient->firstname ?? ''),
                    'doctor' => $app->doctor ? 'Dr. ' . $app->doctor->lastname : 'Non affecté',
                    'service' => $app->medicalService->name ?? 'N/A',
                    'status' => $app->status->label(),
                    'status_key' => strtolower($app->status->value),
                ];
            });

        return Inertia::render('backoffice/dashboard/index', [
            'stats' => [
                'total_patients' => $totalPatients,
                'total_doctors' => $totalDoctors,
                'total_services' => $totalServices,
                'total_appointments' => $totalAppointments,
                'pending_appointments' => $pendingAppointments,
            ],
            'charts' => [
                'trends' => $trends,
                'services' => $servicesStats,
                'top_doctors' => $topDoctors,
            ],
            'todayAppointments' => $todayAppointments,
        ]);
    }

    protected function doctorDashboard()
    {
        $user = auth()->user();

        return Inertia::render('backoffice/hospital/doctor_dashboard', [
            'today_appointments' => Appointment::with(['patient', 'medicalService'])
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
            ],
        ]);
    }

    protected function receptionistDashboard()
    {
        return Inertia::render('backoffice/hospital/receptionist_dashboard', [
            'pending_requests' => Appointment::with(['patient', 'medicalService'])
                ->where('status', 'PENDING')
                ->latest()
                ->get(),
            'doctors_on_duty' => Doctor::with('user', 'medicalService')->where('is_available', true)->get(),
        ]);
    }

    protected function patientDashboard()
    {
        return redirect()->route('appointments.mine');
    }
}
