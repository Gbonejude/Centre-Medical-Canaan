<?php

namespace App\Http\Controllers\BackOffice\Notification;

use App\Http\Controllers\Controller;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->latest()->paginate(15);

        return inertia('Notifications/Index', compact('notifications'));
    }

    public function markasread($id)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
        }

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy($id)
    {
        try {
            $notification = Auth::user()->notifications()->findOrFail($id);
            $notification->delete();

            return redirect()->route('notifications.index')->with('success', 'Notification successfully deleted.');
        } catch (\Exception $e) {
            return redirect()->route('notifications.index')->with('error', 'An error occurred while deleting the notification. Please try again.');
        }
    }
}
