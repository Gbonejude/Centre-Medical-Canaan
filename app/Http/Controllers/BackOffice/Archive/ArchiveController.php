<?php

namespace App\Http\Controllers\BackOffice\Archive;

use App\Http\Controllers\Controller;
use App\Http\Requests\BackOffice\Archive\StoreRequest;
use App\Http\Requests\BackOffice\Archive\UpdateRequest;
use App\Mail\ArchiveNotificationMail;
use App\Models\Archive;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Models\Permission;

class ArchiveController extends Controller
{
    private function isRestrictedUser(): bool
    {
        $user = Auth::user();

        // High-level roles are never restricted
        // We add 'OFFICE' and 'PROGRAM DIRECTOR' to the unrestricted list
        if ($user->hasAnyPermission(['SUPER ADMIN', 'ADMIN', 'OFFICE', 'PROGRAM DIRECTOR'])) {
            return false;
        }

        $restricted = ['CAREGIVER','DSP 10066 MARSHALL','DSP 602 FILLMORE',
            'DSP DAY PROGRAM','DSP IN-HOME SUPPORT','DSP 804 GREENTHORN','DSP 711 GREENTHORN'];
        foreach ($restricted as $role) {
            if ($user->hasPermissionTo($role)) return true;
        }
        return false;
    }

    public function index()
    {
        $user = Auth::user();
        $userPermissionNames = $user->permissions->pluck('name')->toArray();

        $query = Archive::with(['user', 'media', 'permissions'])->latest();

        if ($this->isRestrictedUser()) {
            $query->where(function ($q) use ($user, $userPermissionNames) {
                $q->where('user_id', $user->id)
                  ->orWhere('created_by', $user->id)
                  ->orWhereHas('permissions', fn ($q2) =>
                      $q2->whereIn('name', $userPermissionNames)
                  );
            });
        }

        $allowedPermissions = [
            'ADMIN', 'CAREGIVER', 'DSP 10066 MARSHALL', 'DSP 602 FILLMORE',
            'DSP 711 GREENTHORN', 'DSP 804 GREENTHORN', 'DSP DAY PROGRAM',
            'DSP IN-HOME SUPPORT', 'OFFICE', 'PROGRAM DIRECTOR', 'Q. IMPROV'
        ];
        $permissions = Permission::whereIn('name', $allowedPermissions)->orderBy('name')->get(['id', 'name']);

        return inertia('backoffice/archives/index', [
            'archives'     => $query->get(),
            'users'        => $this->getUsersList(),
            'permissions'  => $permissions,
            'isRestricted' => $this->isRestrictedUser(),
        ]);
    }

    public function create()
    {
        return inertia('backoffice/archives/create', [
            'users'        => $this->getUsersList(),
            'permissions'  => Permission::orderBy('name')->get(['id', 'name']),
            'isRestricted' => $this->isRestrictedUser(),
        ]);
    }

    public function store(StoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->only(['title', 'description', 'user_id']);
            $data['created_by'] = Auth::id();
            $archive = Archive::create($data);

            if ($request->filled('permission_ids')) {
                $archive->permissions()->sync($request->permission_ids);
            }

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $archive->addMedia($file)->toMediaCollection('archives');
                }
            }

            DB::commit();

            $this->sendNotifications($archive);

            return redirect()->route('archives.index')
                ->with('success', 'CSS Drive entry created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating archive: ' . $e->getMessage());
        }
    }

    public function show(Archive $archive)
    {
        $user = Auth::user();
        if ($this->isRestrictedUser()) {
            $userPermissionNames = $user->permissions->pluck('name')->toArray();
            $canAccess = $archive->user_id === $user->id
                || $archive->created_by === $user->id
                || $archive->permissions->pluck('name')->intersect($userPermissionNames)->isNotEmpty();
            if (!$canAccess) abort(403);
        }
        $archive->load(['user', 'media', 'permissions']);
        return inertia('backoffice/archives/show', ['archive' => $archive]);
    }

    public function edit(Archive $archive)
    {
        $archive->load(['user', 'media', 'permissions']);

        return inertia('backoffice/archives/edit', [
            'archive'      => $archive,
            'users'        => $this->getUsersList(),
            'permissions'  => Permission::orderBy('name')->get(['id', 'name']),
            'isRestricted' => $this->isRestrictedUser(),
        ]);
    }

    public function update(UpdateRequest $request, Archive $archive)
    {
        try {
            DB::beginTransaction();

            $archive->update($request->only(['title', 'description', 'user_id']));
            $archive->permissions()->sync($request->permission_ids ?? []);

            if ($request->filled('remove_media')) {
                foreach ($request->remove_media as $mediaId) {
                    $media = Media::find($mediaId);
                    if ($media && $media->model_id === $archive->id) {
                        $media->delete();
                    }
                }
            }

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $archive->addMedia($file)->toMediaCollection('archives');
                }
            }

            DB::commit();

            return redirect()->route('archives.index')
                ->with('success', 'CSS Drive entry updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating archive: ' . $e->getMessage());
        }
    }

    public function destroy(Archive $archive)
    {
        $archive->delete();
        return redirect()->route('archives.index')->with('success', 'Archive deleted successfully.');
    }

    public function downloadMedia(Archive $archive, Media $media)
    {
        if ($media->model_id !== $archive->id || $media->model_type !== get_class($archive)) abort(404);
        return response()->download($media->getPath(), $media->file_name);
    }

    public function viewMedia(Archive $archive, Media $media)
    {
        if ($media->model_id !== $archive->id || $media->model_type !== get_class($archive)) abort(404);
        return response()->file($media->getPath());
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function getUsersList(): array
    {
        return User::orderBy('lastname')->orderBy('firstname')
            ->get(['id', 'firstname', 'lastname'])
            ->map(fn ($u) => [
                'id'        => $u->id,
                'full_name' => $u->lastname . ' ' . $u->firstname,
                'firstname' => $u->firstname,
                'lastname'  => $u->lastname,
            ])->toArray();
    }

    /**
     * Send ONE email per unique user who has any of the archive permissions or is the assigned user.
     */
    private function sendNotifications(Archive $archive): void
    {
        try {
            $archive->load(['permissions', 'user']);
            $notifiedIds = collect();

            // Users targeted via role
            $permNames = $archive->permissions->pluck('name');
            if ($permNames->isNotEmpty()) {
                User::whereHas('permissions', fn ($q) => $q->whereIn('name', $permNames))
                    ->get()
                    ->each(function ($u) use ($archive, &$notifiedIds) {
                        if ($u->email && !$notifiedIds->contains($u->id)) {
                            Mail::to($u->email)->send(new ArchiveNotificationMail(
                                $archive->title,
                                $archive->description ?? '',
                                $u->firstname . ' ' . $u->lastname,
                            ));
                            $notifiedIds->push($u->id);
                        }
                    });
            }

            // Directly assigned user (no duplicate)
            if ($archive->user && $archive->user->email && !$notifiedIds->contains($archive->user->id)) {
                Mail::to($archive->user->email)->send(new ArchiveNotificationMail(
                    $archive->title,
                    $archive->description ?? '',
                    $archive->user->firstname . ' ' . $archive->user->lastname,
                ));
            }
        } catch (\Exception $e) {
            Log::error('Archive notification error: ' . $e->getMessage());
        }
    }
}
