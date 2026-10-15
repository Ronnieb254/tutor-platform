# Mwalimu – Kenya's Tutor Marketplace

A full-stack tutor booking marketplace built with Laravel 11, Livewire 3, Alpine.js, Tailwind CSS, Paystack payments, and Jitsi live sessions.

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 11 (PHP 8.2+) |
| Frontend | Livewire 3 + Alpine.js + Tailwind CSS v3 |
| Database | MySQL 8.0+ |
| Payments | Paystack (KES) |
| Live Sessions | Jitsi Meet |
| Auth | Laravel + Socialite (Google, GitHub, Facebook) |
| Queue | Database driver |

---

## Quick Setup

### 1. Prerequisites
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0+

### 2. Install dependencies
```bash
cd tutor-platform
composer install
npm install
```

### 3. Environment setup
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set:
```env
DB_DATABASE=tutor_platform
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Paystack (get from dashboard.paystack.com)
PAYSTACK_PUBLIC_KEY=pk_test_xxxxxxxxxxxx
PAYSTACK_SECRET_KEY=sk_test_xxxxxxxxxxxx
PAYSTACK_CURRENCY=KES

# Jitsi (optional – leave blank to use public meet.jit.si)
JITSI_DOMAIN=meet.jit.si
JITSI_APP_ID=
JITSI_APP_SECRET=

# Social OAuth (optional)
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

### 4. Database
```bash
php artisan migrate --seed
```

This creates all tables and seeds:
- Admin: `admin@Mwalimu.co.ke` / `password`
- 6 demo tutors (all approved)
- 3 demo students/parents
- 8 subject categories with 35+ subjects

### 5. Storage link
```bash
php artisan storage:link
```

### 6. Build assets
```bash
npm run dev
# or for production:
npm run build
```

### 7. Start server
```bash
php artisan serve
```

Visit: http://localhost:8000

---

## User Accounts (Demo)

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@Mwalimu.co.ke | password |
| Tutor | amara@Mwalimu.co.ke | password |
| Tutor | grace@Mwalimu.co.ke | password |
| Student | alice@example.com | password |
| Parent | sarah@example.com | password |

---

## Key Features

### For Students & Parents
- Browse 500+ verified tutors by subject, price, rating
- Book sessions in 15 / 30 / 45 / 60 minute blocks
- Pay securely with Paystack (M-Pesa or card)
- Receive unique session access code by email (STU-XXXXXX)
- Join live session via Jitsi (no download required)
- Leave reviews after completed sessions
- In-app messaging with tutor
- Wishlist / save favourite tutors
- Parents can book for child profiles

### For Tutors
- Sign up → upload documents → get verified → go live
- Set hourly rate (system auto-calculates 15/30/45/60 min prices)
- Set weekly availability schedule
- Receive booking notifications with session code (TUT-XXXXXX)
- Earnings wallet (platform takes 15% commission)
- Request payouts to bank account
- Manage courses (pre-recorded lessons)

### For Admins
- Approve/reject tutor document submissions
- Manage all users, bookings, categories
- Process payout requests
- Platform settings (commission rate, etc.)
- Blog management
- Full revenue dashboard

---

## Session Flow

```
Student books → Pays via Paystack → Webhook confirms →
  ├─ Tutor email: "New booking! Code: TUT-XXXXXX"
  └─ Student email: "Confirmed! Code: STU-XXXXXX"
                              ↓
              On session day: enter code → Jitsi live class opens
```

---

## Paystack Webhook

Set your webhook URL in the Paystack dashboard to:
```
https://yourdomain.com/api/paystack/webhook
```

The webhook handles `charge.success` events to confirm bookings and credit tutor wallets.

---

## Project Structure

```
app/
├── Http/Controllers/
│   ├── Auth/AuthController.php
│   ├── Auth/SocialAuthController.php
│   ├── BookingController.php
│   ├── PaystackWebhookController.php
│   ├── HomeController.php
│   ├── CourseController.php
│   └── BlogController.php
├── Livewire/
│   ├── Booking/ (BookingWizard, JoinSession, ChatBox, LeaveReview)
│   ├── Tutor/ (TutorDashboard, TutorOnboarding)
│   ├── Student/ (StudentDashboard)
│   ├── Tutors/ (TutorSearch)
│   └── Shared/ (NotificationBell)
├── Models/ (20+ Eloquent models)
├── Services/ (PaystackService, JitsiService, BookingService)
├── Notifications/ (BookingConfirmedTutor, BookingConfirmedStudent)
└── Policies/ (BookingPolicy)

resources/views/
├── layouts/ (app, guest, admin)
├── pages/ (home, tutors, courses, blog, dashboards, bookings)
├── livewire/ (all component views)
└── emails/ (booking confirmation templates)

database/
├── migrations/ (13 migration files, 25+ tables)
└── seeders/ (Admin, Tutor, Student, Category, PlatformSettings)
```

---

## Customisation

**Change commission rate:**
```bash
php artisan tinker
\App\Models\PlatformSetting::set('commission_rate', '0.10'); // 10%
```

**Change currency:**
Update `.env`: `PAYSTACK_CURRENCY=KES`

**Use self-hosted Jitsi:**
```env
JITSI_DOMAIN=your.jitsi.server
JITSI_APP_ID=your_app_id
JITSI_APP_SECRET=your_secret
```
