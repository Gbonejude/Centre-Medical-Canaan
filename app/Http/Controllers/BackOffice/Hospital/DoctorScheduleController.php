<?php

namespace App\Http\Controllers\BackOffice\Hospital;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Inertia\Inertia;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DoctorScheduleController extends Controller implements HasMiddleware
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
        if ($user->hasPermissionTo('DOCTOR') && !$user->hasAnyPermission(['ADMIN', 'SUPER ADMIN', 'RECEPTIONIST'])) {
            $doctor = Doctor::where('user_id', $user->id)->first();
            if ($doctor) {
                return redirect()->route('schedules.edit', $doctor->uuid);
            }
        }

        $query = Doctor::with(['user', 'medicalService', 'specialty']);

        if ($request->search) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('firstname', 'like', '%' . $request->search . '%')
                  ->orWhere('lastname', 'like', '%' . $request->search . '%');
            });
        }

        return Inertia::render('backoffice/schedules/index', [
            'doctors' => $query->latest()->paginate(10)->withQueryString(),
            'filters' => $request->only(['search']),
        ]);
    }

    public function edit($uuid)
    {
        $doctor = Doctor::with('user')->where('uuid', $uuid)->firstOrFail();

        // Sécurité : Un docteur ne peut éditer que son propre planning
        if (auth()->user()->hasPermissionTo('DOCTOR') && !auth()->user()->hasAnyPermission(['ADMIN', 'SUPER ADMIN', 'RECEPTIONIST']) && $doctor->user_id !== auth()->id()) {
            abort(403, "Vous n'êtes pas autorisé à modifier le planning d'un autre médecin.");
        }

        // Initialiser l'availability si vide
        $defaultAvailability = [
            'monday'    => ['enabled' => true, 'slots' => [['start' => '07:00', 'end' => '17:00']]],
            'tuesday'   => ['enabled' => true, 'slots' => [['start' => '07:00', 'end' => '17:00']]],
            'wednesday' => ['enabled' => true, 'slots' => [['start' => '07:00', 'end' => '17:00']]],
            'thursday'  => ['enabled' => true, 'slots' => [['start' => '07:00', 'end' => '17:00']]],
            'friday'    => ['enabled' => true, 'slots' => [['start' => '07:00', 'end' => '17:00']]],
            'saturday'  => ['enabled' => false, 'slots' => []],
            'sunday'    => ['enabled' => false, 'slots' => []],
        ];

        $availability = $doctor->availability ?: $defaultAvailability;

        return Inertia::render('backoffice/schedules/edit', [
            'doctor' => $doctor,
            'availability' => $availability
        ]);
    }

    public function update(Request $request, $uuid)
    {
        $doctor = Doctor::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'availability' => 'required|array',
        ]);

        $doctor->update([
            'availability' => $validated['availability']
        ]);

        return redirect()->route('schedules.index')->with('success', 'Planning mis à jour avec succès.');
    }
}
