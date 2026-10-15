@php
$sessionTiers = [
    15 => ['name' => 'Quick Help',      'price' => 300, 'icon' => '⚡', 'desc' => 'Rapid revision or a quick question'],
    30 => ['name' => 'Concept Check',   'price' => 500, 'icon' => '📘', 'desc' => 'Targeted practice or concept clarity'],
    45 => ['name' => 'Guided Practice', 'price' => 680, 'icon' => '🎯', 'desc' => 'Full structured lesson with Q&A'],
    60 => ['name' => 'Deep Dive',       'price' => 800, 'icon' => '🏆', 'desc' => 'Maximum learning, best value'],
];
@endphp

<div>
    {{-- Step indicator --}}
    <div style="display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:24px;">
        @foreach(['Pick Session','Review','Confirmed'] as $i => $label)
        <div style="display:flex;align-items:center;gap:6px;">
            @if(!$loop->first)<div style="width:24px;height:1px;background:{{ $step > $i ? 'var(--violet)' : 'rgba(21,40,31,.12)' }};"></div>@endif
            <div style="display:flex;align-items:center;gap:5px;">
                <div style="width:26px;height:26px;border-radius:999px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;
                    background:{{ $step > $i+1 ? 'var(--mint)' : ($step === $i+1 ? 'var(--violet)' : 'rgba(21,40,31,.08)') }};
                    color:{{ ($step > $i+1 || $step === $i+1) ? '#fff' : 'var(--muted)' }};">
                    {{ $step > $i+1 ? '✓' : $i+1 }}
                </div>
                <span style="font-size:12px;font-weight:{{ $step === $i+1 ? '700' : '500' }};color:{{ $step === $i+1 ? 'var(--ink)' : 'var(--muted)' }};">{{ $label }}</span>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── STEP 1: Pick duration + date/time ── --}}
    @if($step === 1)
    <div style="display:flex;flex-direction:column;gap:18px;">

        {{-- Duration selector --}}
        <div>
            <p style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:10px;">
                <i class="fa-solid fa-clock fa-xs" style="color:var(--violet);margin-right:4px;"></i> Choose Session Type
            </p>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px;">
                @foreach($sessionTiers as $mins => $tier)
                <label style="cursor:pointer;">
                    <input type="radio" wire:model.live="duration" value="{{ $mins }}" style="display:none;">
                    <div style="border:2px solid {{ $duration == $mins ? 'var(--violet)' : 'rgba(21,40,31,.12)' }};
                                background:{{ $duration == $mins ? 'rgba(109,40,217,.05)' : '#fff' }};
                                border-radius:14px;padding:14px 12px;transition:all .18s;text-align:left;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                            <div style="display:flex;align-items:center;gap:6px;">
                                <span style="font-size:18px;">{{ $tier['icon'] }}</span>
                                <span style="font-size:14px;font-weight:700;color:var(--ink);">{{ $tier['name'] }}</span>
                            </div>
                            <span style="font-family:'Fraunces',serif;font-size:16px;font-weight:800;color:{{ $duration == $mins ? 'var(--violet)' : 'var(--ink)' }};">KSh {{ $tier['price'] }}</span>
                        </div>
                        <div style="font-size:11px;color:var(--muted);">{{ $mins }} min — {{ $tier['desc'] }}</div>
                    </div>
                </label>
                @endforeach
            </div>
            @error('duration')<p style="font-size:12px;color:var(--coral);margin-top:4px;"><i class="fa-solid fa-triangle-exclamation fa-xs"></i> {{ $message }}</p>@enderror
        </div>

        {{-- Date + Time --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <div>
                <p style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:6px;">
                    <i class="fa-regular fa-calendar fa-xs" style="color:var(--violet);margin-right:4px;"></i> Date
                </p>
                <input type="date" wire:model="scheduledDate"
                       min="{{ now()->addDay()->format('Y-m-d') }}"
                       style="width:100%;height:46px;padding:0 14px;border-radius:12px;border:1.5px solid rgba(21,40,31,.18);font-size:14px;font-family:inherit;outline:none;background:#fff;"
                       onfocus="this.style.borderColor='var(--violet)'" onblur="this.style.borderColor='rgba(21,40,31,.18)'">
                @error('scheduledDate')<p style="font-size:12px;color:var(--coral);margin-top:4px;">{{ $message }}</p>@enderror
            </div>
            <div>
                <p style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:6px;">
                    <i class="fa-regular fa-clock fa-xs" style="color:var(--violet);margin-right:4px;"></i> Time
                </p>
                <input type="time" wire:model="scheduledTime"
                       style="width:100%;height:46px;padding:0 14px;border-radius:12px;border:1.5px solid rgba(21,40,31,.18);font-size:14px;font-family:inherit;outline:none;background:#fff;"
                       onfocus="this.style.borderColor='var(--violet)'" onblur="this.style.borderColor='rgba(21,40,31,.18)'">
                @error('scheduledTime')<p style="font-size:12px;color:var(--coral);margin-top:4px;">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Subject --}}
        @if($subjects->isNotEmpty())
        <div>
            <p style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:6px;">
                <i class="fa-solid fa-book-open fa-xs" style="color:var(--violet);margin-right:4px;"></i> Subject <span style="font-weight:400;text-transform:none;">(optional)</span>
            </p>
            <select wire:model="subjectId"
                    style="width:100%;height:46px;padding:0 14px;border-radius:12px;border:1.5px solid rgba(21,40,31,.18);font-size:14px;font-family:inherit;outline:none;background:#fff;appearance:none;"
                    onfocus="this.style.borderColor='var(--violet)'" onblur="this.style.borderColor='rgba(21,40,31,.18)'">
                <option value="">Select a subject…</option>
                @foreach($subjects as $subject)
                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                @endforeach
            </select>
        </div>
        @endif

        {{-- Notes --}}
        <div>
            <p style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:6px;">
                <i class="fa-solid fa-note-sticky fa-xs" style="color:var(--violet);margin-right:4px;"></i> Notes for tutor <span style="font-weight:400;text-transform:none;">(optional)</span>
            </p>
            <textarea wire:model="notes" rows="2"
                      placeholder="Topics to cover, past exam areas, specific questions…"
                      style="width:100%;padding:10px 14px;border-radius:12px;border:1.5px solid rgba(21,40,31,.18);font-size:14px;font-family:inherit;outline:none;resize:none;line-height:1.6;background:#fff;"
                      onfocus="this.style.borderColor='var(--violet)'" onblur="this.style.borderColor='rgba(21,40,31,.18)'"></textarea>
        </div>

        <button wire:click="nextStep"
                style="width:100%;height:50px;border-radius:999px;background:var(--violet);color:#fff;font-size:15px;font-weight:700;border:none;cursor:pointer;font-family:inherit;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:8px;"
                onmouseover="this.style.background='#4C1D95'" onmouseout="this.style.background='var(--violet)'">
            Continue to Review
            <i class="fa-solid fa-arrow-right fa-sm"></i>
        </button>
    </div>
    @endif

    {{-- ── STEP 2: Review & Pay ── --}}
    @if($step === 2)
    @php $tier = $sessionTiers[$duration] ?? $sessionTiers[60]; @endphp
    <div style="display:flex;flex-direction:column;gap:14px;">

        {{-- Summary card --}}
        <div style="background:#F9FAF9;border-radius:16px;border:1px solid var(--hair);padding:20px;">
            <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:14px;">Session Summary</p>
            <div style="display:flex;flex-direction:column;gap:0;">
                @foreach([
                    ['Tutor', $tutorProfile->user->name],
                    ['Session type', $tier['icon'].' '.$tier['name'].' ('.$duration.' min)'],
                    ['Date', \Carbon\Carbon::parse($scheduledDate)->format('D, M j, Y')],
                    ['Time', \Carbon\Carbon::parse($scheduledTime)->format('g:i a').' (EAT)'],
                    ['Subject', $subjectId ? ($subjects->find($subjectId)?->name ?? '—') : 'General'],
                ] as [$label, $value])
                <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid rgba(21,40,31,.06);">
                    <span style="font-size:13px;color:var(--muted);">{{ $label }}</span>
                    <span style="font-size:13px;font-weight:600;color:var(--ink);">{{ $value }}</span>
                </div>
                @endforeach
                <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 0 0;">
                    <span style="font-size:15px;font-weight:700;color:var(--ink);">Total</span>
                    <span style="font-family:'Fraunces',serif;font-size:24px;font-weight:800;color:var(--violet);">KSh {{ $tier['price'] }}</span>
                </div>
            </div>
        </div>

        {{-- Code notice --}}
        <div style="background:rgba(109,40,217,.05);border:1px solid rgba(109,40,217,.15);border-radius:14px;padding:14px 16px;">
            <p style="font-size:13px;font-weight:700;color:var(--violet);margin-bottom:4px;">
                <i class="fa-solid fa-key fa-xs" style="margin-right:5px;"></i> After payment
            </p>
            <p style="font-size:12px;color:var(--muted);line-height:1.65;">You'll receive a unique <strong style="color:var(--ink);">STU-XXXXXX</strong> session access code by email and SMS. Use it to enter your live class on session day.</p>
        </div>

        <div style="display:flex;gap:10px;">
            <button wire:click="prevStep"
                    style="flex:1;height:48px;border-radius:999px;background:#fff;color:var(--ink);border:2px solid rgba(21,40,31,.18);font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;transition:all .18s;">
                <i class="fa-solid fa-arrow-left fa-xs"></i> Back
            </button>
            <button wire:click="initPayment"
                    wire:loading.attr="disabled"
                    style="flex:2;height:48px;border-radius:999px;background:var(--violet);color:#fff;font-size:15px;font-weight:700;border:none;cursor:pointer;font-family:inherit;transition:all .18s;display:flex;align-items:center;justify-content:center;gap:8px;"
                    onmouseover="this.style.background='#4C1D95'" onmouseout="this.style.background='var(--violet)'">
                <span wire:loading.remove wire:target="initPayment">
                    <i class="fa-solid fa-credit-card fa-sm"></i>
                    Pay KSh {{ $tier['price'] }}
                </span>
                <span wire:loading wire:target="initPayment" style="display:flex;align-items:center;gap:6px;">
                    <i class="fa-solid fa-spinner fa-spin fa-sm"></i> Processing…
                </span>
            </button>
        </div>
        <p style="font-size:11px;text-align:center;color:var(--muted);">
            <i class="fa-solid fa-lock fa-xs" style="color:var(--mint);"></i>
            Secured by Paystack · M-Pesa, Visa & Mastercard accepted
        </p>
    </div>
    @endif

    {{-- ── STEP 3: Confirmed ── --}}
    @if($step === 3 && $bookingConfirmed)
    <div style="text-align:center;padding:8px 0;">
        <div style="width:64px;height:64px;border-radius:999px;background:rgba(16,185,129,.12);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <i class="fa-solid fa-circle-check" style="font-size:28px;color:var(--mint);"></i>
        </div>
        <h3 style="font-family:'Fraunces',serif;font-size:22px;color:var(--ink);margin-bottom:8px;">Booking Confirmed!</h3>
        <p style="font-size:14px;color:var(--muted);margin-bottom:22px;">Check your email and SMS for full session details.</p>

        {{-- Session code --}}
        <div style="background:rgba(109,40,217,.06);border:2px solid rgba(109,40,217,.25);border-radius:18px;padding:20px;margin-bottom:18px;">
            <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);margin-bottom:10px;">
                <i class="fa-solid fa-key" style="color:var(--violet);margin-right:4px;"></i> Your Session Access Code
            </p>
            <p style="font-family:monospace;font-size:32px;font-weight:800;color:var(--violet);letter-spacing:.12em;line-height:1;">{{ $studentCode }}</p>
            <p style="font-size:12px;color:var(--muted);margin-top:8px;">Enter this code to join your live session on the day</p>
        </div>

        <a href="{{ route('bookings.show', $bookingUuid) }}"
           style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;height:50px;border-radius:999px;background:var(--violet);color:#fff;font-size:15px;font-weight:700;text-decoration:none;">
            <i class="fa-solid fa-calendar-check fa-sm"></i> View Booking Details
        </a>
    </div>
    @endif
</div>

@push('scripts')
<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
document.addEventListener('livewire:initialized', () => {
    Livewire.on('open-paystack', (data) => {
        const handler = PaystackPop.setup({
            key: '{{ config('paystack.public_key') }}',
            email: data.email,
            amount: data.amount,
            currency: 'KES',
            ref: data.reference,
            metadata: { cancel_action: window.location.href },
            onSuccess: function(transaction) {
                @this.paymentSuccess(transaction.reference);
            },
            onCancel: function() {
                // User cancelled, stay on step 2
            }
        });
        handler.openIframe();
    });
});
</script>
@endpush
