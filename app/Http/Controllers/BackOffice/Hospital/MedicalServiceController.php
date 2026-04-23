<?php

namespace App\Http\Controllers\BackOffice\Hospital;

use App\Http\Controllers\Controller;
use App\Models\MedicalService;
use Illuminate\Http\Request;
use Inertia\Inertia;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

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
        $query = MedicalService::query();

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        return Inertia::render('backoffice/medical-services/index', [
            'services' => $query->latest()->paginate(10)->withQueryString(),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('backoffice/medical-services/create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        MedicalService::create($validated);

        return redirect()->route('medical-services.index')->with('success', 'Service créé avec succès.');
    }

    public function show(MedicalService $medicalService)
    {
        return Inertia::render('backoffice/medical-services/show', [
            'service' => $medicalService
        ]);
    }

    public function edit(MedicalService $medicalService)
    {
        return Inertia::render('backoffice/medical-services/edit', [
            'service' => $medicalService
        ]);
    }

    public function update(Request $request, MedicalService $medicalService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $medicalService->update($validated);

        return redirect()->route('medical-services.index')->with('success', 'Service mis à jour avec succès.');
    }

    public function toggleStatus(MedicalService $medicalService)
    {
        $medicalService->update([
            'is_active' => !$medicalService->is_active
        ]);

        return back()->with('success', 'Statut du service mis à jour.');
    }

    public function destroy(MedicalService $medicalService)
    {
        $medicalService->delete();
        return back()->with('success', 'Service supprimé.');
    }
}
