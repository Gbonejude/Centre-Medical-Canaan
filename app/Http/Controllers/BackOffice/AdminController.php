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
        // Rediriger le docteur vers son planning s'il n'a pas d'autres rôles de gestion
        if (auth()->user()->hasRole('DOCTOR') && !auth()->user()->hasAnyRole(['ADMIN', 'SUPER ADMIN', 'RECEPTIONIST'])) {
            return redirect()->route('appointments.index');
        }

        // ── Statistiques utilisateurs ──
        $doctorCount       = $this->getUserCountByPermission('DOCTOR');
        $receptionistCount = $this->getUserCountByPermission('RECEPTIONIST');
        $patientCount      = $this->getUserCountByPermission('PATIENT');

        // ── Statistiques rendez-vous ──
        $totalAppointments      = Appointment::count();
        $pendingAppointments    = Appointment::where('status', 'PENDING')->count();
        $confirmedAppointments  = Appointment::where('status', 'CONFIRMED')->count();
        $completedAppointments  = Appointment::where('status', 'COMPLETED')->count();
        $cancelledAppointments  = Appointment::where('status', 'CANCELLED')->count();

        // ── Services médicaux ──
        $servicesCount  = MedicalService::count();
        $activeServices = MedicalService::where('is_active', true)->count();

        // ── Rendez-vous d'aujourd'hui ──
        $todayAppointments = Appointment::with(['patient', 'doctor', 'medicalService'])
            ->whereDate('appointment_date', today())
            ->orderBy('appointment_time')
            ->get()
            ->map(fn($a) => [
                'id'               => $a->id,
                'patient'          => $a->patient
                    ? ($a->patient->firstname . ' ' . $a->patient->lastname)
                    : 'N/A',
                'doctor'           => $a->doctor
                    ? ('Dr. ' . $a->doctor->firstname . ' ' . $a->doctor->lastname)
                    : 'Non assigné',
                'service'          => $a->medicalService?->name ?? 'N/A',
                'appointment_time' => $a->appointment_time,
                'status_key'       => strtolower($a->status->value),
                'status'           => $a->status_label,
            ]);

        // ── Permissions pour le filtre utilisateurs ──
        $staffPermissionsMap = Permission::whereNotIn('name', ['SUPER ADMIN', 'PATIENT'])
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn($p) => [$p->name => base64_encode($p->id)]);

        return inertia('backoffice/dashboard/index', [
            'stats' => [
                'doctor_count'           => $doctorCount,
                'receptionist_count'     => $receptionistCount,
                'patient_count'          => $patientCount,
                'total_appointments'     => $totalAppointments,
                'pending_appointments'   => $pendingAppointments,
                'confirmed_appointments' => $confirmedAppointments,
                'completed_appointments' => $completedAppointments,
                'cancelled_appointments' => $cancelledAppointments,
                'services_count'         => $servicesCount,
                'active_services'        => $activeServices,
            ],
            'todayAppointments' => $todayAppointments,
            'permissionsMap'    => $staffPermissionsMap,
        ]);
    }

    private function getUserCountByPermission(string $permissionName): int
    {
        return User::excludeSystemAdmins()
            ->permission($permissionName)
            ->count();
    }
}
