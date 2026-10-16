# Tutor Platform - Session Management System Setup Guide

## Overview
This document outlines all the new components added to the tutoring platform to enable complete session management, notifications, messaging, and authentication with SweetAlert2 integration.

## 🎯 Components Created

### 1. Database Migrations
**Location:** `database/migrations/`

- `2026_10_16_100001_create_notifications_table.php` - Database notifications table
- `2026_10_16_100002_create_notification_preferences_table.php` - User notification preferences

**Run migrations:**
```bash
php artisan migrate
```

### 2. Models
**Location:** `app/Models/`

- `NotificationPreference.php` - User notification settings model

**Updated:**
- `User.php` - Added relationships for notification preferences, messages, and more

### 3. Services
**Location:** `app/Services/`

- `NotificationService.php` - Centralized notification management service with methods:
  - `notifyBookingCreated()`
  - `notifyBookingConfirmed()`
  - `notifyBookingCancelled()`
  - `notifyNewMessage()`
  - `notifySessionReminder()`
  - `notifyUserJoined()`
  - `getUserNotifications()`
  - `markNotificationAsRead()`
  - `markAllAsRead()`
  - `getUnreadCount()`

### 4. Notification Classes
**Location:** `app/Notifications/`

- `BookingCreatedNotification.php` - Sent to tutor when student books
- `BookingConfirmedNotification.php` - Sent to student when booking confirmed
- `BookingCancelledNotification.php` - Sent when booking is cancelled
- `NewMessageNotification.php` - Sent when new message arrives
- `SessionReminderNotification.php` - Sent 1 hour before session
- `UserJoinedSessionNotification.php` - Sent when participant joins

All notifications support both database and email channels.

### 5. Controllers
**Location:** `app/Http/Controllers/`

#### Authentication Controllers
- `Auth/RegisterController.php`
  - `show()` - Display registration form
  - `store()` - Create new user account
  - Auto-creates wallet, tutor profile, and notification preferences

- `Auth/LoginController.php`
  - `show()` - Display login form
  - `store()` - Authenticate user
  - `destroy()` - Logout user

#### Main Controllers
- `DashboardController.php` - Role-based dashboards
  - `index()` - Routes to appropriate dashboard
  - `studentDashboard()` - Student overview
  - `tutorDashboard()` - Tutor overview with earnings
  - `adminDashboard()` - Admin statistics
  - `parentDashboard()` - Parent overview

- `SessionController.php` - Session management
  - `index()` - List sessions
  - `show()` - Session details
  - `join()` - Join meeting (with validation)
  - `history()` - Past sessions
  - `confirm()` - Tutor confirms booking
  - `cancel()` - Cancel booking
  - `complete()` - Mark session complete

- `MessageController.php` - Messaging system
  - `index()` - Fetch messages
  - `store()` - Send message
  - `markAsRead()` - Mark as read
  - `getUnreadCount()` - Get unread count

- `NotificationController.php` - Notification management
  - `index()` - List notifications
  - `unread()` - Get unread notifications
  - `markAsRead()` - Mark single notification
  - `markAllAsRead()` - Mark all as read
  - `destroy()` - Delete notification

### 6. Authorization
**Location:** `app/Policies/`

- `BookingPolicy.php` - Booking access control
  - `view()` - Can user view this booking?
  - `confirm()` - Can user confirm?
  - `cancel()` - Can user cancel?
  - `complete()` - Can user complete?
  - `message()` - Can user message?

**Location:** `app/Providers/`
- `AuthServiceProvider.php` - Registers booking policy

### 7. Frontend Assets
**Location:** `public/js/`

- `sweetalert-config.js` - SweetAlert2 helper functions:
  - `showSuccess()` - Show success alert
  - `showError()` - Show error alert
  - `showConfirm()` - Show confirmation dialog
  - `showToast()` - Show toast notification
  - `showLoading()` - Show loading state
  - `hideLoading()` - Hide loading state
  - `handleFormSubmit()` - AJAX form handling
  - `confirmAction()` - Confirm action via AJAX
  - `confirmJoinSession()` - Join session confirmation
  - `cancelBooking()` - Cancel with reason dialog

