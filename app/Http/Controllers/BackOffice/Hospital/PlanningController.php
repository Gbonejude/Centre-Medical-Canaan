<?php

namespace App\Http\Controllers\BackOffice\Hospital;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlanningController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('fr');

        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        // We fetch a range spanning from the beginning of the previous month to the end of the next month
        // to cover edge days in the calendar grid.
        $targetDate = Carbon::createFromDate($year, $month, 1);
        $startDate = $targetDate->copy()->subMonth()->startOfMonth();
        $endDate = $targetDate->copy()->addMonth()->endOfMonth();

        $user = auth()->user();
        $isDoctorOnly = $user->hasPermissionTo('DOCTOR') && ! $user->hasAnyPermission(['ADMIN', 'SUPER ADMIN', 'RECEPTIONIST']);

        $doctorsQuery = Doctor::with(['user', 'medicalService', 'specialty']);

        if ($isDoctorOnly) {
            $doctorsQuery->where('user_id', $user->id);
        }

        $doctors = $doctorsQuery->latest()->get();

        $doctorIds = $doctors->pluck('user_id')->toArray();
        $appointments = Appointment::with(['patient', 'doctor'])
            ->whereIn('doctor_id', $doctorIds)
            ->whereBetween('appointment_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->whereIn('status', ['PENDING', 'CONFIRMED', 'COMPLETED', 'NO_SHOW'])
            ->get()
            ->groupBy('doctor_id')
            ->map(fn ($group) => $group->groupBy(
                fn ($app) => \Carbon\Carbon::parse($app->appointment_date)->format('Y-m-d')
            ));

        $services = MedicalService::where('is_active', true)->orderBy('name')->get();

        return Inertia::render('backoffice/planning/index', [
            'doctors' => $doctors,
            'appointments' => $appointments,
            'services' => $services,
            'isDoctor' => $isDoctorOnly,
            'initialDate' => now()->format('Y-m-d'),
        ]);
    }
}
