<?php

namespace App\Http\Controllers\BackOffice\Hospital;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\StoreDoctorRequest;
use App\Http\Requests\Doctor\UpdateDoctorRequest;
use App\Mail\NewUserMail;
use App\Models\Doctor;
use App\Models\MedicalService;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class DoctorController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ADMIN|SUPER ADMIN|RECEPTIONIST'),
        ];
    }

    public function index(Request $request)
    {
        return Inertia::render('backoffice/doctors/index', [
            'doctors' => Doctor::with(['user', 'medicalService', 'specialties'])->latest()->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('backoffice/doctors/create', [
            'services' => MedicalService::where('is_active', true)->get(),
            'specialties' => Specialty::where('is_active', true)->get(),
        ]);
    }

    public function store(StoreDoctorRequest $request)
    {
        $validated = $request->validated();

        $passwordGenerate = $this->generatePassword();

        DB::transaction(function () use ($validated, $passwordGenerate) {
            $user = User::create([
                'firstname' => $validated['firstname'],
                'lastname' => $validated['lastname'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'birthday' => $validated['birthday'] ?? null,
                'password' => Hash::make($passwordGenerate),
                'email_verified_at' => now(),
            ]);

            $user->givePermissionTo('DOCTOR');

            $doctor = Doctor::create([
                'user_id' => $user->id,
                'medical_service_id' => $validated['service_id'],
                'specialty_id' => $validated['specialty_ids'][0],
                'bio' => $validated['bio'],
                'is_available' => true,
            ]);

            $doctor->specialties()->sync($validated['specialty_ids']);

            Mail::to($user->email)->send(new NewUserMail($user->email, $passwordGenerate));
        });

        return redirect()->route('doctors.index')->with('success', 'Médecin ajouté avec succès.');
    }

    public function show($uuid)
    {
        $doctor = Doctor::with(['user', 'medicalService', 'specialties'])->where('uuid', $uuid)->firstOrFail();

        return Inertia::render('backoffice/doctors/show', [
            'doctor' => $doctor,
            'userAvatar' => $doctor->user->hasMedia('user') ? $doctor->user->getFirstMediaUrl('user') : null,
        ]);
    }

    public function edit($uuid)
    {
        $doctor = Doctor::with(['user', 'medicalService', 'specialties'])->where('uuid', $uuid)->firstOrFail();

        return Inertia::render('backoffice/doctors/edit', [
            'doctor' => $doctor,
            'services' => MedicalService::where('is_active', true)->get(),
            'specialties' => Specialty::where('is_active', true)->get(),
        ]);
    }

    public function update(UpdateDoctorRequest $request, $uuid)
    {
        $doctor = Doctor::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validated();

        DB::transaction(function () use ($validated, $doctor) {
            $doctor->user->update([
                'firstname' => $validated['firstname'],
                'lastname' => $validated['lastname'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? $doctor->user->phone,
                'gender' => $validated['gender'] ?? $doctor->user->gender,
                'birthday' => $validated['birthday'] ?? $doctor->user->birthday,
            ]);

            if (! $doctor->user->hasPermissionTo('DOCTOR')) {
                $doctor->user->givePermissionTo('DOCTOR');
            }

            $doctor->update([
                'medical_service_id' => $validated['service_id'],
                'specialty_id' => $validated['specialty_ids'][0],
                'bio' => $validated['bio'],
                'is_available' => $validated['is_available'] ?? true,
            ]);

            $doctor->specialties()->sync($validated['specialty_ids']);
        });

        return redirect()->route('doctors.index')->with('success', 'Informations du médecin mises à jour.');
    }

    public function destroy($uuid)
    {
        $doctor = Doctor::where('uuid', $uuid)->firstOrFail();
        $user = $doctor->user;

        DB::transaction(function () use ($doctor, $user) {
            $doctor->delete();
            if ($user) {
                $user->revokePermissionTo('DOCTOR');
                $user->delete();
            }
        });

        return back()->with('success', 'Médecin supprimé avec succès.');
    }

    public function toggleAvailability($uuid)
    {
        $doctor = Doctor::with('user')->where('uuid', $uuid)->firstOrFail();
        $doctor->is_available = ! $doctor->is_available;
        $doctor->save();

        $status = $doctor->is_available ? 'disponible' : 'indisponible';

        return back()->with('success', "Le Dr. {$doctor->user->lastname} est désormais {$status}.");
    }

    protected function generatePassword(): string
    {
        return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);
    }
}
