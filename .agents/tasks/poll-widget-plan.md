# Poll Widget Feature Implementation Plan

## Overview
Add a poll-like widget to the group session detail page that allows parents, students, and guests to suggest subjects and areas they'd like tutored on, with voting to track community interest.

## Key Findings
- **Group Sessions Base**: Stored in `examiner_clinics` table with `is_group_session = true`
- **Subject Model**: Located at `app/Models/Subject.php` with `fillable: [category_id, curriculum_id, name, slug, icon, is_active]`
- **User Model**: Located at `app/Models/User.php`, supports `role` and guest access via session-based voting
- **Group Session View**: `resources/views/pages/group-sessions/show.blade.php` - right sidebar has sticky booking card
- **Routes**: Group session show route is `/group-sessions/{groupSession}` with `whereNumber('groupSession')`
- **Admin Panel**: Located at `resources/views/pages/admin/` - uses Blade layout `layouts.admin`
- **Design System**: Uses CSS variables `--ink`, `--cream`, `--trust-blue`, `--coral`, `--gold`, `--mint` plus Fraunces serif and DM Sans fonts
- **Frontend**: Alpine.js available (no Livewire for this feature)
- **Latest migration timestamp**: `2026_11_12` format

## Design Decisions
1. **Storage**: Create two new tables `group_session_suggestions` and `group_session_suggestion_votes` with indexes on `group_session_id` and `created_at` for efficient querying
2. **Duplicate Vote Prevention**: Use hybrid approach - track `voter_id` if logged in, `voter_ip` for semi-anonymous tracking, `browser_id` (UUID stored in localStorage) for guest dedup when IP is unavailable (rare)
3. **Widget Placement**: After "Topics covered" section in group session show view, before capacity bar
4. **Frontend Interactivity**: Alpine.js component for voting, submission, and real-time updates (no page reload required)
5. **Admin Management**: New admin page `resources/views/pages/admin/group-session-suggestions.blade.php` to view/delete suggestions per session
6. **API Routes**: RESTful routes for suggestion CRUD and voting operations

---

# Implementation Plan

- [ ] 1. Create database migrations for poll widget tables.
      Create `group_session_suggestions` table: id, group_session_id, subject_id (nullable), custom_subject (nullable), area (text), votes_count (int, default 0), created_by (nullable), created_at, updated_at. Create `group_session_suggestion_votes` table: id, suggestion_id, voter_id (nullable), voter_ip (nullable), browser_id (nullable), created_at. Add indexes on group_session_id and created_at in both tables.
      Files: `database/migrations/2026_11_12_create_group_session_suggestions_table.php`, `database/migrations/2026_11_12_create_group_session_suggestion_votes_table.php`
      Verify: Run `php artisan migrate` and confirm both tables exist: `php artisan tinker` then `DB::table('group_session_suggestions')->getColumns()`

- [ ] 2. Create GroupSessionSuggestion and GroupSessionSuggestionVote models with relationships.
      Create `GroupSessionSuggestion` model with fillable fields: group_session_id, subject_id, custom_subject, area, votes_count, created_by. Add relationships: belongsTo ExaminerClinic, belongsTo Subject (nullable), belongsTo User (as created_by, nullable), hasMany GroupSessionSuggestionVote. Create `GroupSessionSuggestionVote` model with fillable: suggestion_id, voter_id, voter_ip, browser_id. Add relationships: belongsTo GroupSessionSuggestion, belongsTo User (as voter, nullable).
      Files: `app/Models/GroupSessionSuggestion.php`, `app/Models/GroupSessionSuggestionVote.php`
      Verify: Run `php artisan tinker` and instantiate both models: `new \App\Models\GroupSessionSuggestion()` and `new \App\Models\GroupSessionSuggestionVote()`

- [ ] 3. Create GroupSessionSuggestionController with API methods.
      Create controller at `app/Http/Controllers/GroupSessionSuggestionController.php` with methods: (1) `index($groupSession)` - returns JSON with paginated suggestions sorted by votes_count DESC, (2) `store($groupSession)` - validates subject_id (or custom_subject) and area, creates suggestion with votes_count=0, returns JSON with new suggestion, (3) `vote($groupSession, $suggestion)` - checks for existing vote (voter_id if logged in, else voter_ip/browser_id), increments votes_count if new vote, creates vote record, (4) `unvote($groupSession, $suggestion)` - removes vote and decrements votes_count. All methods should handle guests gracefully (no auth required).
      Files: `app/Http/Controllers/GroupSessionSuggestionController.php`
      Verify: Run `php artisan route:list | grep suggestion` and confirm routes exist

