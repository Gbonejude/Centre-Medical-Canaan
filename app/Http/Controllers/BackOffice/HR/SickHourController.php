<?php

namespace App\Http\Controllers\BackOffice\HR;

use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Models\SickHour;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SickHourController extends Controller
{
    /**
     * Display a listing of sick hours.
     */
    public function index(Request $request)
    {
        // Get list of users excluding super admins
        $users = User::excludeSystemAdmins()->orderBy('firstname')->get();
        $userIds = $users->pluck('id')->toArray();

        $query = SickHour::with(['user', 'createdBy'])
            ->whereIn('user_id', $userIds) // Exclude super admins from sick hours records
            ->orderBy('date', 'desc');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->filled('start_date')) {
            $query->where('date', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->where('date', '<=', $request->input('end_date'));
        }

        $sickHours = $query->paginate(20);

        // Format hours for display
        $sickHours->getCollection()->transform(function ($record) {
            $record->hours_formatted = TimeHelper::hoursToReadable($record->hours);
            $record->created_by_user = $record->createdBy;

            return $record;
        });

        // Calculate total sick hours by user (excluding super admins)
        $sickHoursByUser = SickHour::query()
            ->whereIn('user_id', $userIds) // Exclude super admins
            ->when($request->filled('start_date'), fn ($q) => $q->where('date', '>=', $request->input('start_date')))
            ->when($request->filled('end_date'), fn ($q) => $q->where('date', '<=', $request->input('end_date')))
            ->selectRaw('user_id, SUM(hours) as total_hours')
            ->groupBy('user_id')
            ->get();

        // Calculate sick hours summary for each user
        $year = $request->filled('year') ? $request->input('year') : now()->year;
        $sickHoursSummary = $users->map(function ($user) use ($year) {
            $totalWorkedHours = $user->getAccruedSickHours($year) * 100; // Reverse 1% calculation
            $accruedHours = $user->getAccruedSickHours($year);
            $usedHours = $user->getUsedSickHours($year);
            $availableHours = $user->getAvailableSickHours($year);

            return [
                'user_id' => $user->id,
                'user_name' => "{$user->firstname} {$user->lastname}",
                'total_worked_hours' => round($totalWorkedHours, 2),
                'accrued_sick_hours' => $accruedHours,
                'accrued_formatted' => TimeHelper::hoursToReadable($accruedHours),
                'used_sick_hours' => $usedHours,
                'used_formatted' => TimeHelper::hoursToReadable($usedHours),
                'available_sick_hours' => $availableHours,
                'available_formatted' => TimeHelper::hoursToReadable($availableHours),
            ];
        });

        return Inertia::render('backoffice/hr/sick-hours/index', [
            'sickHours' => $sickHours,
            'users' => $users,
            'sickHoursByUser' => $sickHoursByUser,
            'sickHoursSummary' => $sickHoursSummary,
            'filters' => $request->only(['user_id', 'start_date', 'end_date', 'year']),
            'currentYear' => $year,
        ]);
    }

    /**
     * Show the form for creating new sick hours.
     */
    public function create()
    {
        $users = User::excludeSystemAdmins()->orderBy('firstname')->get();

        return Inertia::render('backoffice/hr/sick-hours/create', [
            'users' => $users,
        ]);
    }

    /**
     * Store newly created sick hours.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'hours' => 'required|numeric|min:0|max:24',
            'reason' => 'nullable|string',
            'has_medical_note' => 'boolean',
            'medical_note' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $validated['created_by'] = auth()->user()?->id;
        $validated['has_medical_note'] = $request->boolean('has_medical_note');

        $sickHour = SickHour::create($validated);

        if ($request->hasFile('medical_note')) {
            $sickHour->addMedia($request->file('medical_note'))
                ->toMediaCollection('medical_notes');
        }

        return redirect()->route('sick-hours.index')
            ->with('success', 'Sick hours recorded successfully.');
    }

    /**
     * Display the specified sick hours.
     */
    public function show(SickHour $sickHour)
    {
        $sickHour->load(['user', 'createdBy', 'media']);

        return Inertia::render('backoffice/hr/sick-hours/show', [
            'sickHour' => $sickHour,
        ]);
    }

    /**
     * Show the form for editing sick hours.
     */
    public function edit(SickHour $sickHour)
    {
        $users = User::excludeSystemAdmins()->orderBy('firstname')->get();

        return Inertia::render('backoffice/hr/sick-hours/edit', [
            'sickHour' => $sickHour,
            'users' => $users,
        ]);
    }

    /**
     * Update the specified sick hours.
     */
    public function update(Request $request, SickHour $sickHour)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'hours' => 'required|numeric|min:0|max:24',
            'reason' => 'nullable|string',
            'has_medical_note' => 'boolean',
            'medical_note' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $validated['has_medical_note'] = $request->boolean('has_medical_note');

        $sickHour->update($validated);

        if ($request->hasFile('medical_note')) {
            $sickHour->clearMediaCollection('medical_notes');
            $sickHour->addMedia($request->file('medical_note'))
                ->toMediaCollection('medical_notes');
        }

        return redirect()->route('sick-hours.index')
            ->with('success', 'Sick hours updated successfully.');
    }

    /**
     * Remove the specified sick hours.
     */
    public function destroy(SickHour $sickHour)
    {
        $sickHour->delete();

        return redirect()->route('sick-hours.index')
            ->with('success', 'Sick hours deleted successfully.');
    }

    /**
     * Get sick hours summary for a user.
     */
    public function summary(Request $request, User $user)
    {
        $year = $request->get('year', now()->year);

        $monthlySickHours = SickHour::where('user_id', $user->id)
            ->whereYear('date', $year)
            ->selectRaw('MONTH(date) as month, SUM(hours) as total_hours')
            ->groupBy('month')
            ->get()
            ->pluck('total_hours', 'month');

        $totalSickHours = SickHour::where('user_id', $user->id)
            ->whereYear('date', $year)
            ->sum('hours');

        return response()->json([
            'monthly_sick_hours' => $monthlySickHours,
            'total_sick_hours' => $totalSickHours,
            'year' => $year,
        ]);
    }
}
