<div class="w-full max-w-2xl mx-auto">
    {{-- Progress --}}
    <div class="flex items-center justify-center gap-2 mb-8">
        @foreach(['Your Details', 'Subjects', 'Availability', 'Documents'] as $i => $label)
        <div class="flex items-center {{ !$loop->first ? 'gap-2' : '' }}">
            @if(!$loop->first)<div class="w-8 h-px bg-hairline"></div>@endif
            <div class="flex flex-col items-center gap-1">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition
                    {{ $step > $i+1 ? 'bg-mint text-white' : ($step === $i+1 ? 'bg-violet text-white' : 'bg-hairline text-muted') }}">
                    {{ $step > $i+1 ? '✓' : $i+1 }}
                </div>
                <span class="text-[10px] font-medium {{ $step === $i+1 ? 'text-ink' : 'text-muted' }} hidden sm:block">{{ $label }}</span>
            </div>
        </div>
        @endforeach
    </div>

    <div class="card p-8">
        {{-- STEP 1 --}}
        @if($step === 1)
        <h2 class="font-serif text-2xl font-bold text-ink mb-6">Tell us about yourself</h2>
        <div class="space-y-4">
            <div>
                <label class="form-label">Your bio <span class="text-muted font-normal">(min 50 characters)</span></label>
                <textarea wire:model="bio" rows="4" class="form-input resize-none"
                          placeholder="Describe your teaching experience, qualifications, and approach…"></textarea>
                @error('bio')<p class="form-error">{{ $message }}</p>@enderror
                <p class="text-xs text-muted mt-1">{{ strlen($bio) }}/50 minimum characters</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Hourly rate (KSh)</label>
                    <input type="number" wire:model="hourlyRate" class="form-input" placeholder="e.g. 2000" min="500">
                    @error('hourlyRate')<p class="form-error">{{ $message }}</p>@enderror
                    <p class="text-xs text-muted mt-1">Minimum KSh 500/hr</p>
                </div>
                <div>
                    <label class="form-label">Years of experience</label>
                    <input type="number" wire:model="experienceYears" class="form-input" placeholder="0" min="0">
                </div>
            </div>
            <div>
                <label class="form-label">City</label>
                <input type="text" wire:model="city" class="form-input" placeholder="e.g. Nairobi">
                @error('city')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            {{-- Session pricing preview --}}
            @if($hourlyRate >= 500)
            <div class="rounded-2xl bg-violet/5 border border-violet/15 p-4">
                <p class="text-sm font-semibold text-violet mb-3">Your session prices</p>
                <div class="grid grid-cols-4 gap-2">
                    @foreach([15 => 0.25, 30 => 0.50, 45 => 0.75, 60 => 1.00] as $mins => $mult)
                    <div class="text-center rounded-xl bg-white border border-hairline p-2">
                        <p class="text-xs font-bold text-ink">{{ $mins }}min</p>
                        <p class="text-xs text-violet font-semibold mt-0.5">KSh {{ number_format($hourlyRate * $mult, 0) }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        <div class="flex justify-end mt-6">
            <button wire:click="nextStep" class="pill-btn-primary px-8">Continue →</button>
        </div>
        @endif

        {{-- STEP 2: Subjects --}}
        @if($step === 2)
        <h2 class="font-serif text-2xl font-bold text-ink mb-2">Which subjects do you teach?</h2>
        <p class="text-muted text-sm mb-6">Select all that apply. You can update this later.</p>
        @error('selectedSubjects')<p class="form-error mb-3">{{ $message }}</p>@enderror
        <div class="space-y-4 max-h-96 overflow-y-auto pr-1">
            @foreach($subjects as $categoryName => $categorySubjects)
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-muted mb-2">{{ $categoryName }}</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($categorySubjects as $subject)
                    <label class="cursor-pointer">
                        <input type="checkbox" wire:model="selectedSubjects" value="{{ $subject->id }}" class="sr-only">
                        <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-sm font-medium border-2 transition
                            {{ in_array($subject->id, $selectedSubjects) ? 'border-violet bg-violet text-white' : 'border-hairline text-ink hover:border-violet/40' }}">
                            {{ $subject->name }}
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        <p class="text-xs text-muted mt-3">{{ count($selectedSubjects) }} subject(s) selected</p>
        <div class="flex justify-between mt-6">
            <button wire:click="prevStep" class="pill-btn-outline px-6">← Back</button>
            <button wire:click="nextStep" class="pill-btn-primary px-8">Continue →</button>
        </div>
        @endif

        {{-- STEP 3: Availability --}}
        @if($step === 3)
        <h2 class="font-serif text-2xl font-bold text-ink mb-2">Set your weekly availability</h2>
        <p class="text-muted text-sm mb-6">Students will book sessions within these hours.</p>
        <div class="space-y-2">
            @php $days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat']; @endphp
            @foreach($availability as $day => $slot)
            <div class="flex items-center gap-4 p-3 rounded-2xl {{ $slot['active'] ? 'bg-violet/5 border border-violet/15' : 'bg-cream/50' }}">
                <label class="flex items-center gap-2 w-16 flex-shrink-0 cursor-pointer">
                    <input type="checkbox" wire:model="availability.{{ $day }}.active" class="w-4 h-4 accent-violet">
                    <span class="text-sm font-semibold text-ink">{{ $days[$day] }}</span>
                </label>
                @if($slot['active'])
                <div class="flex items-center gap-2 flex-1">
                    <input type="time" wire:model="availability.{{ $day }}.start" class="form-input py-1.5 text-sm">
                    <span class="text-muted text-sm">to</span>
                    <input type="time" wire:model="availability.{{ $day }}.end" class="form-input py-1.5 text-sm">
                </div>
                @else
                <span class="text-sm text-muted">Not available</span>
                @endif
            </div>
            @endforeach
        </div>
        <div class="flex justify-between mt-6">
            <button wire:click="prevStep" class="pill-btn-outline px-6">← Back</button>
            <button wire:click="nextStep" class="pill-btn-primary px-8">Continue →</button>
        </div>
        @endif

        {{-- STEP 4: Documents --}}
        @if($step === 4)
        <h2 class="font-serif text-2xl font-bold text-ink mb-2">Upload your documents</h2>
        <p class="text-muted text-sm mb-6">Our team will review your documents within 24–48 hours. JPG, PNG or PDF, max 5MB each.</p>
        <div class="space-y-4">
            <div class="p-4 rounded-2xl border-2 border-dashed border-hairline hover:border-violet/40 transition">
                <label class="form-label">National ID / Passport <span class="text-coral">*</span></label>
                <input type="file" wire:model="nationalId" accept=".jpg,.jpeg,.png,.pdf" class="text-sm text-muted">
                @error('nationalId')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="p-4 rounded-2xl border-2 border-dashed border-hairline hover:border-violet/40 transition">
                <label class="form-label">Teaching Certificate <span class="text-muted font-normal">(optional)</span></label>
                <input type="file" wire:model="certificate" accept=".jpg,.jpeg,.png,.pdf" class="text-sm text-muted">
            </div>
            <div class="p-4 rounded-2xl border-2 border-dashed border-hairline hover:border-violet/40 transition">
                <label class="form-label">Academic Qualification <span class="text-muted font-normal">(optional)</span></label>
                <input type="file" wire:model="qualification" accept=".jpg,.jpeg,.png,.pdf" class="text-sm text-muted">
            </div>
        </div>
        <div class="flex justify-between mt-6">
            <button wire:click="prevStep" class="pill-btn-outline px-6">← Back</button>
            <button wire:click="submit"
                    wire:loading.attr="disabled"
                    class="pill-btn-primary px-8">
                <span wire:loading.remove wire:target="submit">Submit for Review</span>
                <span wire:loading wire:target="submit">Uploading…</span>
            </button>
        </div>
        @endif

        {{-- DONE --}}
        @if($step === 5)
        <div class="text-center py-4">
            <div class="w-16 h-16 rounded-full bg-mint/20 flex items-center justify-center mx-auto mb-4">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <h2 class="font-serif text-2xl font-bold text-ink mb-2">Application submitted!</h2>
            <p class="text-muted mb-6">Our team will review your profile and documents within 24–48 hours. You'll receive an email once approved.</p>
            <div class="rounded-2xl bg-amber-50 border border-amber-200 p-4 text-sm text-amber-800 text-left mb-6">
                <p class="font-semibold">⏳ What happens next?</p>
                <ol class="mt-2 space-y-1 list-decimal list-inside text-amber-700">
                    <li>Our team reviews your documents</li>
                    <li>You receive an approval email</li>
                    <li>Your profile goes live in search results</li>
                    <li>Students start booking sessions with you!</li>
                </ol>
            </div>
            <a href="{{ route('tutor.dashboard') }}" class="pill-btn-primary">Go to Dashboard</a>
        </div>
        @endif
    </div>
</div>
