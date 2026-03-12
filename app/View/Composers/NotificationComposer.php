<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationComposer
{
    public function compose(View $view)
    {
        if (Auth::check()) {
            $unreadNotifications = Notification::where('user_id', Auth::id())
                ->where('is_read', false)
                ->count();

            $recentNotifications = Notification::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $view->with([
                'unreadNotifications' => $unreadNotifications,
                'recentNotifications' => $recentNotifications
            ]);
        } else {
            $view->with([
                'unreadNotifications' => 0,
                'recentNotifications' => collect([])
            ]);
        }
    }
}