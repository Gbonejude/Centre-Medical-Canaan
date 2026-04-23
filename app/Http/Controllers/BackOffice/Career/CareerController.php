<?php

namespace App\Http\Controllers\BackOffice\Career;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use Illuminate\Http\Request;

class CareerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $careerCandidates = Candidate::where('career', true)
            ->with(['educations', 'employmentHistories', 'personalReferences'])
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'totalCareerCandidates' => $careerCandidates->count(),
            'totalCandidates' => Candidate::count(),
        ];

        return inertia('backoffice/career/index', [
            'careerCandidates' => [
                'data' => $careerCandidates,
                'total' => $careerCandidates->count(),
            ],
            'stats' => $stats,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid)
    {
        try {
            $careerCandidate = Candidate::where('uuid', $uuid)
                ->where('career', true)
                ->firstOrFail();

            $careerCandidate->load([
                'educations' => function ($query) {
                    $query->orderBy('order');
                },
                'personalReferences',
                'employmentHistories' => function ($query) {
                    $query->orderBy('order');
                },
            ]);

            return inertia('backoffice/career/show', [
                'careerCandidate' => $careerCandidate,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('career.index')
                ->with('error', 'Career candidate not found.');
        } catch (\Exception $e) {
            return redirect()->route('career.index')
                ->with('error', 'An error occurred while retrieving career candidate information.');
        }
    }
}
