<?php

namespace App\Http\Controllers\BackOffice\Messaging\Conversation;

use App\Enums\ConversationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Messaging\Conversation\StoreRequest;
use App\Http\Requests\Messaging\Conversation\UpdateRequest;
use App\Mail\ConversationMentionMail;
use App\Mail\ConversationPermissionMail;
use App\Mail\ConversationUpdatedMentionMail;
use App\Mail\ConversationUpdatedPermissionMail;
use App\Models\Conversation;
use App\Models\Mention;
use App\Models\Permission;
use App\Models\Share;
use App\Models\User;
use App\Notifications\CommunityConversationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ConversationController extends Controller
{
    /**
     * Display a listing of conversations.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $search = $request->get('search', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $query = Conversation::with([
            'user:id,firstname,lastname,username,email',
            'media',
            'comments' => function ($query) {
                $query->with([
                    'user:id,firstname,lastname,username',
                    'user.media',
                    'replies' => function ($replyQuery) {
                        $replyQuery->with([
                            'user:id,firstname,lastname,username',
                            'user.media',
                        ])->withCount('reactions');
                    },
                ])->withCount('reactions');
            },
            'user.media',
        ])
            ->withCount(['comments', 'reactions', 'shares'])
            ->where('status', ConversationStatus::PUBLISHED->value)
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->orWhereHas('mentions', function ($q2) use ($userId) {
                        $q2->where('mentionable_id', $userId)
                            ->where('mentionable_type', User::class);
                    });
            });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('firstname', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%");
                    });
            });
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        if (in_array($sort, ['title', 'created_at', 'updated_at'])) {
            $query->orderBy($sort, $direction);
        } elseif ($sort === 'comments_count') {
            $query->orderBy('comments_count', $direction);
        } elseif ($sort === 'reactions_count') {
            $query->orderBy('reactions_count', $direction);
        } else {
            $query->latest();
        }

        $conversations = $query->paginate(15)->withQueryString();

        $conversations->getCollection()->transform(function ($conversation) {
            if ($conversation->user) {
                $conversation->user = $this->addUserAvatarUrls($conversation->user);
            }

            $conversation->media = $conversation->getAllMediaUrls();
            $conversation->shares_count = $conversation->shares()->count();

            if ($conversation->comments) {
                $conversation->comments->transform(function ($comment) {
                    if ($comment->user) {
                        $comment->user = $this->addUserAvatarUrls($comment->user);
                    }

                    if ($comment->replies) {
                        $comment->replies->transform(function ($reply) {
                            if ($reply->user) {
                                $reply->user = $this->addUserAvatarUrls($reply->user);
                            }

                            return $reply;
                        });
                    }

                    return $comment;
                });
            }

            return $conversation;
        });

        return inertia('backoffice/messaging/conversations/index', [
            'conversations' => $conversations,
            'filters' => [
                'search' => $search,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'sort' => $sort,
                'direction' => $direction,
            ],
        ]);
    }

    public function myConversations(Request $request)
    {
        $search = $request->get('search', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $query = Conversation::with([
            'user:id,firstname,lastname,username,email',
            'media',
            'comments' => function ($query) {
                $query->with([
                    'user:id,firstname,lastname,username',
                    'user.media',
                    'replies' => function ($replyQuery) {
                        $replyQuery->with([
                            'user:id,firstname,lastname,username',
                            'user.media',
                        ])->withCount('reactions');
                    },
                ])->withCount('reactions');
            },
            'user.media',
        ])
            ->withCount(['comments', 'reactions', 'shares'])
            ->where('user_id', Auth::id())
            ->where('status', ConversationStatus::PUBLISHED->value);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        if (in_array($sort, ['title', 'created_at', 'updated_at'])) {
            $query->orderBy($sort, $direction);
        } elseif ($sort === 'comments_count') {
            $query->orderBy('comments_count', $direction);
        } elseif ($sort === 'reactions_count') {
            $query->orderBy('reactions_count', $direction);
        } else {
            $query->latest();
        }

        $conversations = $query->paginate(15)->withQueryString();

        $conversations->getCollection()->transform(function ($conversation) {
            if ($conversation->user) {
                $conversation->user = $this->addUserAvatarUrls($conversation->user);
            }

            $conversation->media = $conversation->getAllMediaUrls();

            $conversation->shares_count = $conversation->shares()->count();

            if ($conversation->comments) {
                $conversation->comments->transform(function ($comment) {
                    if ($comment->user) {
                        $comment->user = $this->addUserAvatarUrls($comment->user);
                    }

                    if ($comment->replies) {
                        $comment->replies->transform(function ($reply) {
                            if ($reply->user) {
                                $reply->user = $this->addUserAvatarUrls($reply->user);
                            }

                            return $reply;
                        });
                    }

                    return $comment;
                });
            }

            return $conversation;
        });

        return inertia('backoffice/messaging/conversations/my-conversations', [
            'conversations' => $conversations,
            'filters' => [
                'search' => $search,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'sort' => $sort,
                'direction' => $direction,
            ],
        ]);
    }

    public function myDrafts(Request $request)
    {
        $search = $request->get('search', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $query = Conversation::with([
            'user:id,firstname,lastname,username,email',
            'media',
            'comments' => function ($query) {
                $query->with([
                    'user:id,firstname,lastname,username',
                    'user.media',
                    'replies' => function ($replyQuery) {
                        $replyQuery->with([
                            'user:id,firstname,lastname,username',
                            'user.media',
                        ])->withCount('reactions');
                    },
                ])->withCount('reactions');
            },
            'user.media',
        ])
            ->withCount(['comments', 'reactions', 'shares'])
            ->where('user_id', Auth::id())
            ->where('status', ConversationStatus::DRAFT->value);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        if (in_array($sort, ['title', 'created_at', 'updated_at'])) {
            $query->orderBy($sort, $direction);
        } elseif ($sort === 'comments_count') {
            $query->orderBy('comments_count', $direction);
        } elseif ($sort === 'reactions_count') {
            $query->orderBy('reactions_count', $direction);
        } else {
            $query->latest();
        }

        $conversations = $query->paginate(15)->withQueryString();

        $conversations->getCollection()->transform(function ($conversation) {
            if ($conversation->user) {
                $conversation->user = $this->addUserAvatarUrls($conversation->user);
            }

            $conversation->media = $conversation->getAllMediaUrls();

            $conversation->shares_count = $conversation->shares()->count();

            if ($conversation->comments) {
                $conversation->comments->transform(function ($comment) {
                    if ($comment->user) {
                        $comment->user = $this->addUserAvatarUrls($comment->user);
                    }

                    if ($comment->replies) {
                        $comment->replies->transform(function ($reply) {
                            if ($reply->user) {
                                $reply->user = $this->addUserAvatarUrls($reply->user);
                            }

                            return $reply;
                        });
                    }

                    return $comment;
                });
            }

            return $conversation;
        });

        return inertia('backoffice/messaging/conversations/my-drafts', [
            'conversations' => $conversations,
            'filters' => [
                'search' => $search,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'sort' => $sort,
                'direction' => $direction,
            ],
        ]);
    }

    /**
     * Add avatar URLs to user object.
     *
     * @param  \App\Models\User  $user
     * @return \App\Models\User
     */
    private function addUserAvatarUrls($user)
    {
        $userAvatar = $user->getFirstMedia('users');
        $user->avatar_url = $userAvatar
            ? $userAvatar->getUrl()
            : asset('assets/img/user.jpg');
        $user->avatar_thumb = $userAvatar
            ? $userAvatar->getUrl('thumb')
            : asset('assets/img/user.jpg');

        return $user;
    }

    /**
     * Show the form for creating a new conversation.
     */
    public function create()
    {
        try {
            $users = User::excludeSystemAdmins()
                ->select('id', 'firstname', 'lastname', 'username', 'email')
                ->orderBy('firstname')
                ->get();

            $staffPermissions = Permission::where('is_customer', false)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            $customerPermissions = Permission::where('is_customer', true)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            return inertia('backoffice/messaging/conversations/create', [
                'availableUsers' => $users,
                'staffPermissions' => $staffPermissions,
                'customerPermissions' => $customerPermissions,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('conversations.index')
                ->with('error', 'An error occurred while loading the create form.');
        }
    }

    /**
     * Store a newly created conversation.
     */
    public function store(StoreRequest $request)
    {
        DB::beginTransaction();

        try {
            $validatedData = $request->validated();
            $validatedData['user_id'] = Auth::id();

            $conversationData = collect($validatedData)
                ->except(['media', 'mentions', 'permissions'])
                ->toArray();

            $conversation = Conversation::create($conversationData);

            $permissions = $request->input('permissions', []);
            $permissionIds = [];
            if (! empty($permissions)) {
                $permissionIds = Permission::whereIn('id', $permissions)
                    ->where('name', '!=', 'SUPER ADMIN')
                    ->pluck('id')
                    ->toArray();

                $conversation->permissions()->sync($permissionIds);
            }

            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $conversation->addMedia($file)
                        ->toMediaCollection('conversations');
                }
            }

            $mentionedUserIds = [];
            if ($request->filled('mentions')) {
                $mentionsInput = $request->input('mentions');

                if (in_array('everyone', $mentionsInput) || in_array('all', $mentionsInput)) {
                    $mentionedUserIds = User::where('id', '!=', Auth::id())->pluck('id')->toArray();
                } else {
                    $mentionedUserIds = array_filter($mentionsInput, function ($id) {
                        return is_numeric($id);
                    });
                }

                foreach ($mentionedUserIds as $mentionedUserId) {
                    Mention::create([
                        'created_by' => Auth::id(),
                        'mentionable_type' => User::class,
                        'mentionable_id' => $mentionedUserId,
                        'mentionable_in_type' => Conversation::class,
                        'mentionable_in_id' => $conversation->id,
                    ]);
                }
            }

            DB::commit();

            if ($conversation->status->value !== 'draft') {
                $mentionIds = collect($mentionedUserIds);
                $permIds = collect($permissionIds);
                
                $allRecipientIds = $mentionIds->merge(
                    User::whereHas('permissions', function ($query) use ($permIds) {
                        $query->whereIn('permissions.id', $permIds);
                    })->pluck('id')
                )->unique()->reject(fn($id) => $id == Auth::id());

                if ($allRecipientIds->isNotEmpty()) {
                    $users = User::whereIn('id', $allRecipientIds)->get();
                    $senderName = Auth::user()->firstname . ' ' . Auth::user()->lastname;

                    foreach ($users as $user) {
                        $isMentioned = $mentionIds->contains($user->id);
                        $type = $isMentioned ? 'mention' : 'permission';

                        if ($isMentioned) {
                            Mail::to($user->email)->send(new ConversationMentionMail($user, $conversation));
                        } else {
                            Mail::to($user->email)->send(new ConversationPermissionMail($user, $conversation));
                        }

                        $user->notify(new CommunityConversationNotification($conversation, $type, $senderName));
                    }
                }
            }

            if ($conversation->status->value === 'draft') {
                return redirect()
                    ->route('conversations.drafts')
                    ->with('success', 'Draft saved successfully.');
            } else {
                return redirect()
                    ->route('conversations.index')
                    ->with('success', 'Conversation published successfully.');
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->route('conversations.create')
                ->withInput()
                ->withErrors(['error' => 'An error occurred while creating the conversation. Please try again.']);
        }
    }

    /**
     * Display the specified conversation.
     */
    public function show($uuid, Request $request)
    {
        $conversation = Conversation::with([
            'user:id,firstname,lastname,username',
            'media',
            'permissions:id,name,is_customer',
            'mentions' => function ($query) {
                $query->where('mentionable_type', User::class)
                    ->with('mentionable:id,firstname,lastname,username');
            },
            'comments' => function ($query) {
                $query->with([
                    'user:id,firstname,lastname,username',
                    'media',
                    'replies' => function ($replyQuery) {
                        $replyQuery->with('user:id,firstname,lastname,username')
                            ->withCount('reactions');
                    },
                ])->withCount('reactions', 'replies')
                    ->whereNull('parent_id')
                    ->orderBy('created_at', 'desc');
            },
        ])
            ->withCount(['comments', 'reactions', 'shares'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        $conversation->user = $this->addUserAvatarUrls($conversation->user);
        $conversation->media = $conversation->getAllMediaUrls();

        if ($conversation->comments) {
            $conversation->comments->transform(function ($comment) {
                $comment->user = $this->addUserAvatarUrls($comment->user);

                if ($comment->replies) {
                    $comment->replies->transform(function ($reply) {
                        $reply->user = $this->addUserAvatarUrls($reply->user);

                        return $reply;
                    });
                }

                return $comment;
            });
        }

        $mentionedUsers = [];
        $isMentionEveryone = false;

        if ($conversation->mentions && $conversation->mentions->count() > 0) {
            $mentionedUserIds = $conversation->mentions
                ->where('mentionable_type', User::class)
                ->pluck('mentionable_id')
                ->toArray();

            $totalAvailableUsers = User::excludeSystemAdmins()->select('id', 'firstname', 'lastname', 'username')
                ->orderBy('firstname')
                ->count();

            $totalOtherUsers = $totalAvailableUsers - 1;

            $isMentionEveryone = (count($mentionedUserIds) === $totalOtherUsers);

            if (count($mentionedUserIds) > 0) {
                $mentionedUsers = User::whereIn('id', $mentionedUserIds)
                    ->select('id', 'firstname', 'lastname', 'username')
                    ->orderBy('firstname')
                    ->get()
                    ->map(function ($user) {
                        return $this->addUserAvatarUrls($user);
                    })
                    ->values()
                    ->toArray();
            }
        }

        $availableUsers = User::excludeSystemAdmins()
            ->select('id', 'firstname', 'lastname', 'username')
            ->orderBy('firstname')
            ->get()
            ->map(function ($user) {
                return $this->addUserAvatarUrls($user);
            })
            ->values()
            ->toArray();

        $staffPermissions = Permission::where('is_customer', false)
            ->select('id', 'name')
            ->orderBy('name')
            ->get()
            ->toArray();

        $customerPermissions = Permission::where('is_customer', true)
            ->select('id', 'name')
            ->orderBy('name')
            ->get()
            ->toArray();

        return inertia('backoffice/messaging/conversations/show', [
            'conversation' => $conversation,
            'mentionedUsers' => $mentionedUsers,
            'isMentionEveryone' => $isMentionEveryone,
            'availableUsers' => $availableUsers,
            'staffPermissions' => $staffPermissions,
            'customerPermissions' => $customerPermissions,
        ]);
    }

    /**
     * Show the form for editing the conversation.
     */
    public function edit($uuid, Request $request)
    {
        $conversation = Conversation::with([
            'user:id,firstname,lastname,username',
            'media',
            'permissions:id,name,is_customer',
            'mentions' => function ($query) {
                $query->where('mentionable_type', User::class)
                    ->with('mentionable:id,firstname,lastname,username');
            },
            'comments' => function ($query) {
                $query->with([
                    'user:id,firstname,lastname,username',
                    'media',
                    'replies' => function ($replyQuery) {
                        $replyQuery->with('user:id,firstname,lastname,username')
                            ->withCount('reactions');
                    },
                ])->withCount('reactions', 'replies')
                    ->whereNull('parent_id')
                    ->orderBy('created_at', 'desc');
            },
        ])
            ->withCount(['comments', 'reactions', 'shares'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        $conversation->user = $this->addUserAvatarUrls($conversation->user);
        $conversation->media = $conversation->getAllMediaUrls();

        if ($conversation->comments) {
            $conversation->comments->transform(function ($comment) {
                $comment->user = $this->addUserAvatarUrls($comment->user);

                if ($comment->replies) {
                    $comment->replies->transform(function ($reply) {
                        $reply->user = $this->addUserAvatarUrls($reply->user);

                        return $reply;
                    });
                }

                return $comment;
            });
        }

        $mentionedUsers = [];
        $isMentionEveryone = false;

        if ($conversation->mentions) {
            $mentionedUserIds = $conversation->mentions
                ->where('mentionable_type', User::class)
                ->pluck('mentionable_id')
                ->toArray();

            $totalAvailableUsers = User::excludeSystemAdmins()->select('id', 'firstname', 'lastname', 'username')
                ->orderBy('firstname')
                ->count();

            $totalOtherUsers = $totalAvailableUsers - 1;

            $isMentionEveryone = (count($mentionedUserIds) === $totalOtherUsers);

            if (count($mentionedUserIds) > 0) {
                $mentionedUsers = User::whereIn('id', $mentionedUserIds)
                    ->select('id', 'firstname', 'lastname', 'username')
                    ->get()
                    ->map(function ($user) {
                        return $this->addUserAvatarUrls($user);
                    });
            }
        }

        $availableUsers = User::excludeSystemAdmins()
            ->select('id', 'firstname', 'lastname', 'username')
            ->orderBy('firstname')
            ->get()
            ->map(function ($user) {
                return $this->addUserAvatarUrls($user);
            });

        $staffPermissions = Permission::where('is_customer', false)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $customerPermissions = Permission::where('is_customer', true)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return inertia('backoffice/messaging/conversations/edit', [
            'conversation' => $conversation,
            'mentionedUsers' => $mentionedUsers,
            'isMentionEveryone' => $isMentionEveryone,
            'availableUsers' => $availableUsers,
            'staffPermissions' => $staffPermissions,
            'customerPermissions' => $customerPermissions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Conversation $conversation)
    {
        if ($conversation->user_id !== Auth::id()) {
            return redirect()
                ->route('conversations.index')
                ->withErrors(['error' => 'You are not authorized to update this conversation.']);
        }

        DB::beginTransaction();

        try {
            $validatedData = $request->validated();

            $oldPermissionIds = $conversation->permissions()->pluck('permissions.id')->toArray();
            $oldMentionedUserIds = Mention::where('mentionable_in_type', Conversation::class)
                ->where('mentionable_in_id', $conversation->id)
                ->pluck('mentionable_id')
                ->toArray();

            $oldConversationData = $conversation->only([
                'title', 'content', 'description', 'status',
            ]);

            $conversationData = collect($validatedData)
                ->except(['media', 'mentions', 'permissions'])
                ->toArray();

            $contentChanged = false;
            foreach ($conversationData as $key => $value) {
                if (isset($oldConversationData[$key]) && $oldConversationData[$key] != $value) {
                    $contentChanged = true;
                    break;
                }
            }

            $conversation->update($conversationData);

            $permissions = $request->input('permissions', []);
            $newPermissionIds = [];
            if (! empty($permissions)) {
                $newPermissionIds = Permission::whereIn('id', $permissions)
                    ->where('name', '!=', 'SUPER ADMIN')
                    ->pluck('id')
                    ->toArray();

                $conversation->permissions()->sync($newPermissionIds);
            } else {
                $conversation->permissions()->detach();
            }

            $existingMediaToKeep = $request->input('existing_media', []);

            if (! empty($existingMediaToKeep)) {
                $conversation->media()->whereNotIn('id', $existingMediaToKeep)->delete();
            } else {
                $conversation->clearMediaCollection('conversations');
            }

            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $conversation->addMedia($file)
                        ->toMediaCollection('conversations');
                }
            }

            $newMentionedUserIds = [];
            if ($request->filled('mentions')) {
                $mentionsInput = $request->input('mentions');

                if (in_array('everyone', $mentionsInput) || in_array('all', $mentionsInput)) {
                    $newMentionedUserIds = User::where('id', '!=', Auth::id())->pluck('id')->toArray();
                } else {
                    $newMentionedUserIds = array_filter($mentionsInput, function ($id) {
                        return is_numeric($id);
                    });
                }

                Mention::where('mentionable_in_type', Conversation::class)
                    ->where('mentionable_in_id', $conversation->id)
                    ->delete();

                foreach ($newMentionedUserIds as $mentionedUserId) {
                    Mention::create([
                        'created_by' => Auth::id(),
                        'mentionable_type' => User::class,
                        'mentionable_id' => $mentionedUserId,
                        'mentionable_in_type' => Conversation::class,
                        'mentionable_in_id' => $conversation->id,
                    ]);
                }
            } else {
                Mention::where('mentionable_in_type', Conversation::class)
                    ->where('mentionable_in_id', $conversation->id)
                    ->delete();
            }

            DB::commit();

            if ($conversation->status->value !== 'draft') {
                $senderName = Auth::user()->firstname . ' ' . Auth::user()->lastname;
                
                $addedPermissions = array_diff($newPermissionIds, $oldPermissionIds);
                $removedPermissions = array_diff($oldPermissionIds, $newPermissionIds);
                $permissionsChanged = ! empty($addedPermissions) || ! empty($removedPermissions);

                $addedMentions = array_diff($newMentionedUserIds, $oldMentionedUserIds);
                $removedMentions = array_diff($oldMentionedUserIds, $newMentionedUserIds);
                $mentionsChanged = ! empty($addedMentions) || ! empty($removedMentions);

                $permRecipientIds = collect();
                $mentionRecipientIds = collect();

                if ($contentChanged) {
                    $permRecipientIds = User::whereHas('permissions', function ($query) use ($newPermissionIds) {
                            $query->whereIn('permissions.id', $newPermissionIds);
                        })->pluck('id');
                    $mentionRecipientIds = collect($newMentionedUserIds);
                } else {
                    if ($permissionsChanged && ! empty($addedPermissions)) {
                        $permRecipientIds = User::whereHas('permissions', function ($query) use ($addedPermissions) {
                            $query->whereIn('permissions.id', $addedPermissions);
                        })->pluck('id');
                    }
                    if ($mentionsChanged && ! empty($addedMentions)) {
                        $mentionRecipientIds = collect($addedMentions);
                    }
                }

                $allRecipientIds = $permRecipientIds->merge($mentionRecipientIds)
                    ->unique()
                    ->reject(fn($id) => $id == Auth::id());

                if ($allRecipientIds->isNotEmpty()) {
                    $users = User::whereIn('id', $allRecipientIds)->get();
                    foreach ($users as $user) {
                        $isMentioned = $mentionRecipientIds->contains($user->id);
                        $type = $isMentioned ? 'mention' : 'permission';

                        if ($isMentioned) {
                            Mail::to($user->email)->send(new ConversationUpdatedMentionMail($user, $conversation));
                        } else {
                            Mail::to($user->email)->send(new ConversationUpdatedPermissionMail($user, $conversation));
                        }

                        $user->notify(new CommunityConversationNotification($conversation, $type, $senderName));
                    }
                }
            }

            return back()->with('success', 'Conversation updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->route('conversations.edit', $conversation)
                ->withInput()
                ->withErrors(['error' => 'An error occurred while updating the conversation. Please try again.']);
        }
    }

    /**
     * Remove the specified conversation.
     */
    public function destroy(Conversation $conversation)
    {
        DB::beginTransaction();

        try {
            $title = $conversation->title;

            $conversation->delete();

            DB::commit();

            return back()->with('success', "The conversation \"{$title}\" has been deleted successfully.");
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'An error occurred while deleting the conversation.');
        }
    }

    /**
     * Bulk delete conversations.
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'conversation_ids' => 'required|array|min:1',
            'conversation_ids.*' => 'exists:conversations,id',
        ]);

        DB::beginTransaction();

        try {
            $count = Conversation::whereIn('id', $validated['conversation_ids'])->count();
            Conversation::whereIn('id', $validated['conversation_ids'])->delete();

            DB::commit();

            return redirect()
                ->route('back-office.conversations.index')
                ->with('success', "{$count} conversation(s) deleted successfully.");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->route('back-office.conversations.index')
                ->with('error', 'An error occurred during bulk deletion.');
        }
    }

    public function shareConversation($conversationId, Request $request)
    {
        $conversation = Conversation::where('id', $conversationId)
            ->orWhere('uuid', $conversationId)
            ->firstOrFail();

        Share::create([
            'user_id' => Auth::id(),
            'title' => $conversation->title,
            'conversation_id' => $conversation->id,
        ]);

        return redirect()->back()->with('success', 'Conversation shared successfully');
    }

    /**
     * Delete all draft conversations for the authenticated user
     */
    public function deleteAllDrafts()
    {
        DB::beginTransaction();

        try {
            $draftCount = Conversation::where('user_id', Auth::id())
                ->where('status', 'draft')
                ->count();

            if ($draftCount === 0) {
                return back()->with('info', 'No draft conversations found to delete.');
            }

            Conversation::where('user_id', Auth::id())
                ->where('status', 'draft')
                ->delete();

            DB::commit();

            return back()->with('success', "Successfully deleted {$draftCount} draft conversation(s).");
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'An error occurred while deleting draft conversations.');
        }
    }
}
