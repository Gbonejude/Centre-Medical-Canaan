<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MedicalService;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class AdminController extends Controller
{
    public function index()
    {
        if (auth()->user()->hasRole('DOCTOR') && ! auth()->user()->hasAnyRole(['ADMIN', 'SUPER ADMIN', 'RECEPTIONIST'])) {
            return redirect()->route('appointments.index');
        }

        $doctorCount = $this->getUserCountByPermission('DOCTOR');
        $receptionistCount = $this->getUserCountByPermission('RECEPTIONIST');
        $patientCount = $this->getUserCountByPermission('PATIENT');

        $totalAppointments = Appointment::count();
        $pendingAppointments = Appointment::where('status', 'PENDING')->count();
        $confirmedAppointments = Appointment::where('status', 'CONFIRMED')->count();
        $completedAppointments = Appointment::where('status', 'COMPLETED')->count();
        $cancelledAppointments = Appointment::where('status', 'CANCELLED')->count();

        $servicesCount = MedicalService::count();
        $activeServices = MedicalService::where('is_active', true)->count();

        $todayAppointments = Appointment::with(['patient', 'doctor', 'medicalService'])
            ->whereDate('appointment_date', today())
            ->orderBy('appointment_time')
            ->get()
            ->map(fn ($a) => [
                'id' => $a->id,
                'uuid' => $a->uuid,
                'patient' => $a->patient
                    ? ($a->patient->lastname.' '.$a->patient->firstname)
                    : 'N/A',
                'doctor' => $a->doctor
                    ? ('Dr. '.$a->doctor->lastname)
                    : 'Non assigné',
                'service' => $a->medicalService?->name ?? 'N/A',
                'time' => $a->appointment_time ? substr($a->appointment_time, 0, 5) : '--:--',
                'status_key' => strtolower($a->status->value),
                'status' => $a->status->label(),
            ]);

        // --- Graphiques ---
        // 1. Tendance (7 derniers jours)
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
            $trends[] = ['date' => $date, 'count' => $rawTrends[$date] ?? 0];
        }

        // 2. Répartition par Service
        $servicesStats = Appointment::selectRaw('medical_services.name as label, COUNT(appointments.id) as count')
            ->join('medical_services', 'appointments.medical_service_id', '=', 'medical_services.id')
            ->groupBy('medical_services.name')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->map(fn($item) => ['label' => $item->label, 'count' => $item->count]);

        // 3. Top Médecins
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

        // 4. Répartition par Statut (Pour Donut)
        $statusDistribution = Appointment::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->map(fn($item) => [
                'label' => $item->status->label(),
                'status_key' => strtolower($item->status->value),
                'count' => $item->count
            ]);

        // 5. Activité par Heure (Pic d'affluence)
        $hourlyStats = Appointment::selectRaw('HOUR(appointment_time) as hour, COUNT(*) as count')
            ->whereNotNull('appointment_time')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->map(fn($item) => [
                'hour' => str_pad($item->hour, 2, '0', STR_PAD_LEFT) . 'h',
                'count' => $item->count
            ]);

        $staffPermissionsMap = Permission::whereNotIn('name', ['SUPER ADMIN', 'PATIENT'])
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn ($p) => [$p->name => base64_encode($p->id)]);

        return inertia('backoffice/dashboard/index', [
            'stats' => [
                'total_doctors' => $doctorCount,
                'total_patients' => $patientCount,
                'total_services' => $activeServices,
                'total_appointments' => $totalAppointments,
                'pending_appointments' => $pendingAppointments,
                'confirmed_appointments' => $confirmedAppointments,
                'completed_appointments' => $completedAppointments,
                'cancelled_appointments' => $cancelledAppointments,
            ],
            'charts' => [
                'trends' => $trends,
                'services' => $servicesStats,
                'top_doctors' => $topDoctors,
                'statuses' => $statusDistribution,
                'hourly' => $hourlyStats,
            ],
            'todayAppointments' => $todayAppointments,
            'permissionsMap' => $staffPermissionsMap,
        ]);
    }

    private function getUserCountByPermission(string $permissionName): int
    {
        return User::excludeSystemAdmins()
            ->permission($permissionName)
            ->count();
    }
}
