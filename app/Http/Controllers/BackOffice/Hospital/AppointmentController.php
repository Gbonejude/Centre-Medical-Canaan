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
            new Middleware('permission:ADMIN|SUPER ADMIN|RECEPTIONIST|DOCTOR', except: ['create', 'store', 'mine']),
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
            'appointments'     => $query->latest()->get(),
            'stats'            => $stats,
            'isDoctor'         => $isDoctor,
            'availableDoctors' => Doctor::with(['user', 'specialty'])->where('is_available', true)->get(),
        ]);
    }

    public function create(Request $request)
    {
        return Inertia::render('frontoffice/appointments/create', [
            'services' => MedicalService::where('is_active', true)->orderBy('name')->get(),
            'selectedServiceId' => $request->query('service_id'),
            'selectedDate' => $request->query('date'),
            'selectedTime' => $request->query('time'),
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

        $appointment = Appointment::create([
            'patient_id' => auth()->id() ?? auth()->guard('guest')->id(),
            'medical_service_id' => $validated['medical_service_id'],
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
            'reason' => $validated['reason'],
            'status' => 'PENDING',
        ]);

        dispatch(new \App\Jobs\SendAppointmentEmailJob($appointment, 'requested_patient'));
        dispatch(new \App\Jobs\SendAppointmentEmailJob($appointment, 'new_request_staff'));

        return redirect()->route('front.appointments.mine')->with('success', 'Votre demande de rendez-vous a été envoyée.');
    }

    public function mine()
    {
        $patientId = auth()->id() ?? auth()->guard('guest')->id();

        $appointments = Appointment::with(['medicalService', 'doctor'])
            ->where('patient_id', $patientId)
            ->latest()
            ->get();

        return Inertia::render('frontoffice/appointments/index', [
            'appointments' => $appointments
        ]);
    }

    public function assignDoctor(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'notes' => 'nullable|string',
            'receptionist_notes' => 'nullable|string',
        ]);

        $doctor = Doctor::with('user')->findOrFail($validated['doctor_id']);
        $notes = $validated['notes'] ?? $validated['receptionist_notes'] ?? null;

        $appointment->update([
            'doctor_id' => $doctor->user_id,
            'receptionist_notes' => $notes,
            'status' => 'CONFIRMED',
            'confirmed_at' => now(),
        ]);

        dispatch(new \App\Jobs\SendAppointmentEmailJob($appointment, 'status_changed'));
        dispatch(new \App\Jobs\SendAppointmentEmailJob($appointment, 'assigned_doctor'));

        return back()->with('success', "Le Dr. {$doctor->user->lastname} a été affecté au rendez-vous.");
    }

    public function show(Appointment $appointment)
    {
        $user = auth()->user();
        $isDoctor = $user->hasRole('DOCTOR') && !$user->hasAnyRole(['ADMIN', 'SUPER ADMIN', 'RECEPTIONIST']);

        $appointment->load(['patient', 'doctor', 'medicalService']);
        return Inertia::render('backoffice/appointments/show', [
            'appointment' => $appointment,
            'isDoctor' => $isDoctor,
            'doctors' => Doctor::with('user')->where('medical_service_id', $appointment->medical_service_id)->where('is_available', true)->get(),
        ]);
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'status' => 'required|in:COMPLETED,CANCELLED,POSTPONED',
            'notes' => 'nullable|string',
        ]);

        $user = auth()->user();
        $isDoctorOnly = $user->hasRole('DOCTOR') && !$user->hasAnyRole(['ADMIN', 'SUPER ADMIN', 'RECEPTIONIST']);

        if ($isDoctorOnly && in_array($validated['status'], ['CANCELLED', 'POSTPONED'])) {
            return back()->with('error', "Vous n'êtes pas autorisé à annuler ou reporter un rendez-vous. Veuillez contacter la réception.");
        }

        $updateData = ['status' => $validated['status']];
        $notes = $validated['notes'] ?? null;

        if ($isDoctorOnly) {
            $updateData['doctor_notes'] = $notes;
        } else {
            $updateData['receptionist_notes'] = $notes;
        }

        $appointment->update($updateData);

        dispatch(new \App\Jobs\SendAppointmentEmailJob($appointment, 'status_changed'));

        return back()->with('success', 'Statut du rendez-vous mis à jour.');
    }
}
