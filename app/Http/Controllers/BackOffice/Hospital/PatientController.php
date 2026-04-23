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
        $query = Patient::with('user');

        if ($request->search) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('firstname', 'like', '%' . $request->search . '%')
                  ->orWhere('lastname', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        return Inertia::render('backoffice/patients/index', [
            'patients' => $query->latest()->paginate(10)->withQueryString(),
            'filters' => $request->only(['search']),
        ]);
    }

    public function show($id)
    {
        $patient = Patient::with(['user', 'appointments.doctor'])->findOrFail($id);
        
        return Inertia::render('backoffice/patients/show', [
            'patient' => $patient,
        ]);
    }

    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        $user = $patient->user;
        $patient->delete();
        if ($user) {
            $user->delete();
        }
        
        return back()->with('success', 'Patient supprimé avec succès.');
    }
}
