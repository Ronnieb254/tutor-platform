{{-- Poll widget for group sessions --}}
<div x-data="pollWidget({{ $groupSession->id }})" @init="init()" x-cloak class="poll-widget" style="margin-bottom:24px;">
    {{-- Error toast --}}
    <template x-if="error">
        <div style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);border-radius:12px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:12px;color:#dc2626;font-weight:600;">
            <span style="font-size:20px;">⚠️</span>
            <span x-text="error"></span>
        </div>
    </template>

    {{-- Heading --}}
    <div style="margin-bottom:20px;">
        <h2 style="font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:var(--ink);margin:0 0 8px;">🔝 Top suggestions for this session</h2>
        <p style="font-size:14px;color:rgba(21,40,31,.5);margin:0;">What topics would you like us to cover?</p>
    </div>

    {{-- Suggestions list --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px;margin-bottom:24px;">
        <template x-for="suggestion in suggestions" :key="suggestion.id">
            <div 
                x-show="!loading"
                x-transition
                style="border:1px solid rgba(21,40,31,.1);border-radius:14px;padding:16px;background:#fff;box-shadow:0 2px 8px rgba(21,40,31,.05);transition:all .2s;"
                @mouseenter="this.style.boxShadow='0 4px 16px rgba(21,40,31,.1)'"
                @mouseleave="this.style.boxShadow='0 2px 8px rgba(21,40,31,.05)'"
            >
                {{-- Subject/area heading --}}
                <div style="margin-bottom:12px;">
                    <p style="font-size:12px;color:var(--trust-blue);font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin:0 0 4px;">
                        <template x-if="suggestion.subject">
                            <span x-text="suggestion.subject.name"></span>
                        </template>
                        <template x-if="suggestion.custom_subject && !suggestion.subject">
                            <span x-text="suggestion.custom_subject"></span>
                        </template>
                    </p>
                    <p style="font-size:15px;font-weight:600;color:var(--ink);margin:0;word-break:break-word;" x-text="suggestion.area"></p>
                </div>

                {{-- Vote count and created by --}}
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;padding-bottom:12px;border-bottom:1px solid rgba(21,40,31,.06);">
                    <div style="display:flex;align-items:center;gap:6px;">
                        <span style="font-size:18px;">👍</span>
                        <span style="font-weight:700;color:var(--ink);" x-text="suggestion.votes_count + ' vote' + (suggestion.votes_count !== 1 ? 's' : '')"></span>
                    </div>
                    <span style="font-size:12px;color:rgba(21,40,31,.45);">
                        <template x-if="suggestion.created_by">
                            <span>by <strong x-text="suggestion.created_by.name"></strong></span>
                        </template>
                        <template x-if="!suggestion.created_by">
                            <span>Guest</span>
                        </template>
                    </span>
                </div>

                {{-- Vote button --}}
                <button
                    @click="hasUserVoted(suggestion) ? unvote(suggestion) : vote(suggestion)"
                    :disabled="loading"
                    :style="{
                        'background-color': hasUserVoted(suggestion) ? 'var(--trust-blue)' : 'rgba(21,40,31,.06)',
                        'color': hasUserVoted(suggestion) ? '#fff' : 'var(--ink)',
                    }"
                    style="width:100%;padding:10px;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;transition:all .15s;opacity:1;"
                    @mouseenter="this.style.opacity='0.9'"
                    @mouseleave="this.style.opacity='1'"
                >
                    <template x-if="hasUserVoted(suggestion)">
                        <span>✓ Already voted</span>
                    </template>
                    <template x-if="!hasUserVoted(suggestion)">
                        <span>Vote (👍)</span>
                    </template>
                </button>
            </div>
        </template>

        {{-- Empty state --}}
        <template x-if="!loading && suggestions.length === 0">
            <div style="grid-column:1/-1;text-align:center;padding:40px 20px;background:rgba(21,40,31,.03);border-radius:14px;border:1px dashed rgba(21,40,31,.1);">
                <p style="font-size:48px;margin:0 0 8px;">📭</p>
                <p style="font-size:16px;font-weight:600;color:var(--ink);margin:0 0 6px;">No suggestions yet</p>
                <p style="font-size:14px;color:rgba(21,40,31,.5);margin:0;">Be the first to suggest a topic!</p>
            </div>
        </template>
    </div>

    {{-- Loading spinner --}}
    <template x-if="loading">
        <div style="display:flex;justify-content:center;padding:40px 20px;">
            <div style="width:40px;height:40px;border:3px solid rgba(21,40,31,.1);border-top-color:var(--trust-blue);border-radius:50%;animation:spin .6s linear infinite;"></div>
        </div>
    </template>

    {{-- Submit form --}}
    <div style="background:#F9FAF9;border:1px solid rgba(21,40,31,.07);border-radius:14px;padding:20px;">
        <h3 style="font-size:14px;font-weight:700;color:var(--ink);margin:0 0 16px;"><span style="font-size:16px;">✨</span> Suggest a topic</h3>

        <form @submit.prevent="submitForm" style="display:flex;flex-direction:column;gap:12px;">
            {{-- Subject dropdown --}}
            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:rgba(21,40,31,.6);text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">Subject (optional)</label>
                <select 
                    x-model="formData.subject_id"
                    style="width:100%;padding:10px 12px;border:1px solid rgba(21,40,31,.1);border-radius:8px;font-size:14px;font-family:inherit;color:var(--ink);background:#fff;cursor:pointer;"
                >
                    <option value="">-- Choose a subject --</option>
                    <template x-for="subject in subjects" :key="subject.id">
                        <option :value="subject.id" x-text="subject.name"></option>
                    </template>
                </select>
            </div>

            {{-- Custom subject --}}
            <div x-show="!formData.subject_id">
                <label style="display:block;font-size:12px;font-weight:700;color:rgba(21,40,31,.6);text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">Custom Subject (required if no subject selected)</label>
                <input 
                    type="text"
                    x-model="formData.custom_subject"
                    placeholder="e.g., Python Programming"
                    style="width:100%;padding:10px 12px;border:1px solid rgba(21,40,31,.1);border-radius:8px;font-size:14px;font-family:inherit;color:var(--ink);background:#fff;box-sizing:border-box;"
                />
            </div>

            {{-- Area textarea --}}
            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:rgba(21,40,31,.6);text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">Area or specific topic (3-500 characters)</label>
                <textarea 
                    x-model="formData.area"
                    placeholder="e.g., Advanced functions and closures"
                    style="width:100%;padding:10px 12px;border:1px solid rgba(21,40,31,.1);border-radius:8px;font-size:14px;font-family:inherit;color:var(--ink);background:#fff;min-height:80px;resize:vertical;box-sizing:border-box;"
                ></textarea>
                <p style="font-size:12px;color:rgba(21,40,31,.45);margin:6px 0 0;">
                    <span x-text="formData.area.length"></span> / 500 characters
                </p>
            </div>

            {{-- Submit button --}}
            <button 
                type="submit"
                :disabled="loading || !formData.area.trim()"
                style="padding:12px;background:var(--coral);color:#fff;font-size:14px;font-weight:700;border:none;border-radius:8px;cursor:pointer;transition:all .15s;opacity:1;"
                @mouseenter="this.style.opacity='0.9'"
                @mouseleave="this.style.opacity='1'"
            >
                <template x-if="loading">
                    <span>Submitting...</span>
                </template>
                <template x-if="!loading">
                    <span>Add Suggestion</span>
                </template>
            </button>
        </form>
    </div>
</div>

<style>
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    [x-cloak] {
        display: none !important;
    }

    .poll-widget {
        --ink: #15281F;
        --trust-blue: #0A64BC;
        --coral: #E8522A;
        --gold: #EEBE15;
        --mint: #10B981;
    }

    @media (max-width: 768px) {
        .poll-widget > div:first-of-type {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<script>
    function pollWidget(groupSessionId) {
        return {
            suggestions: [],
            subjects: [],
            loading: false,
            error: null,
            browserId: null,
            formData: {
                subject_id: '',
                custom_subject: '',
                area: '',
            },
            userVotes: new Set(),

            async init() {
                // Initialize browser ID from localStorage or generate new one
                if (!localStorage.getItem('poll_browser_id')) {
                    localStorage.setItem('poll_browser_id', this.generateUUID());
                }
                this.browserId = localStorage.getItem('poll_browser_id');

                // Load subjects
                await this.loadSubjects();

                // Load suggestions
                await this.fetchSuggestions();
            },

            async loadSubjects() {
                try {
                    const response = await fetch(`/api/subjects?active=1`);
                    if (response.ok) {
                        this.subjects = await response.json();
                    }
                } catch (e) {
                    console.error('Failed to load subjects:', e);
                    this.subjects = [];
                }
            },

            async fetchSuggestions() {
                this.loading = true;
                this.error = null;
                try {
                    const response = await fetch(`/group-sessions/${groupSessionId}/suggestions`);
                    if (response.ok) {
                        const data = await response.json();
                        this.suggestions = Array.isArray(data) ? data : (data.data || []);
                        this.userVotes.clear();
                    } else {
                        this.error = 'Failed to load suggestions';
                    }
                } catch (e) {
                    console.error('Fetch error:', e);
                    this.error = 'Could not load suggestions';
                } finally {
                    this.loading = false;
                }
            },

            async submitForm() {
                if (!this.formData.area.trim() || this.formData.area.length < 3 || this.formData.area.length > 500) {
                    this.error = 'Area must be between 3 and 500 characters';
                    return;
                }

                if (!this.formData.subject_id && !this.formData.custom_subject.trim()) {
                    this.error = 'Please select a subject or enter a custom subject';
                    return;
                }

                this.loading = true;
                this.error = null;

                try {
                    const response = await fetch(`/group-sessions/${groupSessionId}/suggestions`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            subject_id: this.formData.subject_id || null,
                            custom_subject: this.formData.custom_subject || null,
                            area: this.formData.area,
                            browser_id: this.browserId,
                        }),
                    });

                    if (response.ok) {
                        const newSuggestion = await response.json();
                        this.suggestions.unshift(newSuggestion);
                        this.formData = {
                            subject_id: '',
                            custom_subject: '',
                            area: '',
                        };
                    } else {
                        const error = await response.json();
                        this.error = error.error || 'Failed to submit suggestion';
                    }
                } catch (e) {
                    console.error('Submit error:', e);
                    this.error = 'Could not submit suggestion';
                } finally {
                    this.loading = false;
                }
            },

            async vote(suggestion) {
                this.loading = true;
                try {
                    const response = await fetch(`/group-sessions/${groupSessionId}/suggestions/${suggestion.id}/vote`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            browser_id: this.browserId,
                        }),
                    });

                    if (response.ok) {
                        const data = await response.json();
                        suggestion.votes_count = data.votes_count;
                        this.userVotes.add(suggestion.id);
                    } else if (response.status === 409) {
                        this.error = 'You have already voted on this suggestion';
                        this.userVotes.add(suggestion.id);
                    } else {
                        this.error = 'Failed to vote';
                    }
                } catch (e) {
                    console.error('Vote error:', e);
                    this.error = 'Could not submit your vote';
                } finally {
                    this.loading = false;
                }
            },

            async unvote(suggestion) {
                this.loading = true;
                try {
                    const response = await fetch(`/group-sessions/${groupSessionId}/suggestions/${suggestion.id}/vote`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            browser_id: this.browserId,
                        }),
                    });

                    if (response.ok) {
                        const data = await response.json();
                        suggestion.votes_count = data.votes_count;
                        this.userVotes.delete(suggestion.id);
                    } else {
                        this.error = 'Failed to remove your vote';
                    }
                } catch (e) {
                    console.error('Unvote error:', e);
                    this.error = 'Could not remove your vote';
                } finally {
                    this.loading = false;
                }
            },

            hasUserVoted(suggestion) {
                return this.userVotes.has(suggestion.id);
            },

            generateUUID() {
                return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                    const r = Math.random() * 16 | 0,
                        v = c === 'x' ? r : (r & 0x3 | 0x8);
                    return v.toString(16);
                });
            },
        }
    }
</script>
