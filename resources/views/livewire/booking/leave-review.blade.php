<div>
    @if($submitted)
    <div class="text-center py-4">
        <div class="text-4xl mb-2">⭐</div>
        <p class="font-semibold text-ink">Thank you for your review!</p>
        <p class="text-sm text-muted mt-1">Your feedback helps other students find great tutors.</p>
    </div>
    @else
    <form wire:submit="submit" class="space-y-4">
        <div>
            <label class="form-label">Rating</label>
            <div class="flex gap-2" x-data="{ hovered: 0 }">
                @for($i = 1; $i <= 5; $i++)
                <button type="button"
                        wire:click="$set('rating', {{ $i }})"
                        @mouseenter="hovered = {{ $i }}"
                        @mouseleave="hovered = 0"
                        class="text-3xl transition"
                        :class="(hovered >= {{ $i }} || (@js($rating) >= {{ $i }} && hovered === 0)) ? 'text-gold' : 'text-hairline'">
                    ★
                </button>
                @endfor
                <span class="text-sm text-muted self-center ml-2">{{ $rating }}/5</span>
            </div>
        </div>
        <div>
            <label class="form-label">Comment <span class="text-muted font-normal">(optional)</span></label>
            <textarea wire:model="comment" rows="3" class="form-input resize-none"
                      placeholder="How was your session? What did you learn?"></textarea>
        </div>
        <button type="submit" class="pill-btn-primary">Submit Review</button>
    </form>
    @endif
</div>