- [ ] 4. Add API routes for poll widget operations.
      Add to `routes/web.php` in the public routes section (no auth required): `GET /group-sessions/{groupSession}/suggestions`, `POST /group-sessions/{groupSession}/suggestions`, `POST /group-sessions/{groupSession}/suggestions/{suggestion}/vote`, `DELETE /group-sessions/{groupSession}/suggestions/{suggestion}/vote`. Use `GroupSessionController` middleware pattern (defensive Schema::hasColumn checks).
      Files: `routes/web.php` (append routes in public group, before authenticated group)
      Verify: Run `php artisan route:list` and grep for `/suggestions` to confirm all 4 routes exist

- [ ] 5. Create Blade partial for poll widget display (`resources/views/partials/group-session-poll.blade.php`).
      Create Alpine.js component that: (1) displays "🔝 Top suggestions for this session" heading, (2) fetches suggestions on page load via GET /group-sessions/{groupSession}/suggestions, (3) renders cards with subject name, area, vote count (formatted as "👍 N parents & students interested"), vote/unvote button, (4) shows form to submit new suggestion with subject dropdown (pre-populated from API or inline data) and area text field, (5) includes loading states and error handling, (6) smooth transitions when voting. Store browser_id as UUID in localStorage on first page load if not logged in.
      Files: `resources/views/partials/group-session-poll.blade.php`
      Verify: Check file exists and contains Alpine x-data directive with valid JavaScript

- [ ] 6. Integrate poll widget into group session show view.
      Add `@include('partials.group-session-poll', ['groupSession' => $groupSession])` after the "Topics covered" section and before the "Capacity bar" in `resources/views/pages/group-sessions/show.blade.php`. Ensure poll widget renders only if ExaminerClinic has is_group_session = true.
      Files: `resources/views/pages/group-sessions/show.blade.php`
      Verify: Open the group session show page in browser at `/group-sessions/{id}` and visually confirm poll widget renders with subject dropdown and suggestion form

- [ ] 7. Add admin view for suggestion management (`resources/views/pages/admin/group-session-suggestions.blade.php`).
      Create admin view following existing `categories.blade.php` pattern: (1) list all suggestions across all group sessions in a DataTables table, (2) columns: Group Session ID, Subject, Area, Votes, Created By, Created At, Actions, (3) actions: delete suggestion (hard delete), (4) filter by group_session_id dropdown. Add admin route `GET /admin/group-sessions/suggestions` in routes/web.php.
      Files: `resources/views/pages/admin/group-session-suggestions.blade.php`, add route to `routes/web.php`
      Verify: Run `php artisan route:list | grep admin.*suggestion` and visit `/admin/group-sessions/suggestions` to confirm page loads and table renders

- [ ] 8. Add admin menu link and create admin controller method.
      Add controller method `GroupSessionSuggestionAdminController@index` to return suggestions with filters. Add menu link in admin sidebar (if a sidebar file exists, like `partials/admin-nav.blade.php`) pointing to `/admin/group-sessions/suggestions`. Ensure delete action is secured with admin-only authorization.
      Files: `app/Http/Controllers/Admin/GroupSessionSuggestionController.php`, update admin layout or nav partial
      Verify: Check admin menu loads without errors and link is clickable; confirm delete action returns 403 if not admin

- [ ] 9. Add validation rules and voter deduplication logic in controller.
      In `GroupSessionSuggestionController`, validate: (1) subject_id must exist in subjects table if provided, (2) area text must be 3-500 characters, (3) custom_subject optional but if both subject_id and custom_subject provided, use subject_id. For voter dedup: if auth()->check() use voter_id, else use voter_ip from request (fallback to request()->ip()), or browser_id from request body. Check GroupSessionSuggestionVote table for existing vote with (suggestion_id, voter_id/voter_ip/browser_id) before allowing vote.
      Files: `app/Http/Controllers/GroupSessionSuggestionController.php` (update store and vote methods)
      Verify: Run `php artisan tinker` and create suggestions with various voter types, confirming duplicates are rejected

