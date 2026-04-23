<?php

namespace App\Http\Controllers\BackOffice\HR;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class HoursReportController extends Controller
{
    /**
     * Display hours report with filters.
     */
    public function index(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'period_start' => 'nullable|date',
            'period_end' => 'nullable|date|after_or_equal:period_start',
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2020',
        ]);

        // Get list of users excluding super admins
        $users = User::excludeSystemAdmins()->orderBy('firstname')->get();
        $userIds = $users->pluck('id')->toArray();

        // Build query - exclude super admins
        $query = Attendance::select(
            'user_id',
            DB::raw('SUM(hours_worked) as total_hours'),
            DB::raw('SUM(CASE WHEN hours_worked <= 8 THEN hours_worked ELSE 8 END) as regular_hours'),
            DB::raw('SUM(CASE WHEN hours_worked > 8 THEN hours_worked - 8 ELSE 0 END) as overtime_hours')
        )
            ->whereIn('user_id', $userIds) // Exclude super admins from hours report
            ->groupBy('user_id');

        // Apply filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('period_start')) {
            $query->where('date', '>=', $request->period_start);
        }

        if ($request->filled('period_end')) {
            $query->where('date', '<=', $request->period_end);
        }

        if ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('date', $request->month)
                ->whereYear('date', $request->year);
        }

        $hoursData = $query->get();

        // Load user information (filter out any null users)
        $hoursReport = $hoursData->map(function ($item) {
            $user = User::find($item->user_id);

            return [
                'user' => $user,
                'total_hours' => round($item->total_hours, 2),
                'regular_hours' => round($item->regular_hours, 2),
                'overtime_hours' => round($item->overtime_hours, 2),
            ];
        })->filter(function ($item) {
            return $item['user'] !== null; // Filter out any records with null users
        })->values();

        return Inertia::render('backoffice/hr/hours-report/index', [
            'hoursReport' => $hoursReport,
            'users' => $users,
            'filters' => $request->only(['user_id', 'period_start', 'period_end', 'month', 'year']),
        ]);
    }

    /**
     * Export hours report to Excel/PDF.
     */
    public function export(Request $request)
    {
        // You can use packages like maatwebsite/excel or barryvdh/laravel-dompdf

        return back()->with('info', 'Export functionality will be implemented soon.');
    }
}