### 8. Views

#### Authentication Views
- `resources/views/auth/login.blade.php` - Login form with SweetAlert
- `resources/views/auth/register.blade.php` - Registration form with role selection

#### Dashboard Views
- `resources/views/dashboards/student.blade.php` - Student dashboard
- `resources/views/dashboards/tutor.blade.php` - Tutor dashboard with pending bookings
- `resources/views/dashboards/admin.blade.php` - Admin overview
- `resources/views/dashboards/parent.blade.php` - Parent dashboard

#### Session Views
- `resources/views/sessions/index.blade.php` - List all sessions with filters
- `resources/views/sessions/show.blade.php` - Session details with chat
- `resources/views/sessions/history.blade.php` - Past sessions with reviews

#### Notification Views
- `resources/views/notifications/index.blade.php` - Notification list

#### Layout
- `resources/views/layouts/app.blade.php` - Main layout with navigation and notification bell

### 9. Routes
**Location:** `routes/web.php`

Added routes:
```php
// Authentication
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');

// Sessions
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::prefix('sessions')->name('sessions.')->group(function () {
        Route::get('/', [SessionController::class, 'index'])->name('index');
        Route::get('/history', [SessionController::class, 'history'])->name('history');
        Route::get('/{booking:uuid}', [SessionController::class, 'show'])->name('show');
        Route::get('/{booking:uuid}/join', [SessionController::class, 'join'])->name('join');
        Route::post('/{booking:uuid}/confirm', [SessionController::class, 'confirm'])->name('confirm');
        Route::post('/{booking:uuid}/cancel', [SessionController::class, 'cancel'])->name('cancel');
        Route::post('/{booking:uuid}/complete', [SessionController::class, 'complete'])->name('complete');
    });

    // Messages
    Route::prefix('sessions/{booking:uuid}/messages')->name('messages.')->group(function () {
        Route::get('/', [MessageController::class, 'index'])->name('index');
        Route::post('/', [MessageController::class, 'store'])->name('store');
        Route::post('/read', [MessageController::class, 'markAsRead'])->name('read');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/unread', [NotificationController::class, 'unread'])->name('unread');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('readAll');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
    });
});
```

## 📋 Installation Steps

### Step 1: Run Migrations
```bash
php artisan migrate
```

This creates:
- `notifications` table - Database notifications
- `notification_preferences` table - User notification settings

### Step 2: Clear Cache
```bash
php artisan cache:clear
php artisan config:cache
```

### Step 3: Compile Frontend Assets
```bash
npm run build
```

### Step 4: Configure Queue (for Email Notifications)
In `.env`:
```env
QUEUE_CONNECTION=database
```

Create queue job table:
```bash
php artisan queue:table
php artisan migrate
```

To process queued jobs:
```bash
php artisan queue:work
```

### Step 5: Test the System

1. **Register**: Visit `/register`
   - Select role (student/tutor/parent)
   - Fill in details
   - Account automatically created with wallet and profiles

2. **Login**: Visit `/login`
   - Use registered credentials
   - Directed to appropriate dashboard

3. **Create Booking**: (From existing booking system)
   - Tutor receives notification
   - Session appears on both dashboards

4. **Confirm Booking**: 
   - Tutor clicks "Confirm" on session
   - Student receives notification
   - Both directed to session details

5. **Send Messages**:
   - Go to session details
   - Type and send message
   - Receiver gets notification

6. **Join Session**:
   - Click "Join Now" (1 hour before to 30 min after scheduled time)
   - System validates and redirects to meeting URL

## 🔑 Key Features

