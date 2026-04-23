<?php

namespace App\Http\Controllers\BackOffice\Rate;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rate\StoreRateRequest;
use App\Http\Requests\Rate\UpdateRateRequest;
use App\Models\Rate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RateController extends Controller
{
    public function index(Request $request)
    {
        $users = User::whereDoesntHave('permissions', fn ($q) =>
                $q->whereIn('name', ['RESIDENTIAL CLIENT', 'HOME CARE CLIENT', 'PRIVATE CLIENT'])
            )
            ->where('can_receive_payment', true)
            ->withCount('schedules')
            ->with(['rates' => function ($q) {
                $q->latest()->limit(1);
            }])
            ->orderBy('firstname')
            ->orderBy('lastname')
            ->get();

        return inertia('backoffice/rates/index', [
            'users'   => $users,
            'filters' => $request->only(['search', 'page']),
        ]);
    }

    public function store(StoreRateRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            
            // Check if user can receive payment
            $user = User::findOrFail($data['user_id']);
            if (!$user->can_receive_payment) {
                DB::rollBack();
                return redirect()->route('rates.index')
                    ->with('error', 'This user is not eligible for payment management.');
            }

            $data['dep1_alias']         = $data['dep1_alias'] ?? 'DEP 1';
            $data['dep2_alias']         = $data['dep2_alias'] ?? 'DEP 2';
            $data['use_flat_rate']      = (bool) ($data['use_flat_rate'] ?? false);
            $data['created_by_user_id'] = Auth::id();

            $rate = Rate::create($data);
            DB::commit();

            return redirect()->route('rates.index');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'An error occurred while saving the rate.')->withInput();
        }
    }

    public function show($uuid)
    {
        $user = User::where('uuid', $uuid)->withCount('schedules')->firstOrFail();
        
        // Check if user can receive payment
        if (!$user->can_receive_payment) {
            return redirect()->route('rates.index')
                ->with('error', 'This user is not eligible for payment management.');
        }
        
        $rates = Rate::where('user_id', $user->id)
            ->with('createdBy')
            ->orderByDesc('created_at')
            ->get();

        return inertia('backoffice/rates/show', [
            'user'  => $user,
            'rates' => $rates,
        ]);
    }

    public function update(UpdateRateRequest $request, Rate $rate)
    {
        DB::beginTransaction();
        try {
            // Check if user can receive payment
            if (!$rate->user->can_receive_payment) {
                DB::rollBack();
                return redirect()->route('rates.index')
                    ->with('error', 'This user is not eligible for payment management.');
            }
            
            $data = $request->validated();
            
            $data['use_flat_rate'] = false; // Always false now

            $rate->update($data);
            DB::commit();

            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while updating the historical rate.');
        }
    }

    public function destroy(Rate $rate)
    {
        try {
            // Check if user can receive payment
            if (!$rate->user->can_receive_payment) {
                return redirect()->route('rates.index')
                    ->with('error', 'This user is not eligible for payment management.');
            }
            
            $rate->delete();
            return redirect()->back();
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while deleting the historical rate.');
        }
    }
}
