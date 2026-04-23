<?php

namespace App\Http\Controllers\BackOffice\HR;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Notifications\LeaveRequestApprovedNotification;
use App\Notifications\LeaveRequestCreatedNotification;
use App\Notifications\LeaveRequestRejectedNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of leave requests.
     */
    public function index(Request $request)
    {
        $query = LeaveRequest::with(['user', 'reviewedBy'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('firstname', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%");
                    });
            });
        }

        $leaveRequests = $query->paginate(20);
        $users = User::excludeSystemAdmins()->orderBy('firstname')->get();

        return Inertia::render('backoffice/hr/leave-requests/index', [
            'leaveRequests' => $leaveRequests,
            'users' => $users,
            'filters' => $request->only(['status', 'type', 'user_id', 'search']),
        ]);
    }

    /**
     * Show the form for creating a new leave request (admin only).
     */
    public function create()
    {
        $users = User::excludeSystemAdmins()->orderBy('firstname')->get();

        return Inertia::render('backoffice/hr/leave-requests/create', [
            'users' => $users,
        ]);
    }

    /**
     * Store a newly created leave request (admin only).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:vacation,sick_leave,personal,day_off,other',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $validated['total_days'] = $startDate->diffInDays($endDate) + 1;

        $leaveRequest = LeaveRequest::create($validated);

        // Notify admins and HR staff
        $adminPermissions = [
            'SUPER ADMIN',
            'PROGRAM DIRECTOR',
            'ADMIN',
            'OFFICE',
        ];

        $admins = User::whereHas('permissions', function ($q) use ($adminPermissions) {
            $q->whereIn('name', $adminPermissions);
        })->get();

        foreach ($admins as $admin) {
            $admin->notify(new LeaveRequestCreatedNotification($leaveRequest));
        }

        return redirect()->route('leave-requests.index')
            ->with('success', 'Leave request created successfully.');
    }

    /**
     * Display the specified leave request.
     */
    public function show(LeaveRequest $leaveRequest)
    {
        $leaveRequest->load(['user', 'reviewedBy']);

        return Inertia::render('backoffice/hr/leave-requests/show', [
            'leaveRequest' => $leaveRequest,
        ]);
    }

    /**
     * Show the form for editing the specified leave request.
     */
    public function edit(LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->status !== 'pending') {
            return redirect()->route('leave-requests.show', $leaveRequest)
                ->with('error', 'Only pending leave requests can be edited.');
        }

        $users = User::excludeSystemAdmins()->orderBy('firstname')->get();

        return Inertia::render('backoffice/hr/leave-requests/edit', [
            'leaveRequest' => $leaveRequest,
            'users' => $users,
        ]);
    }

    /**
     * Update the specified leave request.
     */
    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->status !== 'pending') {
            return redirect()->route('leave-requests.show', $leaveRequest)
                ->with('error', 'Only pending leave requests can be updated.');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:vacation,sick_leave,personal,day_off,other',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $validated['total_days'] = $startDate->diffInDays($endDate) + 1;

        $leaveRequest->update($validated);

        return redirect()->route('leave-requests.index')
            ->with('success', 'Leave request updated successfully.');
    }

    /**
     * Approve a leave request.
     */
    public function approve(Request $request, LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->status !== 'pending') {
            return back()->with('error', 'Only pending leave requests can be approved.');
        }

        $validated = $request->validate([
            'review_notes' => 'nullable|string',
        ]);

        $leaveRequest->update([
            'status' => 'approved',
            'reviewed_by' => Auth::user()->id,
            'reviewed_at' => now(),
            'review_notes' => $validated['review_notes'] ?? null,
        ]);

        // Notify the employee
        $leaveRequest->refresh();
        $leaveRequest->load('reviewedBy');
        $leaveRequest->user->notify(new LeaveRequestApprovedNotification($leaveRequest));

        return back()->with('success', 'Leave request approved successfully.');
    }

    /**
     * Reject a leave request.
     */
    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->status !== 'pending') {
            return back()->with('error', 'Only pending leave requests can be rejected.');
        }

        $validated = $request->validate([
            'review_notes' => 'required|string',
        ]);

        $leaveRequest->update([
            'status' => 'rejected',
            'reviewed_by' => Auth::user()->id,
            'reviewed_at' => now(),
            'review_notes' => $validated['review_notes'],
        ]);

        // Notify the employee
        $leaveRequest->refresh();
        $leaveRequest->load('reviewedBy');
        $leaveRequest->user->notify(new LeaveRequestRejectedNotification($leaveRequest));

        return back()->with('success', 'Leave request rejected.');
    }

    /**
     * Remove the specified leave request.
     */
    public function destroy(LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->status !== 'pending') {
            return back()->with('error', 'Only pending leave requests can be deleted.');
        }

        $leaveRequest->delete();

        return redirect()->route('leave-requests.index')
            ->with('success', 'Leave request deleted successfully.');
    }

    /**
     * Show the form for creating a personal leave request.
     */
    public function myCreate()
    {
        return Inertia::render('backoffice/hr/leave-requests/my-create');
    }

    /**
     * Store a personal leave request.
     */
    public function myStore(Request $request)
    {
        $currentUser = Auth::user();

        $validated = $request->validate([
            'type' => 'required|in:vacation,sick_leave,personal,day_off,other',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        // Force user_id to current user
        $validated['user_id'] = $currentUser->id;

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $validated['total_days'] = $startDate->diffInDays($endDate) + 1;

        $leaveRequest = LeaveRequest::create($validated);

        // Notify admins and HR staff
        $adminPermissions = [
            'SUPER ADMIN',
            'PROGRAM DIRECTOR',
            'ADMIN',
            'OFFICE',
        ];

        $admins = User::whereHas('permissions', function ($q) use ($adminPermissions) {
            $q->whereIn('name', $adminPermissions);
        })->get();

        foreach ($admins as $admin) {
            $admin->notify(new LeaveRequestCreatedNotification($leaveRequest));
        }

        return redirect()->route('leave-requests.my-requests')
            ->with('success', 'Your leave request has been submitted successfully.');
    }

    /**
     * Get my leave requests (for employee view).
     */
    public function myRequests(Request $request)
    {
        $query = LeaveRequest::where('user_id', Auth::user()->id)
            ->with('reviewedBy')
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
            });
        }

        $leaveRequests = $query->paginate(20);

        return Inertia::render('backoffice/hr/leave-requests/my-requests', [
            'leaveRequests' => $leaveRequests,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    /**
     * Display my leave request details (employee view).
     */
    public function myShow(LeaveRequest $leaveRequest)
    {
        // Ensure user can only view their own requests
        if ($leaveRequest->user_id !== Auth::user()->id) {
            return redirect()->route('leave-requests.my-requests')
                ->with('error', 'You can only view your own leave requests.');
        }

        $leaveRequest->load(['user', 'reviewedBy']);

        return Inertia::render('backoffice/hr/leave-requests/my-show', [
            'leaveRequest' => $leaveRequest,
        ]);
    }

    /**
     * Show the form for editing my leave request (employee view).
     */
    public function myEdit(LeaveRequest $leaveRequest)
    {
        // Ensure user can only edit their own requests
        if ($leaveRequest->user_id !== Auth::user()->id) {
            return redirect()->route('leave-requests.my-requests')
                ->with('error', 'You can only edit your own leave requests.');
        }

        if ($leaveRequest->status !== 'pending') {
            return redirect()->route('leave-requests.my-requests')
                ->with('error', 'Only pending leave requests can be edited.');
        }

        return Inertia::render('backoffice/hr/leave-requests/my-edit', [
            'leaveRequest' => $leaveRequest,
        ]);
    }

    /**
     * Update my leave request (employee view).
     */
    public function myUpdate(Request $request, LeaveRequest $leaveRequest)
    {
        // Ensure user can only update their own requests
        if ($leaveRequest->user_id !== Auth::user()->id) {
            return redirect()->route('leave-requests.my-requests')
                ->with('error', 'You can only update your own leave requests.');
        }

        if ($leaveRequest->status !== 'pending') {
            return redirect()->route('leave-requests.my-requests')
                ->with('error', 'Only pending leave requests can be updated.');
        }

        $validated = $request->validate([
            'type' => 'required|in:vacation,sick_leave,personal,day_off,other',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $validated['total_days'] = $startDate->diffInDays($endDate) + 1;

        $leaveRequest->update($validated);

        return redirect()->route('leave-requests.my-requests')
            ->with('success', 'Your leave request has been updated successfully.');
    }
}