- [ ] 10. Add responsive styling to poll widget.
      Update `resources/views/partials/group-session-poll.blade.php` to use project's CSS variables and grid layout: (1) suggestion cards in responsive grid (1 column mobile, 2+ columns on desktop), (2) button hover states matching existing design (--trust-blue, --coral for accent), (3) animations on vote using Alpine transitions (fade, scale), (4) form inputs styled with existing form patterns (padding, border-radius, font-family). Ensure font uses DM Sans for body, Fraunces for headings matching existing site.
      Files: `resources/views/partials/group-session-poll.blade.php` (add inline styles)
      Verify: Open page on mobile (375px width) and desktop (1920px width) and confirm layout is responsive and readable

- [ ] 11. Handle edge cases and add error handling.
      In controller: (1) handle missing subject_id and custom_subject gracefully (require at least custom_subject if subject_id not provided), (2) check group session exists before querying suggestions, (3) return 404 if suggestion doesn't exist, (4) in unvote, only delete vote if user matches voter or has admin access. In view: (1) show loading spinner while fetching, (2) show error message if API fails, (3) disable form submission during processing, (4) show empty state if no suggestions yet.
      Files: `app/Http/Controllers/GroupSessionSuggestionController.php`, `resources/views/partials/group-session-poll.blade.php`
      Verify: Test with browser dev tools: (1) submit empty form (should show validation error), (2) disable JavaScript and confirm graceful fallback, (3) test vote unvote cycle

---

## Styling Notes
- Use CSS variables: `--ink` (text), `--trust-blue` (primary), `--coral` (accent), `--gold` (highlight), `--mint` (success)
- Fonts: Fraunces for headings (h1-h3), DM Sans for body text
- Spacing pattern: 24px, 16px, 12px, 8px margins/padding (match existing code)
- Border radius: 12px for cards, 8px for smaller elements
- Transitions: 0.15s ease for hover/click effects

## Testing Checklist
- [ ] Guest user can view suggestions and vote without login
- [ ] Logged-in user sees vote count update immediately
- [ ] Voter dedup works: same guest cannot vote twice (localStorage and IP fallback)
- [ ] Admin panel loads and displays all suggestions
- [ ] Responsive layout works on mobile/tablet/desktop
- [ ] Subject dropdown pre-fills with active subjects only
- [ ] Suggestions appear sorted by vote count (most popular first)
- [ ] Empty state shown when no suggestions exist
- [ ] Form validation prevents empty/invalid submissions

## Database Schema
```sql
-- group_session_suggestions
CREATE TABLE group_session_suggestions (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  group_session_id BIGINT NOT NULL,
  subject_id BIGINT NULL,
  custom_subject VARCHAR(255) NULL,
  area TEXT NOT NULL,
  votes_count INT DEFAULT 0,
  created_by BIGINT NULL,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  FOREIGN KEY (group_session_id) REFERENCES examiner_clinics(id) ON DELETE CASCADE,
  FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE SET NULL,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_group_session_id (group_session_id),
  INDEX idx_created_at (created_at)
);

-- group_session_suggestion_votes
CREATE TABLE group_session_suggestion_votes (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  suggestion_id BIGINT NOT NULL,
  voter_id BIGINT NULL,
  voter_ip VARCHAR(45) NULL,
  browser_id VARCHAR(36) NULL,
  created_at TIMESTAMP,
  FOREIGN KEY (suggestion_id) REFERENCES group_session_suggestions(id) ON DELETE CASCADE,
  FOREIGN KEY (voter_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY unique_voter (suggestion_id, voter_id),
  UNIQUE KEY unique_ip (suggestion_id, voter_ip),
  UNIQUE KEY unique_browser (suggestion_id, browser_id),
  INDEX idx_created_at (created_at)
);
```

## Notes
- All models use timestamps (created_at, updated_at)
- Defensive database checks: migrations use Schema::hasColumn() to ensure compatibility
- Votes use UNIQUE constraints but allow NULL values to permit multiple anonymous votes if dedup fails (rare edge case)
- Admin delete is hard delete (no soft deletes) to keep data clean
- Suggestion creation doesn't require auth; created_by is NULL for guests
