<?php

namespace App\Http\Controllers\BackOffice\Messaging\Comment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Messaging\Comment\StoreRequest;
use App\Http\Requests\Messaging\Comment\UpdateRequest;
use App\Models\Comment;
use App\Models\Conversation;
use App\Models\Mention;
use App\Models\Reaction;
use App\Models\User;
use App\Notifications\CommunityConversationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    /**
     * Display a listing of comments.
     */
    public function index(Request $request)
    {
        $query = Comment::with(['user', 'conversation', 'parent', 'media'])
            ->withCount(['replies', 'reactions']);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('firstname', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%");
                    })
                    ->orWhereHas('conversation', function ($convQuery) use ($search) {
                        $convQuery->where('title', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('conversation_id')) {
            $query->where('conversation_id', $request->get('conversation_id'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->get('user_id'));
        }

        if ($request->filled('comment_type')) {
            if ($request->get('comment_type') === 'root') {
                $query->whereNull('parent_id');
            } elseif ($request->get('comment_type') === 'replies') {
                $query->whereNotNull('parent_id');
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        if (in_array($sortField, ['title', 'created_at', 'updated_at'])) {
            $query->orderBy($sortField, $sortDirection);
        } elseif ($sortField === 'replies_count') {
            $query->orderBy('replies_count', $sortDirection);
        } elseif ($sortField === 'reactions_count') {
            $query->orderBy('reactions_count', $sortDirection);
        } else {
            $query->latest();
        }

        $comments = $query->paginate(20)->withQueryString();

        $conversations = Conversation::select('id', 'title')
            ->whereHas('comments')
            ->orderBy('title')
            ->get();

        $users = User::excludeSystemAdmins()
            ->select('id', 'firstname', 'lastname', 'username')
            ->whereHas('comments')
            ->orderBy('firstname')
            ->get();

        $commentTypeOptions = [
            ['value' => '', 'label' => 'All Comments'],
            ['value' => 'root', 'label' => 'Root Comments'],
            ['value' => 'replies', 'label' => 'Replies'],
        ];

        return inertia('backoffice/messaging/comments/index', [
            'comments' => $comments,
            'conversations' => $conversations,
            'users' => $users,
            'commentTypeOptions' => $commentTypeOptions,
            'filters' => [
                'search' => $request->get('search', ''),
                'conversation_id' => $request->get('conversation_id', ''),
                'user_id' => $request->get('user_id', ''),
                'comment_type' => $request->get('comment_type', ''),
                'date_from' => $request->get('date_from', ''),
                'date_to' => $request->get('date_to', ''),
                'sort' => $sortField,
                'direction' => $sortDirection,
            ],
        ]);
    }

    /**
     * Show the form for creating a new comment.
     */
    public function create(Request $request)
    {
        try {
            $users = User::excludeSystemAdmins()->active()
                ->select('id', 'firstname', 'lastname', 'username', 'email')
                ->orderBy('firstname')
                ->get();

            $conversations = Conversation::with('user:id,firstname,lastname,username')
                ->select('id', 'title', 'user_id')
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();

            $parentComment = null;
            if ($request->filled('parent_id')) {
                $parentComment = Comment::with(['user:id,firstname,lastname,username', 'conversation:id,title'])
                    ->select('id', 'title', 'content', 'user_id', 'conversation_id')
                    ->find($request->get('parent_id'));
            }

            $selectedConversation = null;
            if ($request->filled('conversation_id')) {
                $selectedConversation = Conversation::select('id', 'title')
                    ->find($request->get('conversation_id'));
            }

            return inertia('backoffice/messaging/comments/create', [
                'users' => $users,
                'conversations' => $conversations,
                'parentComment' => $parentComment,
                'selectedConversation' => $selectedConversation,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('back-office.comments.index')
                ->with('error', 'An error occurred while loading the create form.');
        }
    }

    /**
     * Store a newly created comment.
     */
    public function store(StoreRequest $request)
    {
        DB::beginTransaction();

        try {
            $comment = Comment::create($request->validated());

            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $comment->addMedia($file)
                        ->toMediaCollection('comments');
                }
            }

            if ($request->filled('mentions')) {
                $currentUserId = $request->input('user_id') ?? Auth::id();
                $senderName = Auth::user() ? Auth::user()->firstname . ' ' . Auth::user()->lastname : null;

                foreach ($request->input('mentions') as $mentionedUserId) {
                    Mention::create([
                        'user_id' => $currentUserId,
                        'mentionable_type' => User::class,
                        'mentionable_id' => $mentionedUserId,
                        'mentionable_in_type' => Comment::class,
                        'mentionable_in_id' => $comment->id,
                    ]);

                    $mentionedUser = User::find($mentionedUserId);
                    if ($mentionedUser && $mentionedUser->id !== Auth::id()) {
                        $mentionedUser->notify(new CommunityConversationNotification(
                            $comment->conversation,
                            'mention',
                            $senderName
                        ));
                    }
                }
            }

            if ($request->filled('reaction_type') && $request->filled('reaction_user_id')) {
                Reaction::create([
                    'user_id' => $request->input('reaction_user_id'),
                    'reactable_type' => Comment::class,
                    'reactable_id' => $comment->id,
                    'type' => $request->input('reaction_type'),
                ]);
            }

            DB::commit();

            return redirect()
                ->route('back-office.comments.show', $comment)
                ->with('success', 'Comment created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->route('back-office.comments.create')
                ->withInput()
                ->with('error', 'An error occurred while creating the comment. Please try again.');
        }
    }

    /**
     * Display the specified comment.
     */
    public function show(Comment $comment)
    {
        try {
            $comment->load([
                'user:id,firstname,lastname,username,email',
                'conversation.user:id,firstname,lastname,username',
                'parent.user:id,firstname,lastname,username',
                'replies.user:id,firstname,lastname,username',
                'reactions.user:id,firstname,lastname,username',
                'mentions.creator:id,firstname,lastname,username',
                'mentions.mentionable:id,firstname,lastname,username',
                'media',
            ]);

            $stats = [
                'total_replies' => $comment->replies()->count(),
                'total_reactions' => $comment->reactions()->count(),
                'total_mentions' => $comment->mentions()->count(),
                'media_count' => $comment->media()->count(),
                'depth_level' => method_exists($comment, 'getDepthLevel') ? $comment->getDepthLevel() : 0,
            ];

            return inertia('backoffice/messaging/comments/show', [
                'comment' => $comment,
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('back-office.comments.index')
                ->with('error', 'Comment not found.');
        }
    }

    /**
     * Show the form for editing the comment.
     */
    public function edit(Comment $comment)
    {
        try {
            $comment->load(['media', 'mentions.mentionable', 'reactions']);

            $users = User::excludeSystemAdmins()->active()
                ->select('id', 'firstname', 'lastname', 'username', 'email')
                ->orderBy('firstname')
                ->get();

            $conversations = Conversation::with('user:id,firstname,lastname,username')
                ->select('id', 'title', 'user_id')
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();

            $parentComments = Comment::where('conversation_id', $comment->conversation_id)
                ->where('id', '!=', $comment->id)
                ->whereNull('parent_id')
                ->with('user:id,firstname,lastname,username')
                ->select('id', 'title', 'content', 'user_id')
                ->orderBy('created_at', 'desc')
                ->get();

            $currentMentions = $comment->mentions()
                ->where('mentionable_type', User::class)
                ->pluck('mentionable_id')
                ->toArray();

            return inertia('backoffice/messaging/comments/edit', [
                'comment' => $comment,
                'users' => $users,
                'conversations' => $conversations,
                'parentComments' => $parentComments,
                'currentMentions' => $currentMentions,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('back-office.comments.index')
                ->with('error', 'An error occurred while retrieving the comment data.');
        }
    }

    /**
     * Update the specified comment.
     */
    public function update(UpdateRequest $request, Comment $comment)
    {
        DB::beginTransaction();

        try {
            $comment->update($request->validated());

            if ($request->filled('remove_media')) {
                $comment->media()
                    ->whereIn('id', $request->input('remove_media'))
                    ->each(function ($media) {
                        $media->delete();
                    });
            }

            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $comment->addMedia($file)
                        ->toMediaCollection('comments');
                }
            }

            if ($request->has('mentions')) {
                Mention::where('mentionable_in_type', Comment::class)
                    ->where('mentionable_in_id', $comment->id)
                    ->delete();

                $currentUserId = Auth::id();
                $senderName = Auth::user() ? Auth::user()->firstname . ' ' . Auth::user()->lastname : null;

                foreach ($request->input('mentions', []) as $mentionedUserId) {
                    Mention::create([
                        'user_id' => $currentUserId,
                        'mentionable_type' => User::class,
                        'mentionable_id' => $mentionedUserId,
                        'mentionable_in_type' => Comment::class,
                        'mentionable_in_id' => $comment->id,
                    ]);

                    $mentionedUser = User::find($mentionedUserId);
                    if ($mentionedUser && $mentionedUser->id !== Auth::id()) {
                        $mentionedUser->notify(new CommunityConversationNotification(
                            $comment->conversation,
                            'mention',
                            $senderName
                        ));
                    }
                }
            }

            if ($request->has('reaction_type') && $request->has('reaction_user_id')) {
                if ($request->filled('reaction_type') && $request->filled('reaction_user_id')) {
                    Reaction::where('reactable_type', Comment::class)
                        ->where('reactable_id', $comment->id)
                        ->where('user_id', $request->input('reaction_user_id'))
                        ->delete();

                    Reaction::create([
                        'user_id' => $request->input('reaction_user_id'),
                        'reactable_type' => Comment::class,
                        'reactable_id' => $comment->id,
                        'type' => $request->input('reaction_type'),
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('back-office.comments.show', $comment)
                ->with('success', 'Comment updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->route('back-office.comments.edit', $comment)
                ->withInput()
                ->with('error', 'An error occurred while updating the comment. Please try again.');
        }
    }

    /**
     * Remove the specified comment.
     */
    public function destroy(Comment $comment)
    {
        DB::beginTransaction();

        try {
            $title = $comment->title;

            $comment->delete();

            DB::commit();

            return redirect()
                ->route('back-office.comments.index')
                ->with('success', "The comment \"{$title}\" has been deleted successfully.");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->route('back-office.comments.index')
                ->with('error', 'An error occurred while deleting the comment.');
        }
    }

    /**
     * Bulk delete comments.
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'comment_ids' => 'required|array|min:1',
            'comment_ids.*' => 'exists:comments,id',
        ]);

        DB::beginTransaction();

        try {
            $count = Comment::whereIn('id', $validated['comment_ids'])->count();
            Comment::whereIn('id', $validated['comment_ids'])->delete();

            DB::commit();

            return redirect()
                ->route('back-office.comments.index')
                ->with('success', "{$count} comment(s) deleted successfully.");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->route('back-office.comments.index')
                ->with('error', 'An error occurred during bulk deletion.');
        }
    }

    /**
     * Display a listing of comments for a specific conversation.
     */
    public function indexByConversation($conversationId, Request $request)
    {
        $conversation = Conversation::where('id', $conversationId)
            ->orWhere('uuid', $conversationId)
            ->firstOrFail();

        $comments = $conversation->comments()
            ->with([
                'user:id,firstname,lastname,username',
                'replies.user:id,firstname,lastname,username',
            ])
            ->withCount(['replies', 'reactions'])
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $user = Auth::user();
        if ($user) {
            $comments->each(function ($comment) use ($user) {
                $comment->user_has_reacted = $comment->reactions()
                    ->where('user_id', $user->id)
                    ->where('type', 'like')
                    ->exists();

                $comment->replies->each(function ($reply) use ($user) {
                    $reply->user_has_reacted = $reply->reactions()
                        ->where('user_id', $user->id)
                        ->where('type', 'like')
                        ->exists();
                });
            });
        }

        return response()->json([
            'comments' => $comments,
            'conversation' => $conversation->only(['id', 'uuid', 'title']),
        ]);
    }

    public function storeByConversation($conversationId, Request $request)
    {
        $conversation = Conversation::where('id', $conversationId)
            ->orWhere('uuid', $conversationId)
            ->firstOrFail();

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment = $conversation->comments()->create([
            'content' => $request->input('content'),
            'title' => $request->input('content'),
            'user_id' => Auth::id(),
            'parent_id' => null,
        ]);

        // Notify conversation owner
        $conversationOwner = $conversation->user;
        if ($conversationOwner && $conversationOwner->id !== Auth::id()) {
            $senderName = Auth::user()->firstname . ' ' . Auth::user()->lastname;
            $conversationOwner->notify(new CommunityConversationNotification($conversation, 'comment', $senderName));
        }

        return redirect()->back()->with('success', 'Comment posted successfully');
    }

    public function storeReply($commentId, Request $request)
    {
        $parentComment = Comment::findOrFail($commentId);

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $parentId = $parentComment->parent_id ? $parentComment->parent_id : $parentComment->id;

        $reply = Comment::create([
            'content' => $request->input('content'),
            'title' => $request->input('content'),
            'user_id' => Auth::id(),
            'conversation_id' => $parentComment->conversation_id,
            'parent_id' => $parentId,
        ]);

        $conversation = $parentComment->conversation;
        $currentUser = Auth::user();

        // Notify parent comment owner
        $parentOwner = $parentComment->user;
        if ($parentOwner && $parentOwner->id !== $currentUser->id) {
            $senderName = $currentUser->firstname . ' ' . $currentUser->lastname;
            $parentOwner->notify(new CommunityConversationNotification($conversation, 'reply', $senderName));
        }

        // Notify conversation owner if different from parent owner
        $conversationOwner = $conversation->user;
        if ($conversationOwner && $conversationOwner->id !== $currentUser->id && (!$parentOwner || $conversationOwner->id !== $parentOwner->id)) {
            $senderName = $currentUser->firstname . ' ' . $currentUser->lastname;
            $conversationOwner->notify(new CommunityConversationNotification($conversation, 'comment', $senderName));
        }

        return redirect()->back()->with('success', 'Reply posted successfully');
    }

    public function toggleReaction($commentId, Request $request)
    {
        $comment = Comment::findOrFail($commentId);
        $user = Auth::user();
        $type = $request->get('type', 'like');

        $existingReaction = $comment->reactions()
            ->where('user_id', $user->id)
            ->where('type', $type)
            ->first();

        if ($existingReaction) {
            $existingReaction->delete();
            $liked = false;
            $message = 'Reaction removed';
        } else {
            $comment->reactions()->create([
                'user_id' => $user->id,
                'type' => $type,
            ]);
            $liked = true;
            $message = 'Reaction added';

            // Notify comment owner
            $commentOwner = $comment->user;
            if ($commentOwner && $commentOwner->id !== $user->id) {
                $senderName = $user->firstname . ' ' . $user->lastname;
                $commentOwner->notify(new CommunityConversationNotification($comment->conversation, 'reaction', $senderName));
            }
        }

        return redirect()->back()->with('success', $message);
    }

    public function toggleConversationReaction($conversationId, Request $request)
    {
        $conversation = Conversation::findOrFail($conversationId);
        $user = Auth::user();
        $type = $request->get('type', 'like');

        $existingReaction = $conversation->reactions()
            ->where('user_id', $user->id)
            ->where('type', $type)
            ->first();

        if ($existingReaction) {
            $existingReaction->delete();
            $message = 'Reaction removed';
        } else {
            $conversation->reactions()->create([
                'user_id' => $user->id,
                'type' => $type,
            ]);
            $message = 'Reaction added';

            // Notify conversation owner
            $conversationOwner = $conversation->user;
            if ($conversationOwner && $conversationOwner->id !== $user->id) {
                $senderName = $user->firstname . ' ' . $user->lastname;
                $conversationOwner->notify(new CommunityConversationNotification($conversation, 'reaction', $senderName));
            }
        }

        return redirect()->back()->with('success', $message);
    }
}
