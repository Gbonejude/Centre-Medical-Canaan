<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Mail\NewUserMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Permission;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ADMIN|SUPER ADMIN'),
        ];
    }

    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $permissionFilter = $request->get('p', '');

        $users = User::with(['roles', 'media', 'permissions'])
            ->excludeSystemAdmins()
            ->orderBy('lastname')
            ->orderBy('firstname')
            ->get();

        $users->transform(function ($user) {
            $user->avatar_url = $user->hasMedia('user')
                ? $user->getFirstMediaUrl('user')
                : null;

            $user->display_permissions = $user->permissions->map(function ($permission) {
                return [
                    'id' => 'permission-'.$permission->id,
                    'name' => $permission->name,
                    'encoded_id' => base64_encode($permission->id),
                ];
            })->values();

            return $user;
        });

        // Permissions staff pour l'hôpital (pas PATIENT)
        $staffPermissions = Permission::where('name', '!=', 'PATIENT')
            ->where('name', '!=', 'DOCTOR')
            ->where('name', '!=', 'SUPER ADMIN')
            ->orderBy('name')
            ->get()
            ->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'encoded_id' => base64_encode($permission->id),
                    'is_customer' => false,
                ];
            });

        return inertia('backoffice/users/index', [
            'users' => $users,
            'filters' => [
                'search' => $search,
                'permission' => $permissionFilter,
            ],
            'staffPermissions' => $staffPermissions,
        ]);
    }

    public function create()
    {
        $staffPermissions = Permission::where('name', '!=', 'PATIENT')
            ->where('name', '!=', 'DOCTOR')
            ->where('name', '!=', 'SUPER ADMIN')
            ->get();

        return inertia('backoffice/users/create', [
            'permissions' => $staffPermissions,
        ]);
    }

    public function store(StoreRequest $request)
    {
        try {
            $userData = $request->only(['firstname', 'lastname', 'email', 'gender', 'phone', 'birthday']);
            $passwordGenerate = $this->generatePassword();
            $userData['password'] = bcrypt($passwordGenerate);

            $permissions = $request->input('permissions', []);
            $user = User::create($userData);

            if ($user) {
                if (! empty($permissions)) {
                    $user->permissions()->sync($permissions);
                }
                if ($request->hasFile('image')) {
                    $user->addMediaFromRequest('image')
                        ->toMediaCollection('user');
                }
            }

            Mail::to($request->email)->send(new NewUserMail($user->email, $passwordGenerate));

            return redirect()->route('users.index')
                ->with('success', 'Utilisateur créé avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('error', 'Erreur lors de la création : '.$e->getMessage());
        }
    }

    public function show($uuid)
    {
        $userData = User::where('uuid', $uuid)->firstOrFail();
        $userPermissions = $userData->getAllPermissions();
        $userAvatar = $userData->hasMedia('user') ? $userData->getFirstMediaUrl('user') : null;

        return inertia('backoffice/users/show', [
            'user' => $userData,
            'userPermissions' => $userPermissions,
            'userAvatar' => $userAvatar,
        ]);
    }

    public function edit($uuid)
    {
        $userData = User::where('uuid', $uuid)->firstOrFail();
        $userPermissions = $userData->getAllPermissions()->pluck('id');
        $staffPermissions = Permission::where('name', '!=', 'PATIENT')
            ->where('name', '!=', 'DOCTOR')
            ->where('name', '!=', 'SUPER ADMIN')
            ->get();

        $userAvatar = $userData->hasMedia('user') ? $userData->getFirstMediaUrl('user') : null;

        return inertia('backoffice/users/edit', [
            'permissions' => $staffPermissions,
            'userPermissions' => $userPermissions,
            'user' => $userData,
            'userAvatar' => $userAvatar,
        ]);
    }

    public function update(UpdateRequest $request, $uuid)
    {
        try {
            $user = User::where('uuid', $uuid)->firstOrFail();
            $userData = $request->only(['firstname', 'lastname', 'email', 'gender', 'phone', 'birthday']);

            if ($request->filled('password')) {
                $userData['password'] = $request->password;
            }

            $permissions = $request->input('permissions', []);

            $user->update($userData);
            $user->permissions()->sync($permissions);

            if ($request->has('remove_image') && $request->boolean('remove_image')) {
                $user->clearMediaCollection('user');
            } elseif ($request->hasFile('image')) {
                $user->clearMediaCollection('user');
                $user->addMediaFromRequest('image')
                    ->toMediaCollection('user');
            }

            return redirect()->route('users.index')
                ->with('success', 'Utilisateur mis à jour avec succès');
        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('error', 'Erreur lors de la mise à jour.');
        }
    }

    public function updateStatus($uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();
        $user->update(['active' => ! $user->active]);

        return redirect()->route('users.index')->with('success', 'Statut mis à jour.');
    }

    public function destroy($uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé.');
    }

    protected function generatePassword(): string
    {
        return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);
    }
}
