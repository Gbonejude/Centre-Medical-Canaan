<?php

namespace App\Http\Controllers\BackOffice\HR;

use App\Http\Controllers\Controller;
use App\Models\Bonus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BonusController extends Controller
{
    /**
     * Display a listing of bonuses.
     */
    public function index(Request $request)
    {
        $query = Bonus::with(['user', 'approvedBy'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Period filters
        if ($request->filled('period_start')) {
            $query->where(function ($q) use ($request) {
                $q->where('period_start', '>=', $request->period_start)
                    ->orWhere('date', '>=', $request->period_start);
            });
        }

        if ($request->filled('period_end')) {
            $query->where(function ($q) use ($request) {
                $q->where('period_end', '<=', $request->period_end)
                    ->orWhere('date', '<=', $request->period_end);
            });
        }

        // Month and year filter
        if ($request->filled('month') && $request->filled('year')) {
            $query->where(function ($q) use ($request) {
                $q->whereMonth('period_start', $request->month)
                    ->whereYear('period_start', $request->year)
                    ->orWhere(function ($q2) use ($request) {
                        $q2->whereMonth('date', $request->month)
                            ->whereYear('date', $request->year);
                    });
            });
        }

        $bonuses = $query->paginate(20);
        $users = User::excludeSystemAdmins()->orderBy('firstname')->get();

        return Inertia::render('backoffice/hr/bonuses/index', [
            'bonuses' => $bonuses,
            'users' => $users,
            'filters' => $request->only(['status', 'user_id', 'period_start', 'period_end', 'month', 'year']),
        ]);
    }

    /**
     * Show the form for creating a new bonus.
     */
    public function create()
    {
        $users = User::excludeSystemAdmins()->orderBy('firstname')->get();

        return Inertia::render('backoffice/hr/bonuses/create', [
            'users' => $users,
        ]);
    }

    /**
     * Store a newly created bonus.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'nullable|numeric|min:0',
            'date' => 'required|date',
            'period_start' => 'nullable|date',
            'period_end' => 'nullable|date|after_or_equal:period_start',
            'calculation_type' => 'nullable|in:manual,automatic',
            'total_hours' => 'nullable|numeric|min:0',
            'bonus_percentage' => 'nullable|numeric|min:0|max:1',
        ]);

        $calculationType = $validated['calculation_type'] ?? 'manual';

        if ($calculationType === 'automatic') {
            // Get user and calculate
            $user = User::findOrFail($validated['user_id']);
            $totalHours = $validated['total_hours'] ?? $user->getTotalHoursForPeriod(
                $validated['period_start'],
                $validated['period_end']
            );

            $bonusPercentage = $validated['bonus_percentage'] ?? $user->bonus_percentage ?? 0.25;

            $validated['total_hours'] = $totalHours;
            $validated['bonus_percentage'] = $bonusPercentage;
            $validated['amount'] = round($totalHours * $bonusPercentage, 2);
            $validated['calculation_type'] = 'automatic';
        } else {
            $validated['calculation_type'] = 'manual';
        }

        Bonus::create($validated);

        return redirect()->route('bonuses.index')
            ->with('success', 'Bonus created successfully.');
    }

    /**
     * Display the specified bonus.
     */
    public function show(Bonus $bonus)
    {
        $bonus->load(['user', 'approvedBy']);

        return Inertia::render('backoffice/hr/bonuses/show', [
            'bonus' => $bonus,
        ]);
    }

    /**
     * Show the form for editing the specified bonus.
     */
    public function edit(Bonus $bonus)
    {
        if ($bonus->status !== 'pending') {
            return redirect()->route('bonuses.show', $bonus)
                ->with('error', 'Only pending bonuses can be edited.');
        }

        $users = User::excludeSystemAdmins()->orderBy('firstname')->get();

        return Inertia::render('backoffice/hr/bonuses/edit', [
            'bonus' => $bonus,
            'users' => $users,
        ]);
    }

    /**
     * Update the specified bonus.
     */
    public function update(Request $request, Bonus $bonus)
    {
        if ($bonus->status !== 'pending') {
            return redirect()->route('bonuses.show', $bonus)
                ->with('error', 'Only pending bonuses can be updated.');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
        ]);

        $bonus->update($validated);

        return redirect()->route('bonuses.index')
            ->with('success', 'Bonus updated successfully.');
    }

    /**
     * Approve a bonus.
     */
    public function approve(Bonus $bonus)
    {
        if ($bonus->status !== 'pending') {
            return back()->with('error', 'Only pending bonuses can be approved.');
        }

        $bonus->update([
            'status' => 'approved',
            'approved_by' => Auth::user()->id,
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Bonus approved successfully.');
    }

    /**
     * Reject a bonus.
     */
    public function reject(Request $request, Bonus $bonus)
    {
        if ($bonus->status !== 'pending') {
            return back()->with('error', 'Only pending bonuses can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $bonus->update([
            'status' => 'rejected',
            'approved_by' => Auth::user()->id,
            'approved_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return back()->with('success', 'Bonus rejected.');
    }

    /**
     * Remove the specified bonus.
     */
    public function destroy(Bonus $bonus)
    {
        if ($bonus->status !== 'pending') {
            return back()->with('error', 'Only pending bonuses can be deleted.');
        }

        $bonus->delete();

        return redirect()->route('bonuses.index')
            ->with('success', 'Bonus deleted successfully.');
    }

    /**
     * Calculate bonus preview for automatic calculation.
     */
    public function calculateBonus(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'bonus_percentage' => 'nullable|numeric|min:0|max:1',
        ]);

        $user = User::findOrFail($request->user_id);
        $totalHours = $user->getTotalHoursForPeriod($request->period_start, $request->period_end);
        $bonusPercentage = $request->bonus_percentage ?? $user->bonus_percentage ?? 0.25;
        $calculatedAmount = round($totalHours * $bonusPercentage, 2);

        return response()->json([
            'total_hours' => $totalHours,
            'bonus_percentage' => $bonusPercentage,
            'calculated_amount' => $calculatedAmount,
            'user' => [
                'id' => $user->id,
                'name' => $user->firstname.' '.$user->lastname,
                'default_bonus_percentage' => $user->bonus_percentage ?? 0.25,
            ],
        ]);
    }
}
