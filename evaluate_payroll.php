<?php

use App\Http\Controllers\BackOffice\Schedule\ScheduleController;
use Illuminate\Http\Request;

auth()->login(\App\Models\User::find(2));

$req = new Request(['start_date' => '2026-04-06', 'end_date' => '2026-04-19']);
$c = new ScheduleController();
$resp = $c->payroll($req);

$props = $resp->toResponse($req)->getOriginalContent()->getData()['page']['props'];

$addai = collect($props['payrollData'])->firstWhere('user.id', 47);

echo json_encode(['data' => $addai ?? null]);
