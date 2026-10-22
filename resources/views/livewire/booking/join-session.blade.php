<div>
    @if(!$this->canJoin())
    <div class="card p-6 text-center">
        <div class="text-4xl mb-3">🕐</div>
        <h3 class="font-serif text-xl font-semibold text-ink mb-2">Session not yet available</h3>
        <p class="text-muted text-sm">Your session is on
            <strong class="text-ink">{{ $booking->scheduled_at->format('D, M j \a\t g:ia') }}</strong>.
        </p>
        <p class="text-sm text-muted mt-1">The Join button becomes available 15 minutes before your session.</p>
        <div class="mt-4 bg-cream rounded-2xl p-4">
            <p class="text-xs text-muted">Time until session</p>
            <p class="font-serif text-2xl font-bold text-Trust Blue mt-1" id="countdown"></p>
        </div>
    </div>
    @elseif(!$codeVerified)
    <div class="card p-6">
        <h3 class="font-serif text-xl font-semibold text-ink mb-2">Enter your session code</h3>
        <p class="text-muted text-sm mb-5">
            Check your email for the access code that was sent when your booking was confirmed.
        </p>
        <div class="space-y-4">
            <div>
                <label class="form-label">Session access code</label>
                <input type="text" wire:model="enteredCode"
                       class="form-input text-center font-mono text-lg tracking-widest uppercase"
                       placeholder="TUT-XXXXXX or STU-XXXXXX"
                       maxlength="10">
                @if($codeError)
                <p class="form-error mt-2">{{ $codeError }}</p>
                @endif
            </div>
            <button wire:click="verifyCode" class="w-full pill-btn-primary py-3">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                Join Session
            </button>
        </div>
    </div>
    @else
    {{-- Jitsi Embed --}}
    <div>
        <div class="flex items-center gap-2 mb-4">
            <span class="w-2.5 h-2.5 rounded-full bg-mint animate-pulse"></span>
            <span class="text-sm font-semibold text-mint">Live Session Active</span>
        </div>
        <div id="jitsi-container" class="w-full rounded-3xl overflow-hidden bg-ink" style="height:600px;"></div>
    </div>
    @endif
</div>

@push('scripts')
<script src="https://{{ config('jitsi.domain', 'meet.jit.si') }}/external_api.js"></script>
<script>
document.addEventListener('livewire:initialized', () => {
    Livewire.on('launch-jitsi', (config) => {
        const options = {
            roomName: config.room,
            parentNode: document.getElementById('jitsi-container'),
            userInfo: { displayName: config.userName, email: config.userEmail },
            configOverwrite: {
                startWithAudioMuted: false,
                startWithVideoMuted: false,
                disableDeepLinking: true,
            },
            interfaceConfigOverwrite: {
                TOOLBAR_BUTTONS: ['microphone','camera','closedcaptions','desktop','fullscreen','hangup','chat','recording','raisehand','videoquality','filmstrip','tileview'],
                SHOW_JITSI_WATERMARK: false,
            },
        };
        if (config.jwt) options.jwt = config.jwt;
        new JitsiMeetExternalAPI(config.domain, options);
    });
});

// Countdown timer
(function() {
    const el = document.getElementById('countdown');
    if (!el) return;
    const target = new Date('{{ $booking->scheduled_at->toISOString() }}');
    setInterval(() => {
        const diff = target - new Date();
        if (diff <= 0) { el.textContent = 'Starting now!'; return; }
        const h = Math.floor(diff/3600000);
        const m = Math.floor((diff%3600000)/60000);
        const s = Math.floor((diff%60000)/1000);
        el.textContent = `${h}h ${m}m ${s}s`;
    }, 1000);
})();
</script>
@endpush
