<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    // 通知一覧
    public function index(): View
    {
        $notifications = auth()->user()->notifications()->latest()->paginate(10);

        return view('notifications.index', compact('notifications'));
    }

    // 通知を既読にする
    public function markAsRead(string $notification): RedirectResponse
    {
        $user = auth()->user();
        $notification = $user->notifications()->where('id', $notification)->firstOrFail();
        $notification->markAsRead();

        return redirect()->route('notifications.index')->with('success', '通知を既読にしました。');
    }
}
