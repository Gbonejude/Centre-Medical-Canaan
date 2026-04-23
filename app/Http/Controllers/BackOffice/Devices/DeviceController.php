<?php

namespace App\Http\Controllers\BackOffice\Devices;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDevice;
use App\Notifications\DeviceAuthorizedNotification;
use App\Notifications\DeviceDeactivatedNotification;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $systemAdminEmails = User::getSystemAdminEmails();
        $search = $request->input('search');

        $devices = UserDevice::query()
            ->select('user_devices.*')
            ->join('users', 'users.id', '=', 'user_devices.user_id')
            ->with('user')
            ->whereNotIn('users.email', $systemAdminEmails)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('users.firstname', 'like', "%{$search}%")
                        ->orWhere('users.lastname', 'like', "%{$search}%")
                        ->orWhere('users.email', 'like', "%{$search}%")
                        ->orWhere('user_devices.device_name', 'like', "%{$search}%")
                        ->orWhere('user_devices.browser', 'like', "%{$search}%")
                        ->orWhere('user_devices.device_os', 'like', "%{$search}%")
                        ->orWhere('user_devices.ip_address', 'like', "%{$search}%");
                });
            })
            ->orderBy('users.lastname')
            ->orderBy('users.firstname')
            ->paginate(10)
            ->withQueryString();

        return inertia('backoffice/devices/index', [
            'devices' => $devices,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function authorizeDevice(UserDevice $device)
    {
        if ($device->is_authorized) {
            return redirect()
                ->route('devices.index')
                ->with('warning', 'This device is already authorized.');
        }

        $device->is_authorized = true;
        $device->save();

        // Envoyer notification à l'utilisateur
        $device->user->notify(new DeviceAuthorizedNotification($device));

        return redirect()
            ->route('devices.index')
            ->with('success', 'Device successfully authorized.');
    }

    public function deactivateDevice(UserDevice $device)
    {
        if (! $device->is_authorized) {
            return redirect()
                ->route('devices.index')
                ->with('warning', 'This device is already disabled.');
        }

        $device->update([
            'is_authorized' => false,
        ]);

        // Envoyer notification à l'utilisateur
        $device->user->notify(new DeviceDeactivatedNotification($device));

        return redirect()
            ->route('devices.index')
            ->with('success', 'Device successfully disabled.');
    }
}
