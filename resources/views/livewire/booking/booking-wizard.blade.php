<div>
    {{-- Step indicator --}}
    <div class="flex items-center justify-center gap-2 mb-8">
        @foreach(['Pick time', 'Review', 'Confirmed'] as $i => $label)
        <div class="flex items-center gap-2 {{ !$loop->first ? 'ml-2' : '' }}">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                {{ $step > $i+1 ? 'bg-mint text-white' : ($step === $i+1 ? 'bg-violet text-white' : 'bg-hairline text-muted') }}">
                {{ $step > $i+1 ? '✓' : $i+1 }}
            </div>
            <span class="text-xs font-medium {{ $step === $i+1 ? 'text-ink' : 'text-muted' }}">{{ $label }}</span>
            @if(!$loop->last)<div class="w-8 h-px bg-hairline"></div>@endif
        </div>
        @endforeach
    </div>

    {{-- STEP 1: Pick duration + date/time --}}
    @if($step === 1)
    <div class="space-y-6">
        <div>
            <label class="form-label">Session duration</label>
            <div class="grid grid-cols-4 gap-2">
                @foreach([15, 30, 45, 60] as $mins)
                <label class="cursor-pointer">
                    <input type="radio" wire:model="duration" value="{{ $mins }}" class="sr-only">
                    <div class="rounded-2xl border-2 p-3 text-center transition
                        {{ $duration == $mins ? 'border-violet bg-violet/5' : 'border-hairline hover:border-violet/40' }}">
                        <p class="font-bold text-ink text-base">{{ $mins }}m</p>
                        <p class="text-xs text-muted mt-0.5">KSh {{ number_format($tutorProfile->getPriceForDuration($mins), 0) }}</p>
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="form-label">Date</label>
                <input type="date" wire:model="scheduledDate"
                       min="{{ now()->addDay()->format('Y-m-d') }}"
                       class="form-input">
                @error('scheduledDate')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Time</label>
                <input type="time" wire:model="scheduledTime" class="form-input">
                @error('scheduledTime')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>

        @if($subjects->isNotEmpty())
        <div>
            <label class="form-label">Subject (optional)</label>
            <select wire:model="subjectId" class="form-input">
                <option value="">Select a subject</option>
                @foreach($subjects as $subject)
                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                @endforeach
            </select>
        </div>
        @endif

        @if(auth()->user()?->isParent())
        <div>
            <label class="form-label">Booking for</label>
            <select wire:model="bookingFor" class="form-input">
                <option value="self">Myself</option>
                @foreach($children as $child)
                <option value="child:{{ $child->id }}">{{ $child->name }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <div>
            <label class="form-label">Notes for tutor (optional)</label>
            <textarea wire:model="notes" rows="2" class="form-input resize-none" placeholder="Any specific topics or requirements…"></textarea>
        </div>

        <button wire:click="nextStep" class="w-full pill-btn-primary py-3">
            Continue to Review →
        </button>
    </div>
    @endif

    {{-- STEP 2: Review & Pay --}}
    @if($step === 2)
    <div class="space-y-5">
        <div class="card p-5 space-y-3">
            <h3 class="font-serif text-lg font-semibold text-ink">Session summary</h3>
            <div class="divide-y divide-hairline">
                @foreach([
                    ['Tutor', $tutorProfile->user->name],
                    ['Duration', $duration . ' minutes'],
                    ['Date & Time', \Carbon\Carbon::parse($scheduledDate . ' ' . $scheduledTime)->format('D, M j Y \a\t g:ia')],
                    ['Session fee', 'KSh ' . number_format($this->sessionPrice, 0)],
                ] as [$label, $value])
                <div class="flex items-center justify-between py-2.5">
                    <span class="text-sm text-muted">{{ $label }}</span>
                    <span class="text-sm font-semibold text-ink">{{ $value }}</span>
                </div>
                @endforeach
            </div>
            <div class="flex items-center justify-between pt-2 border-t-2 border-ink">
                <span class="font-bold text-ink">Total</span>
                <span class="font-bold text-xl text-ink">KSh {{ number_format($this->sessionPrice, 0) }}</span>
            </div>
        </div>

        <div class="rounded-2xl bg-violet/5 border border-violet/20 p-4 text-sm text-violet">
            <p class="font-semibold mb-1">📧 After payment</p>
            <p class="text-violet/80">You'll receive an email with your unique session access code (STU-XXXXXX) and the session details.</p>
        </div>

        <div class="flex gap-3">
            <button wire:click="prevStep" class="flex-1 pill-btn-outline py-3">← Back</button>
            <button wire:click="initPayment"
                    wire:loading.attr="disabled"
                    class="flex-1 pill-btn-primary py-3 relative">
                <span wire:loading.remove wire:target="initPayment">
                    Pay KSh {{ number_format($this->sessionPrice, 0) }}
                </span>
                <span wire:loading wire:target="initPayment">Processing…</span>
            </button>
        </div>
    </div>
    @endif

    {{-- STEP 3: Confirmed --}}
    @if($step === 3 && $bookingConfirmed)
    <div class="text-center space-y-6">
        <div class="w-16 h-16 rounded-full bg-mint/20 flex items-center justify-center mx-auto">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div>
            <h3 class="font-serif text-2xl font-bold text-ink mb-2">Booking confirmed!</h3>
            <p class="text-muted">Check your email for full details. Your session access code is:</p>
        </div>

        <div class="session-code-box">
            <p class="text-sm font-semibold text-muted uppercase tracking-widest mb-2">Your Session Access Code</p>
            <p class="session-code-text">{{ $studentCode }}</p>
            <p class="text-sm text-muted mt-2">Use this code to enter your live session on the day</p>
        </div>

        <a href="{{ route('bookings.show', $bookingUuid) }}" class="pill-btn-primary w-full py-3 block">
            View Booking Details
        </a>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('livewire:initialized', () => {
    Livewire.on('open-paystack', (data) => {
        const handler = PaystackPop.setup({
            key: '{{ config('paystack.public_key') }}',
            email: data.email,
            amount: data.amount,
            currency: 'KES',
            ref: data.reference,
            access_code: data.access_code,
            onSuccess: function(transaction) {
                @this.paymentSuccess(transaction.reference);
            },
            onCancel: function() {
                console.log('Payment cancelled');
            }
        });
        handler.openIframe();
    });
});
</script>
<script src="https://js.paystack.co/v1/inline.js"></script>
@endpush
