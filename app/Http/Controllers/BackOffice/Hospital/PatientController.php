<?php

namespace App\Http\Controllers\BackOffice\Hospital;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PatientController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ADMIN|SUPER ADMIN|RECEPTIONIST'),
        ];
    }

    public function index(Request $request)
    {
        return Inertia::render('backoffice/patients/index', [
            'patients' => Patient::with('user')->latest()->get(),
        ]);
    }

    public function show($uuid)
    {
        $patient = Patient::with(['user', 'appointments.doctor'])->where('uuid', $uuid)->firstOrFail();
        
        return Inertia::render('backoffice/patients/show', [
            'patient' => $patient,
        ]);
    }

    public function destroy($uuid)
    {
        $patient = Patient::where('uuid', $uuid)->firstOrFail();
        $user = $patient->user;
        $patient->delete();
        if ($user) {
            $user->delete();
        }
        
        return back()->with('success', 'Patient supprimé avec succès.');
    }
}
