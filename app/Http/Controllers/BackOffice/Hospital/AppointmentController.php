<?php

namespace App\Http\Controllers\BackOffice\Hospital;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hospital\AssignDoctorRequest;
use App\Http\Requests\Hospital\StoreAppointmentRequest;
use App\Http\Requests\Hospital\UpdateAppointmentFrontRequest;
use App\Http\Requests\Hospital\UpdateAppointmentStatusRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;

class AppointmentController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ADMIN|SUPER ADMIN|RECEPTIONIST|DOCTOR', except: ['create', 'store', 'mine', 'editFront', 'updateFront', 'cancelFront']),
        ];
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $isDoctor = $user->hasPermissionTo('DOCTOR') && ! $user->hasAnyPermission(['ADMIN', 'SUPER ADMIN', 'RECEPTIONIST']);

        $query = Appointment::with(['patient', 'doctor', 'medicalService']);

        if ($isDoctor) {
            $query->where('doctor_id', $user->id);
        }

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
            'appointments' => $query->latest()->get(),
            'stats' => $stats,
            'isDoctor' => $isDoctor,
            'availableDoctors' => Doctor::with(['user', 'specialty', 'medicalService'])->where('is_available', true)->get(),
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

    public function store(StoreAppointmentRequest $request)
    {
        $validated = $request->validated();

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
            'appointments' => $appointments,
        ]);
    }

    public function assignDoctor(AssignDoctorRequest $request, $uuid)
    {
        $appointment = Appointment::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validated();

        $doctor = Doctor::with('user')->findOrFail($validated['doctor_id']);

        $isBusy = Appointment::where('doctor_id', $doctor->user_id)
            ->where('appointment_date', $appointment->appointment_date)
            ->where('appointment_time', $appointment->appointment_time)
            ->whereIn('status', ['PENDING', 'CONFIRMED'])
            ->where('id', '!=', $appointment->id)
            ->exists();

        if ($isBusy) {
            return back()->with('error', "Le Dr. {$doctor->user->lastname} a déjà un rendez-vous programmé à cette date et heure.");
        }

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
        $isDoctor = $user->hasPermissionTo('DOCTOR') && ! $user->hasAnyPermission(['ADMIN', 'SUPER ADMIN', 'RECEPTIONIST']);

        $appointment->load(['patient', 'doctor', 'medicalService']);

        $busyDoctorIds = Appointment::where('appointment_date', $appointment->appointment_date)
            ->where('appointment_time', $appointment->appointment_time)
            ->whereIn('status', ['PENDING', 'CONFIRMED'])
            ->where('id', '!=', $appointment->id)
            ->pluck('doctor_id')
            ->toArray();

        $doctors = Doctor::with(['user', 'specialty', 'medicalService'])
            ->where('is_available', true)
            ->when($appointment->medicalService->name !== 'Autres', function ($q) use ($appointment) {
                return $q->where('medical_service_id', $appointment->medical_service_id);
            })
            ->get();

        return Inertia::render('backoffice/appointments/show', [
            'appointment' => $appointment,
            'isDoctor' => $isDoctor,
            'doctors' => $doctors,
            'busyDoctorIds' => $busyDoctorIds,
        ]);
    }

    public function updateStatus(UpdateAppointmentStatusRequest $request, Appointment $appointment)
    {
        $validated = $request->validated();

        $user = auth()->user();
        $isDoctorOnly = $user->hasPermissionTo('DOCTOR') && ! $user->hasAnyPermission(['ADMIN', 'SUPER ADMIN', 'RECEPTIONIST']);

        if ($isDoctorOnly && in_array($validated['status'], ['CANCELLED', 'POSTPONED'])) {
            return back()->with('error', "Vous n'êtes pas autorisé à annuler ou reporter un rendez-vous. Veuillez contacter la réception.");
        }

        $updateData = ['status' => $validated['status']];

        if ($validated['status'] === 'POSTPONED') {
            if ($validated['appointment_date']) {
                $updateData['appointment_date'] = $validated['appointment_date'];
            }
            if ($validated['appointment_time']) {
                $updateData['appointment_time'] = $validated['appointment_time'];
            }
            $updateData['doctor_id'] = null;
        }

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

    public function cancelFront(Request $request, Appointment $appointment)
    {
        $user = auth('guest')->user();

        if (! $user || $appointment->patient_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        if (in_array($appointment->status->value, ['COMPLETED', 'CANCELLED'])) {
            return back()->with('error', 'Ce rendez-vous ne peut plus être annulé.');
        }

        $appointment->update(['status' => 'CANCELLED']);

        return back()->with('success', 'Votre rendez-vous a été annulé avec succès.');
    }

    public function editFront(Request $request, Appointment $appointment)
    {
        $user = auth('guest')->user();

        if (! $user || $appointment->patient_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        if (in_array($appointment->status->value, ['COMPLETED', 'CANCELLED'])) {
            return redirect()->route('front.appointments.mine')->with('error', 'Ce rendez-vous ne peut plus être modifié.');
        }

        return Inertia::render('frontoffice/appointments/edit', [
            'appointment' => $appointment,
            'services' => MedicalService::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function updateFront(UpdateAppointmentFrontRequest $request, Appointment $appointment)
    {
        $user = auth('guest')->user();

        if (! $user || $appointment->patient_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        if (in_array($appointment->status->value, ['COMPLETED', 'CANCELLED'])) {
            return back()->with('error', 'Ce rendez-vous ne peut plus être modifié.');
        }

        $validated = $request->validated();

        $appointment->update($validated);

        return redirect()->route('front.appointments.mine')->with('success', 'Votre rendez-vous a été modifié avec succès.');
    }

    public function destroyFront(Request $request, Appointment $appointment)
    {
        $user = auth('guest')->user();

        if (! $user || $appointment->patient_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        if ($appointment->status !== 'CANCELLED') {
            return back()->with('error', 'Seuls les rendez-vous annulés peuvent être supprimés.');
        }

        $appointment->delete();

        return back()->with('success', 'Le rendez-vous a été supprimé de votre historique.');
    }
}
