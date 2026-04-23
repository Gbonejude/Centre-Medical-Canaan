<?php

namespace App\Http\Controllers\BackOffice\Hospital;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalService;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AppointmentController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ADMIN|SUPER ADMIN|RECEPTIONIST|DOCTOR'),
        ];
    }
    public function index(Request $request)
    {
        $user = auth()->user();
        $isDoctor = $user->hasRole('DOCTOR') && !$user->hasAnyRole(['ADMIN', 'SUPER ADMIN', 'RECEPTIONIST']);

        $query = Appointment::with(['patient', 'doctor', 'medicalService']);

        // Si c'est un docteur, on filtre uniquement ses rendez-vous
        if ($isDoctor) {
            $query->where('doctor_id', $user->id);
        }

        // Filtrage par onglet (tab)
        if ($request->tab === 'requests') {
            $query->whereNull('doctor_id')->where('status', 'PENDING');
        } elseif ($request->tab === 'scheduled') {
            $query->whereNotNull('doctor_id')->whereIn('status', ['PENDING', 'CONFIRMED']);
        } elseif ($request->tab === 'history') {
            $query->whereIn('status', ['COMPLETED', 'CANCELLED']);
        }

        if ($request->search) {
            $query->whereHas('patient', function($q) use ($request) {
                $q->where('firstname', 'like', '%' . $request->search . '%')
                  ->orWhere('lastname', 'like', '%' . $request->search . '%');
            });
        }

        // Statistiques adaptées au rôle
        $statsQuery = Appointment::query();
        if ($isDoctor) {
            $statsQuery->where('doctor_id', $user->id);
        }

        $stats = [
            'requests' => (clone $statsQuery)->whereNull('doctor_id')->where('status', 'PENDING')->count(),
            'scheduled' => (clone $statsQuery)->whereNotNull('doctor_id')->whereIn('status', ['PENDING', 'CONFIRMED'])->count(),
            'history' => (clone $statsQuery)->whereIn('status', ['COMPLETED', 'CANCELLED'])->count(),
        ];

        return Inertia::render('backoffice/appointments/index', [
            'appointments' => $query->latest()->paginate(10)->withQueryString(),
            'filters' => $request->only(['tab', 'search']),
            'stats' => $stats,
            'isDoctor' => $isDoctor,
            'availableDoctors' => Doctor::with(['user', 'specialty'])->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('frontoffice/appointments/create', [
            'services' => MedicalService::where('is_active', true)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'medical_service_id' => 'required|exists:medical_services,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'reason' => 'nullable|string',
        ]);

        Appointment::create([
            'patient_id' => auth()->id(),
            'medical_service_id' => $validated['medical_service_id'],
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
            'reason' => $validated['reason'],
            'status' => 'PENDING',
        ]);

        return redirect()->route('appointments.mine')->with('success', 'Votre demande de rendez-vous a été envoyée.');
    }

    public function assignDoctor(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'notes' => 'nullable|string',
        ]);

        $doctor = Doctor::with('user')->findOrFail($validated['doctor_id']);

        $appointment->update([
            'doctor_id' => $doctor->user_id,
            'receptionist_notes' => $validated['notes'],
            'status' => 'CONFIRMED',
            'confirmed_at' => now(),
        ]);

        return back()->with('success', "Le Dr. {$doctor->user->lastname} a été affecté au rendez-vous.");
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor', 'medicalService']);
        return Inertia::render('backoffice/appointments/show', [
            'appointment' => $appointment,
            'doctors' => Doctor::with('user')->where('medical_service_id', $appointment->medical_service_id)->get(),
        ]);
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'status' => 'required|in:COMPLETED,CANCELLED,POSTPONED',
            'notes' => 'nullable|string',
        ]);

        $updateData = ['status' => $validated['status']];
        $notes = $validated['notes'] ?? null;

        if (auth()->user() && auth()->user()->hasRole('DOCTOR')) {
            $updateData['doctor_notes'] = $notes;
        } else {
            $updateData['receptionist_notes'] = $notes;
        }

        $appointment->update($updateData);

        return back()->with('success', 'Statut du rendez-vous mis à jour.');
    }
}