### 1. Authentication System
- Registration with role selection (student/tutor/parent)
- Auto-creation of wallet and profiles
- Login with remember me functionality
- Logout functionality
- SweetAlert integration for all feedback

### 2. Notification System
- Database notifications (permanent record)
- Email notifications (configurable)
- 6 notification types with proper context
- User preferences for notification types
- Unread count tracking
- Mark as read functionality

### 3. Session Management
- View all sessions with filters
- Session history with status tracking
- Tutor confirmation workflow
- Student booking workflow
- Time-validated meeting join (60 min before to 30 min after)
- Participant tracking (student_joined, tutor_joined)

### 4. Messaging System
- Real-time messaging between participants
- Unread message tracking
- Message notifications
- Auto-marking as read when viewed

### 5. Dashboards
- **Student**: Upcoming sessions, total completed, unread messages, recent tutors
- **Tutor**: Upcoming sessions, pending bookings, earnings this month, average rating
- **Admin**: User statistics, revenue, recent activity, pending verifications
- **Parent**: Children profiles, upcoming sessions for each child

### 6. SweetAlert2 Integration
- Success/error notifications throughout
- Confirmation dialogs for actions
- Toast notifications for quick feedback
- Loading states for async operations
- Custom booking cancellation dialog with reason
- Session join confirmation

## 🔐 Security Features

### Authorization
- BookingPolicy ensures only participants can access sessions
- Policy-based authorization on all session routes
- User role verification on dashboards

### Validation
- All inputs validated with Laravel's validation
- CSRF protection on all forms
- Secure password requirements
- Email uniqueness check

### Data Protection
- Database transactions for critical operations
- Proper error handling
- User-friendly error messages

## 📊 Database Schema

### notifications
```sql
- id (uuid, primary)
- type (string) - Notification class name
- notifiable_type (string) - Model type
- notifiable_id (bigint) - Model ID
- data (json) - Notification data
- read_at (timestamp, nullable)
- created_at, updated_at
```

### notification_preferences
```sql
- id (bigint, primary)
- user_id (foreign key)
- email_enabled (boolean)
- sms_enabled (boolean)
- push_enabled (boolean)
- booking_notifications (boolean)
- message_notifications (boolean)
- reminder_notifications (boolean)
- created_at, updated_at
```

## 🚀 Usage Examples

### Register User
```php
POST /register
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "SecurePassword123",
    "password_confirmation": "SecurePassword123",
    "role": "student",
    "phone": "+254700000000"
}
```

### Login
```php
POST /login
{
    "email": "john@example.com",
    "password": "SecurePassword123",
    "remember": true
}
```

### Confirm Booking
```php
POST /sessions/{booking_uuid}/confirm
```

### Send Message
```php
POST /sessions/{booking_uuid}/messages
{
    "body": "Looking forward to our session tomorrow!"
}
```

### Join Session
```php
GET /sessions/{booking_uuid}/join
```

## 📞 API Responses

All controllers support both web and JSON responses:

**Success:**
```json
{
    "success": true,
    "message": "Operation successful",
    "redirect": "/dashboard",
    "data": {}
}
```

**Error:**
```json
{
    "success": false,
    "message": "Error description",
    "error": "Detailed error"
}
```

## 🔧 Configuration

### Email Notifications
Configure in `config/mail.php` and `.env`:
```env
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@tutorplatform.com
MAIL_FROM_NAME="Tutor Platform"
```

### Queue Jobs
Configure in `.env`:
```env
QUEUE_CONNECTION=database
```

### Session Settings
In `Booking` model, these are pre-configured:
- `canJoin()` - Validates 60 min before to 30 min after
- Session codes generated for validation
- Status workflow: pending → confirmed → completed/cancelled

## 📈 Performance Considerations

1. **Database Indexes** - Added on notification tables for quick queries
2. **Pagination** - All lists paginated (10-15 items per page)
3. **Query Optimization** - Using eager loading with `with()` for relationships
4. **Caching** - Consider caching dashboard statistics
5. **Queue Processing** - Email notifications run asynchronously

