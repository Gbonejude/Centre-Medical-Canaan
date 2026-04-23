<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => function () use ($request) {
                $user = auth()->user();

                $guest = $request->user('guest');

                return [
                    'user' => $user ? [
                        'id' => $user->id,
                        'firstname' => $user->firstname,
                        'lastname' => $user->lastname,
                        'username' => $user->username,
                        'email' => $user->email,
                        'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
                        'avatar_url' => $user->hasMedia('users')
                        ? $user->getFirstMediaUrl('users')
                        : null,
                    ] : null,
                    'notifications' => $user ? $user->unreadNotifications()->take(5)->get() : [],

                    'guest' => $guest ? [
                        'id' => $guest->id,
                        'uuid' => $guest->uuid,
                        'firstname' => $guest->firstname,
                        'lastname' => $guest->lastname,
                        'email' => $guest->email,
                        'phone' => $guest->phone,
                        'active' => $guest->active,
                        'email_verified_at' => $guest->email_verified_at,
                        'created_at' => $guest->created_at,
                    ] : null,

                ];
            },
            'errors' => function () use ($request) {
                return $request->session()->get('errors')
                    ? $request->session()->get('errors')->getBag('default')->getMessages()
                    : (object) [];
            },
            'flash' => function () use ($request) {
                return [
                    'success' => $request->session()->get('success'),
                    'error' => $request->session()->get('error'),
                    'info' => $request->session()->get('info'),
                    'warning' => $request->session()->get('warning'),
                ];
            },
            'route' => function () use ($request) {
                return [
                    'name' => $request->route()?->getName(),
                    'params' => $request->route()?->parameters() ?? [],
                ];
            },
            'app' => [
                'name' => config('app.name'),
                'url' => config('app.url'),
                'locale' => app()->getLocale(),
            ],
        ]);
    }
}
