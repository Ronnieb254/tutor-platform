<div x-data="{ open: false }" class="relative" wire:poll.30s="loadCount">
    <button @click="open = !open" @click.outside="open = false"
            class="relative w-10 h-10 rounded-full border border-hairline hover:border-Trust Blue/40 flex items-center justify-center transition">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
        </svg>
        @if($unreadCount > 0)
        <span class="absolute -top-0.5 -right-0.5 w-4 h-4 rounded-full bg-coral text-white text-[9px] font-bold flex items-center justify-center">
            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
        </span>
        @endif
    </button>

    <div x-show="open" x-transition x-cloak
         class="absolute right-0 top-full mt-2 w-80 bg-white rounded-2xl border border-hairline shadow-card-hover z-50 overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b border-hairline">
            <span class="font-semibold text-sm text-ink">Notifications</span>
            @if($unreadCount > 0)
            <button wire:click="markAllRead" class="text-xs text-Trust Blue hover:underline">Mark all read</button>
            @endif
        </div>
        <div class="max-h-72 overflow-y-auto divide-y divide-hairline">
            @forelse($notifications as $notification)
            <div class="px-4 py-3 hover:bg-cream/50 transition {{ is_null($notification->read_at) ? 'bg-Trust Blue/5' : '' }}">
                <p class="text-sm text-ink font-medium">{{ $notification->data['message'] ?? 'New notification' }}</p>
                @if(!empty($notification->data['code']))
                <p class="text-xs font-mono text-Trust Blue mt-0.5">Code: {{ $notification->data['code'] }}</p>
                @endif
                <p class="text-xs text-muted mt-1">{{ $notification->created_at->diffForHumans() }}</p>
            </div>
            @empty
            <div class="px-4 py-8 text-center text-muted text-sm">No notifications yet</div>
            @endforelse
        </div>
    </div>
</div>