## 🧪 Testing Workflow

1. Create student account (register)
2. Create tutor account (register)
3. Create booking via existing system
4. Tutor receives notification
5. Tutor confirms booking
6. Student receives notification
7. Both view session details
8. Send messages back and forth
9. Verify messages appear real-time
10. Join session 1 hour before scheduled time
11. System validates and redirects

## 🐛 Troubleshooting

### Notifications Not Appearing
- Check `notification_preferences` for user
- Verify notifications table has records
- Check email configuration for email notifications
- Run: `php artisan queue:work` if using queue

### Session Join Not Working
- Verify current time is within window (60 min before to 30 min after)
- Check booking status is 'confirmed'
- Verify `meeting_url` is set on booking

### Messages Not Sending
- Check booking UUID is valid
- Verify user is participant (student or tutor)
- Check message body is not empty

### Login Issues
- Verify email exists in database
- Check password is correct
- Ensure user account is active (`is_active = true`)

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [SweetAlert2 Documentation](https://sweetalert2.github.io/)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)

## 📝 File Checklist

### Controllers Created (7 files)
- ✅ `app/Http/Controllers/Auth/RegisterController.php`
- ✅ `app/Http/Controllers/Auth/LoginController.php`
- ✅ `app/Http/Controllers/DashboardController.php`
- ✅ `app/Http/Controllers/SessionController.php`
- ✅ `app/Http/Controllers/MessageController.php`
- ✅ `app/Http/Controllers/NotificationController.php`

### Models (1 file)
- ✅ `app/Models/NotificationPreference.php`

### Notifications (6 files)
- ✅ `app/Notifications/BookingCreatedNotification.php`
- ✅ `app/Notifications/BookingConfirmedNotification.php`
- ✅ `app/Notifications/BookingCancelledNotification.php`
- ✅ `app/Notifications/NewMessageNotification.php`
- ✅ `app/Notifications/SessionReminderNotification.php`
- ✅ `app/Notifications/UserJoinedSessionNotification.php`

### Services (1 file)
- ✅ `app/Services/NotificationService.php`

### Policies (1 file)
- ✅ `app/Policies/BookingPolicy.php`

### Providers (1 file)
- ✅ `app/Providers/AuthServiceProvider.php`

### Migrations (2 files)
- ✅ `database/migrations/2026_10_16_100001_create_notifications_table.php`
- ✅ `database/migrations/2026_10_16_100002_create_notification_preferences_table.php`

### Views (11 files)
- ✅ `resources/views/auth/login.blade.php`
- ✅ `resources/views/auth/register.blade.php`
- ✅ `resources/views/dashboards/student.blade.php`
- ✅ `resources/views/dashboards/tutor.blade.php`
- ✅ `resources/views/dashboards/admin.blade.php`
- ✅ `resources/views/dashboards/parent.blade.php`
- ✅ `resources/views/sessions/index.blade.php`
- ✅ `resources/views/sessions/show.blade.php`
- ✅ `resources/views/sessions/history.blade.php`
- ✅ `resources/views/notifications/index.blade.php`
- ✅ `resources/views/layouts/app.blade.php`

### Assets (1 file)
- ✅ `public/js/sweetalert-config.js`

### Routes
- ✅ Updated `routes/web.php`

**Total: 32 files created/updated**

---

## 🎉 Summary

You now have a complete, production-ready session management system with:
- ✅ User authentication (register/login/logout)
- ✅ Role-based dashboards (student/tutor/admin/parent)
- ✅ Session management with status workflow
- ✅ Real-time messaging system
- ✅ Comprehensive notification system (email + database)
- ✅ Meeting join validation
- ✅ Authorization policies
- ✅ SweetAlert2 integration throughout
- ✅ Responsive design
- ✅ Security best practices

The system is ready for deployment and can handle the complete tutor-student booking workflow!
