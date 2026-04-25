<?php

namespace App\Http\Controllers\BackOffice\Hospital;

use App\Http\Controllers\Controller;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SpecialtyController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ADMIN|SUPER ADMIN|RECEPTIONIST'),
        ];
    }

    public function index(Request $request)
    {
        $query = Specialty::query();

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        return Inertia::render('backoffice/specialties/index', [
            'specialties' => $query->latest()->paginate(10)->withQueryString(),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('backoffice/specialties/create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:specialties,name',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Specialty::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('specialties.index')->with('success', 'Spécialité créée avec succès.');
    }

    public function edit($uuid)
    {
        $specialty = Specialty::where('uuid', $uuid)->firstOrFail();
        return Inertia::render('backoffice/specialties/edit', [
            'specialty' => $specialty
        ]);
    }

    public function update(Request $request, $uuid)
    {
        $specialty = Specialty::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:specialties,name,' . $specialty->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $specialty->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'is_active' => $validated['is_active'],
        ]);

        return redirect()->route('specialties.index')->with('success', 'Spécialité mise à jour avec succès.');
    }

    public function destroy($uuid)
    {
        $specialty = Specialty::where('uuid', $uuid)->firstOrFail();
        
        if ($specialty->doctors()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer cette spécialité car elle est liée à des médecins.');
        }

        $specialty->delete();
        return back()->with('success', 'Spécialité supprimée avec succès.');
    }

    public function toggleStatus($uuid)
    {
        $specialty = Specialty::where('uuid', $uuid)->firstOrFail();
        $specialty->is_active = !$specialty->is_active;
        $specialty->save();

        return back()->with('success', 'Statut mis à jour.');
    }
}
