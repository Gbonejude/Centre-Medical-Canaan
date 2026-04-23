<?php

namespace App\Http\Controllers\BackOffice\AcceptedCandidate;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcceptedCandidate\StoreRequest;
use App\Http\Requests\AcceptedCandidate\UpdateRequest;
use App\Mail\CandidateCompletedMail;
use App\Models\Candidate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AcceptedCandidateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $dateRange = $request->get('dateRange', 'all');

        $query = Candidate::where(function ($q) {
            $q->whereNull('career')->orWhere('career', false);
        });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'LIKE', "%{$search}%")
                    ->orWhere('lastname', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('address', 'LIKE', "%{$search}%");
            });
        }

        if ($dateRange !== 'all') {
            $today = Carbon::today();

            switch ($dateRange) {
                case 'today':
                    $query->whereDate('created_at', $today);
                    break;
                case 'week':
                    $startOfWeek = $today->copy()->startOfWeek();
                    $query->where('created_at', '>=', $startOfWeek);
                    break;
                case 'month':
                    $startOfMonth = $today->copy()->startOfMonth();
                    $query->where('created_at', '>=', $startOfMonth);
                    break;
                case 'year':
                    $startOfYear = $today->copy()->startOfYear();
                    $query->where('created_at', '>=', $startOfYear);
                    break;
            }
        }

        $acceptedCandidates = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'totalCandidates' => $query->count(),
        ];

        return inertia('backoffice/accepted-candidates/index', [
            'acceptedCandidates' => $acceptedCandidates,
            'stats' => $stats,
            'filters' => [
                'search' => $search,
                'dateRange' => $dateRange,

            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $existingCandidate = Candidate::where('created_by', auth()->id())->first();

            if ($existingCandidate) {
                return redirect()->route('accepted-candidates.index')
                    ->with('error', 'You have already submitted a candidate application.');
            }

            $user = auth()->user();
            $userData = [
                'firstname' => $user->firstname ?? '',
                'lastname' => $user->lastname ?? '',
                'email' => $user->email ?? '',
                'phone' => $user->phone ?? '',
            ];

            return inertia('backoffice/accepted-candidates/create', [
                'userData' => $userData,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('accepted-candidates.index')
                ->with('error', 'An error occurred while loading the candidate form.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validated();

            $existingCandidate = Candidate::where('created_by', auth()->id())->first();

            if ($existingCandidate) {
                return redirect()->route('accepted-candidates.index')
                    ->with('error', 'You have already submitted a candidate application.');
            }

            $educations = $validated['educations'] ?? [];
            $personalReferences = $validated['personal_references'] ?? [];
            $employmentHistory = $validated['employment_history'] ?? [];

            unset($validated['educations'], $validated['personal_references'], $validated['employment_history']);

            $validated['created_by'] = Auth::id();

            $acceptedCandidate = Candidate::create($validated);

            foreach ($educations as $education) {
                $acceptedCandidate->educations()->create($education);
            }

            foreach ($personalReferences as $reference) {
                $acceptedCandidate->personalReferences()->create($reference);
            }

            foreach ($employmentHistory as $employment) {
                $acceptedCandidate->employmentHistories()->create($employment);
            }

            Mail::to($request->email)->send(new CandidateCompletedMail($acceptedCandidate->firstname, $acceptedCandidate->lastname));
            DB::commit();

            return redirect()->route('accepted-candidates.index')
                ->with('success', 'Your profile has been completed successfully. We will contact you soon.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('accepted-candidates.create')
                ->with('error', 'An error occurred while creating the candidate: '.$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Candidate $acceptedCandidate)
    {
        try {
            $acceptedCandidate->load([
                'educations' => function ($query) {
                    $query->orderBy('order');
                },
                'personalReferences',
                'employmentHistories' => function ($query) {
                    $query->orderBy('order');
                },
            ]);

            return inertia('backoffice/accepted-candidates/show', [
                'acceptedCandidate' => $acceptedCandidate,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('accepted-candidates.index')
                ->with('error', 'An error occurred while retrieving candidate information.');
        }
    }

    /**
     * Display the user's profile.
     */
    public function profile()
    {
        try {
            $user = auth()->user();

            $user->load([
                'permissions',
                'candidate.educations' => function ($query) {
                    $query->orderBy('order');
                },
                'candidate.personalReferences',
                'candidate.employmentHistories' => function ($query) {
                    $query->orderBy('order');
                },
            ]);

            if (! $user->candidate) {
                return redirect()->route('accepted-candidates.create')
                    ->with('info', 'Please complete your candidate profile first.');
            }

            return inertia('backoffice/accepted-candidates/profile', [
                'user' => $user,
                'acceptedCandidate' => $user->candidate,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('dashboard.index')
                ->with('error', 'An error occurred while loading your profile.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Candidate $acceptedCandidate)
    {
        try {
            $acceptedCandidate->load([
                'educations' => function ($query) {
                    $query->orderBy('order');
                },
                'personalReferences',
                'employmentHistories' => function ($query) {
                    $query->orderBy('order');
                },
            ]);

            return inertia('backoffice/accepted-candidates/edit', [
                'acceptedCandidate' => $acceptedCandidate,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('accepted-candidates.index')
                ->with('error', 'An error occurred while loading the edit form.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Candidate $acceptedCandidate)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validated();
            $validated['updated_by'] = Auth::id();

            $educations = $validated['educations'] ?? [];
            $personalReferences = $validated['personal_references'] ?? [];
            $employmentHistory = $validated['employment_history'] ?? [];

            unset($validated['educations'], $validated['personal_references'], $validated['employment_history']);

            $acceptedCandidate->update($validated);

            $this->syncRelatedData($acceptedCandidate, 'educations', $educations);

            $this->syncRelatedData($acceptedCandidate, 'personalReferences', $personalReferences);

            $this->syncRelatedData($acceptedCandidate, 'employmentHistories', $employmentHistory);

            DB::commit();

            return redirect()->route('accepted-candidates.show', $acceptedCandidate)
                ->with('success', 'Accepted candidate successfully updated.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('accepted-candidates.edit', $acceptedCandidate)
                ->with('error', 'An error occurred while updating the candidate: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Candidate $acceptedCandidate)
    {
        DB::beginTransaction();

        try {
            $acceptedCandidate->educations()->delete();
            $acceptedCandidate->personalReferences()->delete();
            $acceptedCandidate->employmentHistory()->delete();

            $acceptedCandidate->delete();

            DB::commit();

            return redirect()->route('accepted-candidates.index')
                ->with('success', 'Accepted candidate successfully deleted.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('accepted-candidates.index')
                ->with('error', 'An error occurred while deleting the candidate: '.$e->getMessage());
        }
    }

    /**
     * Synchronize related data (educations, references, employment history)
     */
    private function syncRelatedData($acceptedCandidate, $relationName, $data)
    {
        $existingIds = [];
        $relation = $acceptedCandidate->{$relationName}();

        foreach ($data as $item) {
            if (isset($item['id']) && $item['id']) {
                $relation->where('id', $item['id'])->update($item);
                $existingIds[] = $item['id'];
            } else {
                $newRecord = $relation->create($item);
                $existingIds[] = $newRecord->id;
            }
        }

        $relation->whereNotIn('id', $existingIds)->delete();
    }
}
