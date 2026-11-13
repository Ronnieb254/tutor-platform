@extends('layouts.admin')
@section('title', 'Topic Suggestions')
@section('content')

<div style="max-width:1400px;margin:0 auto;padding:24px;">
    <!-- Header -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;gap:16px;">
        <div>
            <h1 style="font-family:'Fraunces',serif;font-size:28px;font-weight:700;color:var(--ink);margin-bottom:6px;">Topic Suggestions</h1>
            <p style="color:var(--muted);font-size:14px;">Monitor and analyze student topic suggestions for group sessions</p>
        </div>
        <a href="{{ route('admin.group-session-suggestions.export', ['timeframe' => $timeframe]) }}" class="btn btn-v" style="height:44px;white-space:nowrap;">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Export CSV
        </a>
    </div>

    <!-- Stats Cards -->
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px;">
        <!-- Total Suggestions -->
        <div style="background:#fff;border-radius:14px;border:1px solid var(--hairline);padding:20px;">
            <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:16px;">
                <div>
                    <div style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">Total Suggestions</div>
                    <div style="font-family:'Fraunces',serif;font-size:32px;font-weight:700;color:var(--ink);">{{ $totalSuggestions }}</div>
                </div>
                <div style="width:44px;height:44px;background:rgba(10,100,188,.1);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#0A64BC" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                </div>
            </div>
            <p style="font-size:12px;color:var(--muted);margin:0;">Last {{ $timeframe }} days</p>
        </div>

        <!-- Unique Topics -->
        <div style="background:#fff;border-radius:14px;border:1px solid var(--hairline);padding:20px;">
            <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:16px;">
                <div>
                    <div style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">Unique Topics</div>
                    <div style="font-family:'Fraunces',serif;font-size:32px;font-weight:700;color:var(--ink);">{{ $uniqueTopics }}</div>
                </div>
                <div style="width:44px;height:44px;background:rgba(16,185,129,.1);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#10B981" stroke-width="2"><path d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                </div>
            </div>
            <p style="font-size:12px;color:var(--muted);margin:0;">Distinct topics</p>
        </div>

        <!-- With Description -->
        <div style="background:#fff;border-radius:14px;border:1px solid var(--hairline);padding:20px;">
            <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:16px;">
                <div>
                    <div style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">With Description</div>
                    <div style="font-family:'Fraunces',serif;font-size:32px;font-weight:700;color:var(--ink);">{{ $suggestionsWithDescription }}</div>
                </div>
                <div style="width:44px;height:44px;background:rgba(232,82,42,.1);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#E8522A" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </div>
            </div>
            <p style="font-size:12px;color:var(--muted);margin:0;">With details</p>
        </div>

        <!-- Unique Users -->
        <div style="background:#fff;border-radius:14px;border:1px solid var(--hairline);padding:20px;">
            <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:16px;">
                <div>
                    <div style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">Unique Users</div>
                    <div style="font-family:'Fraunces',serif;font-size:32px;font-weight:700;color:var(--ink);">{{ $totalUsers }}</div>
                </div>
                <div style="width:44px;height:44px;background:rgba(212,160,23,.1);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#EEBE15" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
            </div>
            <p style="font-size:12px;color:var(--muted);margin:0;">Contributors</p>
        </div>
    </div>

    <!-- Top Topics & Recent Activity -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;">
        <!-- Top 10 Topics -->
        <div class="card">
            <div style="padding:16px 20px;border-bottom:1px solid var(--hairline);display:flex;align-items:center;justify-content:space-between;">
                <h3 style="font-size:16px;font-weight:700;color:var(--ink);margin:0;">Top 10 Topics</h3>
                <span style="font-size:12px;color:var(--muted);">{{ $topTopics->count() }} topics</span>
            </div>
            <div style="overflow-y:auto;max-height:400px;">
                @if($topTopics->isNotEmpty())
                    @foreach($topTopics as $index => $topic)
                        <div style="padding:14px 20px;border-bottom:1px solid rgba(21,40,31,.05);display:flex;align-items:center;justify-content:space-between;">
                            <div style="display:flex;align-items:center;gap:12px;flex:1;">
                                <div style="width:32px;height:32px;background:rgba(10,100,188,.1);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:14px;font-weight:600;color:#0A64BC;">
                                    {{ $index + 1 }}
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <p style="font-size:14px;font-weight:600;color:var(--ink);margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $topic->topic }}</p>
                                </div>
                            </div>
                            <span style="background:rgba(10,100,188,.1);color:#0A64BC;font-size:12px;font-weight:700;padding:4px 10px;border-radius:6px;flex-shrink:0;white-space:nowrap;">{{ $topic->count }}</span>
                        </div>
                    @endforeach
                @else
                    <div style="padding:40px 20px;text-align:center;color:var(--muted);">
                        <div style="font-size:24px;margin-bottom:8px;">📊</div>
                        <p style="margin:0;font-size:14px;">No suggestions yet</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card">
            <div style="padding:16px 20px;border-bottom:1px solid var(--hairline);display:flex;align-items:center;justify-content:space-between;">
                <h3 style="font-size:16px;font-weight:700;color:var(--ink);margin:0;">Recent Activity</h3>
                <span style="font-size:12px;color:var(--muted);">Last 5</span>
            </div>
            <div style="overflow-y:auto;max-height:400px;">
                @if($recentActivity->isNotEmpty())
                    @foreach($recentActivity as $activity)
                        <div style="padding:14px 20px;border-bottom:1px solid rgba(21,40,31,.05);">
                            <div style="display:flex;align-items:start;gap:10px;margin-bottom:8px;">
                                <div style="width:32px;height:32px;background:rgba(232,82,42,.1);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:14px;">
                                    💬
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <p style="font-size:13px;font-weight:600;color:var(--ink);margin:0 0 2px 0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $activity->topic }}</p>
                                    <small style="color:var(--muted);font-size:12px;">
                                        @if($activity->user)
                                            {{ $activity->user->name }}
                                        @else
                                            Guest
                                        @endif
                                    </small>
                                    <br>
                                    <small style="color:var(--muted);font-size:11px;">{{ $activity->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div style="padding:40px 20px;text-align:center;color:var(--muted);">
                        <div style="font-size:24px;margin-bottom:8px;">📭</div>
                        <p style="margin:0;font-size:14px;">No activity yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="card" style="margin-bottom:20px;">
        <div style="padding:20px;display:grid;grid-template-columns:1fr 200px 200px auto;gap:12px;align-items:end;">
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:var(--muted);margin-bottom:6px;text-transform:uppercase;letter-spacing:.05em;">Search</label>
                <input type="text" placeholder="Search topic or description..." value="{{ $search }}" 
                    style="width:100%;height:42px;padding:0 14px;border:1.5px solid var(--hairline);border-radius:10px;font-size:14px;outline:none;"
                    onfocus="this.style.borderColor='#0A64BC'" onblur="this.style.borderColor='var(--hairline)'" id="search-input">
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:var(--muted);margin-bottom:6px;text-transform:uppercase;letter-spacing:.05em;">Timeframe</label>
                <select style="width:100%;height:42px;padding:0 12px;border:1.5px solid var(--hairline);border-radius:10px;font-size:14px;outline:none;background:#fff;" id="timeframe-select">
                    <option value="7" {{ $timeframe == 7 ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="30" {{ $timeframe == 30 ? 'selected' : '' }}>Last 30 Days</option>
                    <option value="90" {{ $timeframe == 90 ? 'selected' : '' }}>Last 90 Days</option>
                    <option value="365" {{ $timeframe == 365 ? 'selected' : '' }}>Last Year</option>
                    <option value="0" {{ $timeframe == 0 ? 'selected' : '' }}>All Time</option>
                </select>
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:var(--muted);margin-bottom:6px;text-transform:uppercase;letter-spacing:.05em;">Sort By</label>
                <select style="width:100%;height:42px;padding:0 12px;border:1.5px solid var(--hairline);border-radius:10px;font-size:14px;outline:none;background:#fff;" id="sort-select">
                    <option value="recent" {{ $sortBy == 'recent' ? 'selected' : '' }}>Most Recent</option>
                    <option value="popular" {{ $sortBy == 'popular' ? 'selected' : '' }}>Most Popular</option>
                </select>
            </div>
            <button onclick="applyFilters()" class="btn btn-v" style="height:42px;">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                Apply
            </button>
        </div>
    </div>

    <!-- Results Summary -->
    <div style="margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;font-size:13px;color:var(--muted);">
        <p>Showing <strong>{{ $suggestions->firstItem() ?? 0 }}</strong> to <strong>{{ $suggestions->lastItem() ?? 0 }}</strong> of <strong>{{ $suggestions->total() }}</strong> suggestions</p>
        <p>Page {{ $suggestions->currentPage() }} of {{ $suggestions->lastPage() }}</p>
    </div>

    <!-- Suggestions Table -->
    <div class="card">
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid var(--hairline);background:#fafcfa;">
                        <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;">Topic</th>
                        <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;">Description</th>
                        <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;">User</th>
                        <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;">Email</th>
                        <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;">Date</th>
                        <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suggestions as $suggestion)
                        <tr style="border-bottom:1px solid rgba(21,40,31,.05);transition:background .1s;">
                            <td style="padding:13px 16px;font-size:13.5px;color:var(--ink);font-weight:600;">{{ $suggestion->topic }}</td>
                            <td style="padding:13px 16px;font-size:13px;color:var(--muted);">
                                @if($suggestion->description)
                                    {{ Str::limit($suggestion->description, 50) }}
                                @else
                                    <em>No description</em>
                                @endif
                            </td>
                            <td style="padding:13px 16px;font-size:13px;">
                                @if($suggestion->user)
                                    <span style="background:rgba(10,100,188,.1);color:#0A64BC;font-size:11px;font-weight:700;padding:3px 10px;border-radius:6px;">{{ $suggestion->user->name }}</span>
                                @else
                                    <span style="background:rgba(107,114,128,.1);color:#6B7280;font-size:11px;font-weight:700;padding:3px 10px;border-radius:6px;">Guest</span>
                                @endif
                            </td>
                            <td style="padding:13px 16px;font-size:13px;color:var(--muted);">{{ $suggestion->email ?? 'N/A' }}</td>
                            <td style="padding:13px 16px;font-size:13px;color:var(--muted);">{{ $suggestion->created_at->format('M d, Y') }}</td>
                            <td style="padding:13px 16px;">
                                <a href="{{ route('admin.group-session-suggestions.show', $suggestion) }}" class="btn btn-o btn-sm" style="padding:6px 10px;">
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding:40px 16px;text-align:center;color:var(--muted);font-size:14px;">
                                📭 No suggestions found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($suggestions->hasPages())
            <div style="padding:20px;border-top:1px solid var(--hairline);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
                <div style="font-size:13px;color:var(--muted);">
                    Showing {{ $suggestions->firstItem() }} to {{ $suggestions->lastItem() }} of {{ $suggestions->total() }} results
                </div>
                <div style="display:flex;align-items:center;gap:4px;">
                    {{-- Previous --}}
                    @if($suggestions->onFirstPage())
                        <span style="padding:6px 12px;border-radius:8px;border:1px solid var(--hairline);font-size:13px;color:var(--muted);cursor:not-allowed;background:#fafafa;">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        </span>
                    @else
                        <a href="{{ $suggestions->previousPageUrl() }}" style="padding:6px 12px;border-radius:8px;border:1px solid var(--hairline);font-size:13px;color:var(--ink);text-decoration:none;background:#fff;display:flex;align-items:center;justify-content:center;" onmouseover="this.style.borderColor='#0A64BC'" onmouseout="this.style.borderColor='var(--hairline)'">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        </a>
                    @endif

                    {{-- Page window --}}
                    @php
                        $current = $suggestions->currentPage();
                        $last = $suggestions->lastPage();
                        $window = collect(range(1, $last))->filter(fn($p) => $p === 1 || $p === $last || abs($p - $current) <= 2);
                    @endphp

                    @php $prev = null; @endphp
                    @foreach($window as $page)
                        @if($prev !== null && $page - $prev > 1)
                            <span style="padding:6px 8px;font-size:13px;color:var(--muted);">…</span>
                        @endif
                        @if($page == $current)
                            <span style="padding:6px 12px;border-radius:8px;border:1px solid #0A64BC;font-size:13px;font-weight:700;color:#fff;background:#0A64BC;">{{ $page }}</span>
                        @else
                            <a href="{{ $suggestions->url($page) }}" style="padding:6px 12px;border-radius:8px;border:1px solid var(--hairline);font-size:13px;color:var(--ink);text-decoration:none;background:#fff;" onmouseover="this.style.borderColor='#0A64BC'" onmouseout="this.style.borderColor='var(--hairline)'">{{ $page }}</a>
                        @endif
                        @php $prev = $page; @endphp
                    @endforeach

                    {{-- Next --}}
                    @if($suggestions->hasMorePages())
                        <a href="{{ $suggestions->nextPageUrl() }}" style="padding:6px 12px;border-radius:8px;border:1px solid var(--hairline);font-size:13px;color:var(--ink);text-decoration:none;background:#fff;display:flex;align-items:center;justify-content:center;" onmouseover="this.style.borderColor='#0A64BC'" onmouseout="this.style.borderColor='var(--hairline)'">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </a>
                    @else
                        <span style="padding:6px 12px;border-radius:8px;border:1px solid var(--hairline);font-size:13px;color:var(--muted);cursor:not-allowed;background:#fafafa;display:flex;align-items:center;justify-content:center;">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function applyFilters() {
    const search = document.getElementById('search-input').value;
    const timeframe = document.getElementById('timeframe-select').value;
    const sort = document.getElementById('sort-select').value;
    const url = new URL(window.location);
    url.searchParams.set('search', search);
    url.searchParams.set('timeframe', timeframe);
    url.searchParams.set('sort', sort);
    window.location = url.toString();
}
</script>

@endsection
