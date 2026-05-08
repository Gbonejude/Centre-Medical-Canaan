<?php

namespace App\Http\Controllers\BackOffice\Hospital;

use App\Http\Controllers\Controller;
use App\Http\Requests\MedicalService\StoreMedicalServiceRequest;
use App\Http\Requests\MedicalService\UpdateMedicalServiceRequest;
use App\Models\MedicalService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;

class MedicalServiceController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ADMIN|SUPER ADMIN|RECEPTIONIST'),
        ];
    }

    public function index(Request $request)
    {
        return Inertia::render('backoffice/medical-services/index', [
            'services' => MedicalService::latest()->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('backoffice/medical-services/create');
    }

    public function store(StoreMedicalServiceRequest $request)
    {
        $validated = $request->validated();

        MedicalService::create($validated);

        return redirect()->route('medical-services.index')->with('success', 'Service créé avec succès.');
    }

    public function show(MedicalService $medicalService)
    {
        return Inertia::render('backoffice/medical-services/show', [
            'service' => $medicalService,
        ]);
    }

    public function edit(MedicalService $medicalService)
    {
        return Inertia::render('backoffice/medical-services/edit', [
            'service' => $medicalService,
        ]);
    }

    public function update(UpdateMedicalServiceRequest $request, MedicalService $medicalService)
    {
        $validated = $request->validated();

        $medicalService->update($validated);

        return redirect()->route('medical-services.index')->with('success', 'Service mis à jour avec succès.');
    }

    public function toggleStatus(MedicalService $medicalService)
    {
        $medicalService->update([
            'is_active' => ! $medicalService->is_active,
        ]);

        return back()->with('success', 'Statut du service mis à jour.');
    }

    public function destroy(MedicalService $medicalService)
    {
        $medicalService->delete();

        return back()->with('success', 'Service supprimé.');
    }
}
