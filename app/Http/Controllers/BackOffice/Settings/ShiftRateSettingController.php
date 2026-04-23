<?php

namespace App\Http\Controllers\BackOffice\Settings;

use App\Http\Controllers\Controller;
use App\Models\ShiftRateSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShiftRateSettingController extends Controller
{
    /**
     * Display shift rate settings and history
     */
    public function index()
    {
        try {
            $currentSettings = ShiftRateSetting::current();

            // Get history of all settings
            $history = ShiftRateSetting::orderBy('effective_date', 'desc')
                ->orderBy('id', 'desc')
                ->paginate(10);

            return Inertia::render('backoffice/settings/shift-rates/index', [
                'currentSettings' => $currentSettings,
                'history' => $history,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while retrieving shift rate settings.');
        }
    }

    /**
     * Store new shift rate settings (creates a new record, doesn't update)
     * This preserves history and ensures old schedules use old rates
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'daily_shift_amount' => 'required|numeric|min:0',
                'night_shift_amount' => 'required|numeric|min:0',
                'flat_rate' => 'required|numeric|min:0.01',
                'effective_date' => 'required|date',
            ]);

            // Create new settings record
            ShiftRateSetting::create($validated);

            return redirect()->back()
                ->with('success', 'New shift rate settings created successfully. Old schedules will continue to use their original rates.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while creating shift rate settings.');
        }
    }
}
