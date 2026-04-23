<?php

namespace App\Http\Controllers\FrontOffice;

use App\Http\Controllers\Controller;
use App\Models\MedicalService;

class PagesController extends Controller
{
    public function index()
    {
        $services = MedicalService::where('is_active', true)
            ->select(['id', 'name', 'description'])
            ->latest()
            ->paginate(8);

        $allServices = MedicalService::where('is_active', true)
            ->select(['id', 'name'])
            ->get();

        return inertia('frontoffice/home/index', [
            'services' => $services,
            'allServices' => $allServices,
        ]);
    }
}
