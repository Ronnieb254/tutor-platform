# TutorPlatform

A full-featured online tutoring marketplace built with Laravel 11, Livewire 3, Alpine.js, and Tailwind CSS. Payments via Paystack. Live sessions via Jitsi Meet.

---

## Requirements

| Tool | Version |
|------|---------|
| PHP | 8.2+ |
| Composer | 2.x |
| Node.js | 20+ |
| MySQL | 8.0+ |

---

## Setup Instructions

### 1. Clone the repository

```bash
git clone <repo-url> tutor-platform
cd tutor-platform
```

### 2. Copy environment file

```bash
cp .env.example .env
```

### 3. Install PHP dependencies

```bash
composer install
```

### 4. Generate application key

```bash
php artisan key:generate
```

### 5. Configure your environment

Open `.env` and set the following:

**Database**
```
DB_DATABASE=tutor_platform
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

**Paystack** (get keys from https://dashboard.paystack.com)
```
PAYSTACK_PUBLIC_KEY=pk_live_xxxxxxxxxxxx
PAYSTACK_SECRET_KEY=sk_live_xxxxxxxxxxxx
```

**Jitsi** (get credentials from https://jaas.8x8.vc)
```
JITSI_APP_ID=your_jitsi_app_id
JITSI_APP_SECRET=your_jitsi_app_secret
JITSI_DOMAIN=meet.jit.si
```

**Social Auth** (optional – Google, GitHub, Facebook OAuth apps)
```
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
```

### 6. Run migrations and seed the database

```bash
php artisan migrate --seed
```

### 7. Install Node dependencies and build assets

```bash
npm install && npm run build
```

### 8. Start the development server

```bash
php artisan serve
```

Visit http://localhost:8000

---

## Default Admin Login

After seeding, use these credentials to log in as admin:

- **Email:** admin@tutorplatform.com
- **Password:** password

---

## Platform Modules

| # | Module | Description |
|---|--------|-------------|
| 1 | **Auth & Profiles** | Registration, login, OAuth (Google/GitHub/Facebook), email verification, role-based access (admin, tutor, student) |
| 2 | **Course Management** | Tutors create courses with sections and video lessons; rich-text descriptions; thumbnail uploads |
| 3 | **Live Sessions** | Jitsi-powered 1-on-1 and group live classes with JWT-authenticated rooms; session scheduling and calendar |
| 4 | **Payments** | Paystack integration for course purchases and live-session bookings; 15% platform commission; payout tracking |
| 5 | **Messaging** | Real-time chat between tutors and students powered by Laravel Echo + Pusher |
| 6 | **Reviews & Ratings** | Students leave star ratings and reviews on courses and tutors; aggregate score displayed on profiles |
| 7 | **Wallet & Earnings** | Tutor earnings dashboard; withdrawal requests; admin approval flow |
| 8 | **Notifications** | In-app and email notifications for bookings, payments, messages, and reviews |
| 9 | **Admin Dashboard** | Full platform oversight: user management, course moderation, payout approvals, revenue analytics |
| 10 | **Search & Discovery** | Full-text course/tutor search with filters for subject, price, rating, and availability |

---

## Tech Stack

- **Backend:** Laravel 11 (PHP 8.2)
- **Frontend:** Livewire 3, Alpine.js 3, Tailwind CSS 3
- **Payments:** Paystack
- **Live Video:** Jitsi Meet (JWT-authenticated rooms)
- **Realtime:** Laravel Echo + Pusher
- **Auth:** Laravel Sanctum, Laravel Socialite
- **Permissions:** Spatie Laravel Permission
- **Image Processing:** Intervention Image 3
- **Build Tool:** Vite 5

---

## License

MIT
