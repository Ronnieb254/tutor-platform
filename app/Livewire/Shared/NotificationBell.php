<?php

namespace App\Livewire\Shared;

use Livewire\Component;

class NotificationBell extends Component
{
    public int $unreadCount = 0;

    public function mount()
    {
        $this->loadCount();
    }

    public function loadCount()
    {
        $this->unreadCount = auth()->user()?->unreadNotifications()->count() ?? 0;
    }

    public function markAllRead()
    {
        auth()->user()?->unreadNotifications()->update(['read_at' => now()]);
        $this->unreadCount = 0;
    }

    public function render()
    {
        $notifications = auth()->user()
            ?->notifications()
            ->latest()
            ->take(6)
            ->get() ?? collect();

        return view('livewire.shared.notification-bell', compact('notifications'));
    }
}
