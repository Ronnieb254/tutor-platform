<div class="flex flex-col lg:flex-row gap-8">
    {{-- Filters Sidebar --}}
    <aside class="w-full lg:w-64 flex-shrink-0">
        <div class="card p-5 space-y-5 sticky top-24">
            <h2 class="font-serif text-lg font-semibold text-ink">Filter Tutors</h2>

            <div>
                <label class="form-label">Search</label>
                <input type="text" wire:model.live.debounce.400ms="search"
                       class="form-input" placeholder="Name or subject…">
            </div>

            <div>
                <label class="form-label">Category</label>
                <select wire:model.live="categoryId" class="form-input">
                    <option value="">All categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            @if($subjects->isNotEmpty())
            <div>
                <label class="form-label">Subject</label>
                <select wire:model.live="subjectId" class="form-input">
                    <option value="">All subjects</option>
                    @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label class="form-label">Price range (KSh/hr)</label>
                <div class="flex items-center gap-2">
                    <input type="number" wire:model.live.debounce.500ms="minPrice"
                           class="form-input" placeholder="Min" min="0">
                    <span class="text-muted text-sm">–</span>
                    <input type="number" wire:model.live.debounce.500ms="maxPrice"
                           class="form-input" placeholder="Max" min="0">
                </div>
            </div>

            <div>
                <label class="form-label">Minimum rating</label>
                <select wire:model.live="minRating" class="form-input">
                    <option value="">Any rating</option>
                    <option value="4.5">4.5+ ★</option>
                    <option value="4.0">4.0+ ★</option>
                    <option value="3.5">3.5+ ★</option>
                </select>
            </div>

            <div>
                <label class="form-label">Sort by</label>
                <select wire:model.live="sortBy" class="form-input">
                    <option value="rating_avg">Highest rated</option>
                    <option value="hourly_rate">Lowest price</option>
                    <option value="total_sessions">Most sessions</option>
                </select>
            </div>
        </div>
    </aside>

    {{-- Results --}}
    <div class="flex-1">
        <div class="flex items-center justify-between mb-5">
            <p class="text-sm text-muted">{{ $tutors->total() }} tutor{{ $tutors->total() !== 1 ? 's' : '' }} found</p>
            <div wire:loading class="text-xs text-violet flex items-center gap-1.5">
                <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="3" stroke-linecap="round" class="opacity-75"/></svg>
                Searching…
            </div>
        </div>

        @if($tutors->isEmpty())
        <div class="card p-10 text-center">
            <div class="text-4xl mb-3">🔍</div>
            <p class="font-serif text-xl font-semibold text-ink mb-2">No tutors found</p>
            <p class="text-muted text-sm">Try adjusting your filters or search term.</p>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            @foreach($tutors as $tutor)
            <div class="card-hover p-5">
                <div class="flex items-start gap-4">
                    <img src="{{ $tutor->user->avatar_url }}" alt="{{ $tutor->user->name }}"
                         class="w-16 h-16 rounded-2xl object-cover flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="font-serif font-semibold text-ink">{{ $tutor->user->name }}</p>
                                <div class="flex items-center gap-1 mt-0.5">
                                    <span class="text-mint text-xs font-semibold">✓ Verified</span>
                                    @if($tutor->experience_years > 0)
                                    <span class="text-muted text-xs">· {{ $tutor->experience_years }}y exp</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="font-bold text-ink text-sm">KSh {{ number_format($tutor->hourly_rate, 0) }}</p>
                                <p class="text-xs text-muted">per hour</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-1 mt-2">
                            @foreach($tutor->subjects->take(3) as $subject)
                            <span class="badge-violet text-[10px]">{{ $subject->name }}</span>
                            @endforeach
                            @if($tutor->subjects->count() > 3)
                            <span class="badge bg-ink/5 text-muted text-[10px]">+{{ $tutor->subjects->count() - 3 }}</span>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 mt-2 text-xs text-muted">
                            <span class="text-gold font-bold">★ {{ number_format($tutor->rating_avg, 1) }}</span>
                            <span>({{ $tutor->total_reviews }} reviews)</span>
                            <span>·</span>
                            <span>{{ $tutor->total_sessions }} sessions</span>
                        </div>
                    </div>
                </div>

                @if($tutor->bio)
                <p class="text-xs text-muted mt-3 line-clamp-2">{{ $tutor->bio }}</p>
                @endif

                {{-- Duration blocks --}}
                <div class="grid grid-cols-4 gap-1.5 mt-4">
                    @foreach([15, 30, 45, 60] as $mins)
                    <div class="text-center rounded-xl border border-hairline py-1.5 hover:border-violet hover:bg-violet/5 cursor-pointer transition">
                        <p class="text-[11px] font-bold text-ink">{{ $mins }}m</p>
                        <p class="text-[10px] text-muted">KSh {{ \App\Models\TutorProfile::sessionPrices()[$mins]['price'] ?? '' }}</p>
                    </div>
                    @endforeach
                </div>

                <a href="{{ route('tutors.show', $tutor->id) }}"
                   class="mt-3 w-full pill-btn-primary text-sm py-2.5 block text-center">
                    View Profile & Book
                </a>
            </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $tutors->links() }}
        </div>
        @endif
    </div>
</div>
