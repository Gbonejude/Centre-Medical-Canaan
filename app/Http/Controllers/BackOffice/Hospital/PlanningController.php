<?php

namespace App\Http\Controllers\BackOffice\Hospital;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class PlanningController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('fr');
        
        // 1. Déterminer la semaine à afficher
        $dateStr = $request->input('date', now()->format('Y-m-d'));
        $currentDate = Carbon::parse($dateStr)->startOfWeek(); // Lundi
        $endDate = (clone $currentDate)->endOfWeek(); // Dimanche

        // 2. Récupérer les docteurs avec leurs disponibilités
        $user = auth()->user();
        $isDoctorOnly = $user->hasPermissionTo('DOCTOR') && !$user->hasAnyPermission(['ADMIN', 'SUPER ADMIN', 'RECEPTIONIST']);

        $doctorsQuery = Doctor::with(['user', 'medicalService', 'specialty']);

        // Si c'est un docteur seul, on restreint à son propre planning
        if ($isDoctorOnly) {
            $doctorsQuery->where('user_id', $user->id);
        }

        // Filtres
        if ($request->search && !$isDoctorOnly) {
            $doctorsQuery->whereHas('user', function($q) use ($request) {
                $q->where('firstname', 'like', '%' . $request->search . '%')
                  ->orWhere('lastname', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->service_id && !$isDoctorOnly) {
            $doctorsQuery->where('medical_service_id', $request->service_id);
        }

        $perPageRaw = $request->input('per_page', 10);
        $perPage = ($perPageRaw === 'all') ? 1000 : (int)$perPageRaw;
        
        $doctors = $doctorsQuery->latest()->paginate($perPage)->withQueryString();

        // 3. Récupérer les rendez-vous de la semaine pour les docteurs affichés
        $doctorIds = $doctors->pluck('user_id')->toArray();
        $appointments = Appointment::with(['patient', 'doctor'])
            ->whereIn('doctor_id', $doctorIds)
            ->whereBetween('appointment_date', [$currentDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->whereIn('status', ['PENDING', 'CONFIRMED', 'COMPLETED'])
            ->get()
            ->groupBy(['doctor_id', 'appointment_date']);

        // 4. Services pour le filtre
        $services = MedicalService::where('is_active', true)->orderBy('name')->get();

        return Inertia::render('backoffice/planning/index', [
            'doctors' => $doctors,
            'appointments' => $appointments,
            'services' => $services,
            'isDoctor' => $isDoctorOnly,
            'currentWeek' => [
                'start' => $currentDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
                'display' => "Du " . $currentDate->translatedFormat('d M') . " au " . $endDate->translatedFormat('d M Y'),
            ],
            'filters' => $request->only(['search', 'service_id', 'date', 'per_page']),
        ]);
    }
}
