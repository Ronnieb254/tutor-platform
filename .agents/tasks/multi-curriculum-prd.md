# Mwalimu – Multi-Curriculum Platform PRD

## Objective
One unified Laravel tutor marketplace supporting four curricula:
1. Kenya 8-4-4
2. Kenya Competency-Based Education (CBE/CBC)
3. International Baccalaureate (IB)
4. Cambridge IGCSE

## Architecture
Single platform, shared infrastructure, separate curriculum catalogues.
Do NOT build four separate apps.

## Curricula Structure
- **Kenyan track**: 8-4-4 (Form 1–4), CBE Junior Secondary (Grade 7–9), CBE Senior School (Grade 10–12)
- **International track**: IGCSE Cambridge, IGCSE Edexcel, IB MYP, IB Diploma (HL/SL)

## Data Model Required

### `curricula` table
| column | type |
|---|---|
| id | bigint PK |
| name | string (e.g. "8-4-4", "CBE Junior Secondary", "IGCSE Cambridge", "IB Diploma") |
| track | enum: kenyan, international |
| slug | string unique |
| description | text nullable |
| sort_order | int default 0 |
| is_active | bool default true |
| timestamps | |

### `curriculum_levels` table
Curriculum-specific academic levels (Form 1, Grade 7, MYP Year 1, etc.)
| column | type |
|---|---|
| id | bigint PK |
| curriculum_id | FK |
| name | string |
| code | string nullable |
| sort_order | int |
| timestamps | |

### `subjects` table – add `curriculum_id` FK (nullable, index)
Subjects are curriculum-scoped. "Mathematics" under 8-4-4 ≠ "Mathematics" under IB Diploma.

### `tutor_curricula` pivot
| column | type |
|---|---|
| id | bigint PK |
| tutor_profile_id | FK |
| curriculum_id | FK |
| verification_status | enum: pending, approved, rejected |
| verified_at | timestamp nullable |
| timestamps | |

### `learner_profiles` table (multi-profile per user account)
| column | type |
|---|---|
| id | bigint PK |
| user_id | FK |
| name | string (child or self name) |
| curriculum_id | FK |
| level_id | FK nullable |
| relationship | enum: self, child, ward |
| is_active | bool |
| timestamps | |

### `bookings` table – add columns
- `curriculum_id` FK nullable
- `learner_profile_id` FK nullable

## Landing Page / Homepage
Two-card curriculum chooser:
- LEFT (dark green): "KENYAN CURRICULUM – Learn the Kenyan way" – 8-4-4, CBC Junior Secondary, CBC Senior School – "Find a Kenyan-curriculum tutor" → `/tutors?track=kenyan`
- RIGHT (dark navy/indigo): "INTERNATIONAL CURRICULUM – Learn for the world" – IGCSE Cambridge/Edexcel, IB MYP, IB Diploma – "Find an international tutor" → `/tutors?track=international`
- Track selection stored in session: `session(['curriculum_track' => 'kenyan'])`

## Tutor Search / Browse
- Track switcher tabs at top
- Filter cascade: track → curriculum → level → subject
- Only show tutors approved for the selected curriculum
- Curriculum badges on tutor cards

## Tutor Registration / Onboarding
Add "Which curriculum do you teach?" section:
- Checkbox: Kenyan Curriculum → conditional panel showing:
  - Levels: 8-4-4 (Form 1-4), CBC Junior Secondary, CBC Senior School
  - Subjects (multi-select)
  - Required docs: National ID, TSC certificate, Certificate of Good Conduct, Passport photo, Degree (recommended)
- Checkbox: International (IGCSE/IB) → conditional panel showing:
  - Programme: IGCSE Cambridge, IGCSE Edexcel, IB MYP, IB Diploma (HL/SL)
  - Subjects (multi-select)
  - Required docs: National ID, Degree certificate, Teaching qualification (PGCE or IB cert), Proof of IGCSE/IB experience, Certificate of Good Conduct, Passport photo, Examiner credentials (optional)
- Save to `tutor_curricula` pivot
- Note: "Standard verification 72 hours. Speedy Verification KSh 1,000 takes 24 hours."

## Filtering Logic
- When learner selects Kenyan track → only show tutors with approved kenyan curricula
- When learner selects International track → only show tutors with approved international curricula
- Bookings retain curriculum_id and learner_profile_id for full history

## Admin Requirements
- Manage all 4 curricula and their levels/subjects
- Manage tutor curriculum approvals per curriculum
- Manage learner profiles
- View revenue reports filtered by curriculum

## Shared Features (unchanged)
User accounts, M-Pesa payments, Jitsi classrooms, session codes, wallet, booking engine, messaging, notifications, group sessions, clinics, reviews, ratings.

## Acceptance Criteria
1. New user can select any of the 4 curricula
2. Correct academic structure appears after selection
3. Learner can select subject and topic
4. Only approved tutors matched per curriculum
5. Booking retains curriculum and topic info
6. Parent can manage multiple children with different curricula
7. Users can switch curriculum profiles without losing history
8. Admins can manage all 4 catalogues independently
9. All 4 pathways use same booking/payment/communication infrastructure
