<div wire:poll.10s>
    <div class="space-y-3 max-h-64 overflow-y-auto mb-4 pr-1" id="chat-messages">
        @forelse($messages as $message)
        @php $isMine = $message->sender_id === auth()->id(); @endphp
        <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
            <div class="max-w-[75%]">
                @if(!$isMine)
                <p class="text-xs text-muted mb-1">{{ $message->sender->name }}</p>
                @endif
                <div class="px-4 py-2.5 rounded-2xl text-sm
                    {{ $isMine ? 'bg-Trust Blue text-white rounded-br-sm' : 'bg-cream text-ink rounded-bl-sm border border-hairline' }}">
                    {{ $message->body }}
                </div>
                <p class="text-[10px] text-muted mt-1 {{ $isMine ? 'text-right' : '' }}">
                    {{ $message->created_at->diffForHumans() }}
                </p>
            </div>
        </div>
        @empty
        <p class="text-center text-sm text-muted py-4">No messages yet. Start the conversation!</p>
        @endforelse
    </div>

    <form wire:submit="sendMessage" class="flex gap-2">
        <input type="text" wire:model="newMessage"
               class="form-input flex-1 h-11"
               placeholder="Type a message…">
        <button type="submit" class="pill-btn-primary px-5 py-2">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
        </button>
    </form>
</div>
