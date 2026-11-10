<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')
<title>{{ $tutor->user->name }} - Verified Tutor | Mwalimu</title>
<meta name="description" content="{{ Str::limit($tutor->bio ?? 'Book a session with '.$tutor->user->name.' on Mwalimu.', 160) }}">
<style>
/* ============================================
   HERO SECTION
   ============================================ */
.profile-hero {
  background: linear-gradient(135deg, #111C16 0%, #15281F 60%, #1a2e22 100%);
  padding: 56px 0 0;
  position: relative;
  overflow: hidden;
}

.profile-hero::before {
  content: '';
  position: absolute;
  top: -120px;
  right: -80px;
  width: 400px;
  height: 400px;
  background: rgba(10, 100, 188, .15);
  border-radius: 50%;
  pointer-events: none;
}

.profile-hero::after {
  content: '';
  position: absolute;
  bottom: -60px;
  left: 20%;
  width: 200px;
  height: 200px;
  background: rgba(212, 160, 23, .08);
  border-radius: 50%;
  pointer-events: none;
}

/* Hero breadcrumb */
.hero-breadcrumb {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  color: rgba(255, 255, 255, .7);
  text-decoration: none;
  transition: color .15s;
}

.hero-breadcrumb:hover {
  color: rgba(255, 255, 255, .9);
}

/* Hero title */
.hero-title {
  font-family: 'Fraunces', serif;
  font-size: clamp(24px, 4vw, 42px);
  font-weight: 800;
  color: #fff;
  line-height: 1.2;
  margin: 0;
}

/* Hero badges */
.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 6px 14px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 700;
  border: 1px solid;
}

.hero-badge-verified {
  background: rgba(16, 185, 129, .2);
  color: #4ADE80;
  border-color: rgba(16, 185, 129, .3);
}

.hero-badge-examiner {
  background: rgba(212, 160, 23, .2);
  color: #FCD34D;
  border-color: rgba(212, 160, 23, .25);
}

/* Hero stats row */
.hero-stats {
  display: flex;
  flex-wrap: wrap;
  gap: 24px;
  font-size: 14px;
  color: rgba(255, 255, 255, .65);
}

.hero-stat-item {
  display: flex;
  align-items: center;
  gap: 6px;
}

.hero-stat-item strong {
  color: #fff;
}

/* ============================================
   TAB NAVIGATION
   ============================================ */
.ptab {
  padding: 16px 24px;
  font-size: 15px;
  font-weight: 600;
  background: none;
  border: none;
  cursor: pointer;
  color: rgba(255, 255, 255, .5);
  border-bottom: 3px solid transparent;
  transition: all .25s;
  font-family: inherit;
  white-space: nowrap;
}

.ptab.on,
.ptab:hover {
  color: #fff;
  border-bottom-color: #0A64BC;
}

/* ============================================
   2-COLUMN LAYOUT (DESKTOP)
   ============================================ */
.show-layout {
  display: grid;
  grid-template-columns: 1fr;
  gap: 24px;
}

@media (min-width: 1024px) {
  .show-layout {
    grid-template-columns: 1fr 360px;
  }
}

/* Sidebar wrapper for desktop */
.sidebar-sticky {
  display: none;
  position: sticky;
  top: 96px;
  align-self: start;
  flex-direction: column;
  gap: 20px;
}

@media (min-width: 1024px) {
  .sidebar-sticky {
    display: flex;
  }
}

/* ============================================
   PROFESSIONAL CARDS & BUTTONS
   ============================================ */
.prof-card {
  background: #fff;
  border-radius: 18px;
  border: 1px solid rgba(21, 40, 31, .08);
  padding: 28px;
  box-shadow: 0 2px 12px rgba(21, 40, 31, .05);
  transition: all .3s;
}

.prof-card:hover {
  box-shadow: 0 8px 28px rgba(21, 40, 31, .12);
}

.prof-btn {
  border-radius: 14px;
  color: #fff;
  font-weight: 700;
  border: none;
  cursor: pointer;
  font-family: inherit;
  transition: all .3s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
}

.prof-btn:hover:not(:disabled) {
  transform: translateY(-2px);
}

/* ============================================
   STAT CARDS
   ============================================ */
.stat-card {
  background: linear-gradient(135deg, rgba(255, 255, 255, .95), #fff);
  border-radius: 16px;
  border: 1px solid rgba(21, 40, 31, .08);
  padding: 24px;
  display: flex;
  align-items: center;
  gap: 18px;
  box-shadow: 0 4px 16px rgba(21, 40, 31, .06);
  transition: all .3s;
}

.stat-card:hover {
  box-shadow: 0 8px 32px rgba(21, 40, 31, .12);
  transform: translateY(-2px);
}

.stat-card-icon {
  width: 56px;
  height: 56px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  font-size: 26px;
  color: #fff;
}

.stat-card-value {
  font-family: 'Fraunces', serif;
  font-size: 28px;
  font-weight: 700;
  color: #15281F;
  line-height: 1;
  margin: 0;
}

.stat-card-label {
  font-size: 13px;
  color: rgba(21, 40, 31, .6);
  margin-top: 4px;
  font-weight: 500;
}

/* ============================================
   ABOUT SECTION
   ============================================ */
.section-title {
  font-family: 'Fraunces', serif;
  font-size: 22px;
  color: #15281F;
  margin: 0 0 16px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.section-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.about-bio {
  font-size: 16px;
  color: #15281F;
  line-height: 2;
  margin: 0;
}

.field-label {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .08em;
  color: rgba(21, 40, 31, .45);
  margin-bottom: 12px;
  display: block;
}

.lang-pill {
  background: #F4F6F4;
  color: #15281F;
  padding: 7px 16px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: 500;
  display: inline-flex;
  margin: 0 6px 6px 0;
}

/* ============================================
   SUBJECTS SECTION
   ============================================ */
.subjects-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 12px;
}

@media (min-width: 640px) {
  .subjects-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 1024px) {
  .subjects-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

.subject-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 14px 16px;
  background: #F9FAF9;
  border-radius: 12px;
  border: 1px solid rgba(21, 40, 31, .07);
  transition: all .3s;
}

.subject-item:hover {
  border-color: #0A64BC;
  background: rgba(10, 100, 188, .04);
  transform: translateY(-1px);
}

.subject-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #0A64BC;
  flex-shrink: 0;
}

.subject-name {
  font-size: 14px;
  font-weight: 500;
  color: #15281F;
}

.subject-category {
  font-size: 11px;
  color: rgba(21, 40, 31, .4);
  margin-left: auto;
}

/* ============================================
   AVAILABILITY SECTION
   ============================================ */
.avail-row {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 14px 16px;
  border-radius: 13px;
  margin-bottom: 7px;
  border: 1px solid;
}

.avail-row-available {
  background: rgba(10, 100, 188, .04);
  border-color: rgba(10, 100, 188, .15);
}

.avail-row-off {
  background: #FAFCFA;
  border-color: rgba(21, 40, 31, .07);
}

.avail-day {
  font-weight: 600;
  font-size: 14px;
  color: #15281F;
  width: 100px;
  flex-shrink: 0;
}

.avail-time {
  font-size: 14px;
  font-weight: 500;
  color: #0A64BC;
  display: flex;
  align-items: center;
  gap: 6px;
}

.avail-badge {
  margin-left: auto;
  padding: 4px 12px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 4px;
}

.avail-badge-available {
  background: rgba(16, 185, 129, .1);
  color: #10B981;
}

.avail-badge-off {
  background: #F3F4F6;
  color: #9CA3AF;
}

/* ============================================
   REVIEWS SECTION
   ============================================ */
.rating-number {
  font-family: 'Fraunces', serif;
  font-size: 64px;
  font-weight: 800;
  color: #15281F;
  line-height: 1;
}

.rating-bar {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 10px;
}

.rbar-track {
  height: 8px;
  background: #F4F6F4;
  border-radius: 999px;
  overflow: hidden;
  flex: 1;
}

.rbar-fill {
  height: 100%;
  background: #EEBE15;
  border-radius: 999px;
  transition: width .5s ease;
}

.review-card {
  display: flex;
  align-items: flex-start;
  gap: 14px;
}

.review-avatar {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  font-weight: 700;
  color: #fff;
  flex-shrink: 0;
}

.review-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  flex-wrap: wrap;
  margin-bottom: 8px;
}

.review-name {
  font-weight: 700;
  font-size: 14px;
  color: #15281F;
}

.review-date {
  font-size: 12px;
  color: rgba(21, 40, 31, .4);
}

.review-comment {
  font-size: 14px;
  color: #15281F;
  line-height: 1.8;
}

/* ============================================
   SESSION TIER CARDS
   ============================================ */
.tier-card {
  border: 2px solid rgba(21, 40, 31, .15);
  border-radius: 18px;
  padding: 24px;
  text-align: left;
  cursor: pointer;
  transition: all .3s ease;
  background: #fff;
  display: block;
  width: 100%;
  box-shadow: 0 2px 8px rgba(21, 40, 31, .04);
  position: relative;
}

.tier-card:hover {
  border-color: #0A64BC;
  background: rgba(10, 100, 188, .04);
  box-shadow: 0 8px 24px rgba(10, 100, 188, .12);
  transform: translateY(-2px);
}

.tier-card.selected {
  border-color: #0A64BC;
  background: rgba(10, 100, 188, .08);
  box-shadow: 0 8px 24px rgba(10, 100, 188, .15);
}

.tier-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 8px;
}

.tier-card-icon {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  font-size: 18px;
  color: #fff;
}

.tier-card-name {
  font-size: 14px;
  font-weight: 700;
  color: #15281F;
}

.tier-card-duration {
  font-size: 11px;
  color: rgba(21, 40, 31, .5);
}

.tier-card-price {
  font-family: 'Fraunces', serif;
  font-weight: 800;
  text-align: right;
  font-size: 18px;
}

.tier-card-description {
  font-size: 12px;
  color: rgba(21, 40, 31, .5);
  margin-top: 8px;
}

/* ============================================
   BOOKING FORMS & INPUTS
   ============================================ */
.book-input,
.book-select {
  width: 100%;
  height: 46px;
  padding: 0 14px;
  border-radius: 12px;
  border: 1.5px solid rgba(21, 40, 31, .18);
  font-size: 14px;
  font-family: inherit;
  outline: none;
  background: #fff;
  box-sizing: border-box;
  transition: all .2s;
}

.book-input:focus,
.book-select:focus {
  border-color: #0A64BC;
  box-shadow: 0 0 0 3px rgba(10, 100, 188, .12);
}

.book-textarea {
  width: 100%;
  padding: 12px 14px;
  border-radius: 12px;
  border: 1.5px solid rgba(21, 40, 31, .18);
  font-size: 14px;
  font-family: inherit;
  outline: none;
  resize: none;
  line-height: 1.6;
  background: #fff;
  box-sizing: border-box;
  transition: all .2s;
}

.book-textarea:focus {
  border-color: #0A64BC;
  box-shadow: 0 0 0 3px rgba(10, 100, 188, .12);
}

.time-slot-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 8px;
}

@media (min-width: 480px) {
  .time-slot-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

@media (min-width: 768px) {
  .time-slot-grid {
    grid-template-columns: repeat(5, 1fr);
  }
}

.time-slot-btn {
  padding: 10px 6px;
  border-radius: 10px;
  border: 1.5px solid rgba(21, 40, 31, .12);
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all .15s;
  font-family: inherit;
  text-align: center;
  background: #F9FAF9;
  color: #15281F;
}

.time-slot-btn:hover {
  border-color: #0A64BC;
}

.time-slot-btn.selected {
  background: #0A64BC;
  color: #fff;
  border-color: #0A64BC;
}

/* ============================================
   BOOKING PROGRESS STEPS
   ============================================ */
.booking-progress {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 16px;
  margin-bottom: 28px;
}

.booking-step {
  display: flex;
  align-items: center;
}

.booking-step-connector {
  height: 2px;
  width: 48px;
  transition: background .3s;
  margin: 0 8px;
}

.booking-step-dot {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: 700;
  transition: all .3s;
}

.booking-step-label {
  font-size: 11px;
  font-weight: 600;
  transition: color .3s;
}

/* ============================================
   SIDEBAR COMPONENTS
   ============================================ */
.sidebar-stats-card {
  background: #fff;
  border-radius: 18px;
  border: 1px solid rgba(21, 40, 31, .08);
  padding: 24px;
  box-shadow: 0 2px 12px rgba(21, 40, 31, .05);
}

.sidebar-stat-row {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 0;
  border-bottom: 1px solid rgba(21, 40, 31, .07);
}

.sidebar-stat-row:last-child {
  border-bottom: none;
}

.sidebar-stat-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  color: #fff;
  flex-shrink: 0;
}

.sidebar-stat-text {
  flex: 1;
}

.sidebar-stat-value {
  font-weight: 700;
  font-size: 16px;
  color: #15281F;
}

.sidebar-stat-label {
  font-size: 12px;
  color: rgba(21, 40, 31, .55);
  margin-top: 2px;
}

.sidebar-cta-btn {
  width: 100%;
  height: 88px;
  background: linear-gradient(135deg, #0A64BC 0%, #0052A3 100%);
  color: #fff;
  font-size: 18px;
  font-weight: 800;
  border: 3px solid #0A64BC;
  cursor: pointer;
  font-family: inherit;
  transition: all .3s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  border-radius: 16px;
  box-shadow: 0 10px 36px rgba(10, 100, 188, .4);
}

.sidebar-cta-btn:hover {
  transform: translateY(-4px);
  box-shadow: 0 16px 48px rgba(10, 100, 188, .5);
}

.sidebar-sessions-list {
  background: #fff;
  border-radius: 18px;
  border: 1px solid rgba(21, 40, 31, .08);
  padding: 20px;
  box-shadow: 0 2px 12px rgba(21, 40, 31, .05);
}

.sidebar-sessions-title {
  font-size: 13px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .08em;
  color: rgba(21, 40, 31, .45);
  margin-bottom: 14px;
  display: block;
}

.sidebar-session-type {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 11px 0;
  font-size: 13px;
  color: #15281F;
}

.sidebar-session-icon {
  width: 28px;
  height: 28px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  color: #fff;
  flex-shrink: 0;
}

.sidebar-session-info {
  flex: 1;
}

.sidebar-session-name {
  font-weight: 700;
  display: block;
}

.sidebar-session-time {
  font-size: 12px;
  color: rgba(21, 40, 31, .5);
}

.sidebar-session-price {
  font-weight: 700;
  color: #0A64BC;
}

/* ============================================
   ANIMATIONS
   ============================================ */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
</head>
<body style="display:flex;flex-direction:column;min-height:100vh;margin:0;padding:0;">
@php
$prices = [20=>300,40=>600,60=>900];
$sessNames = [20=>'Quick Help',40=>'Guided Practice',60=>'Deep Dive'];
$sessDesc = [20=>'Rapid revision or a quick question',40=>'Full structured lesson with Q&A',60=>'Maximum learning, best value'];
$isFreeFirstSession = false;
$colors = ['#0A64BC','#10B981','#E8522A','#EEBE15','#3B82F6'];
$avColor = $colors[$tutor->id % count($colors)];
$reviews = $tutor->reviews()->where('is_visible',true)->with('student')->latest()->paginate(8);
@endphp

@include('partials.nav')

{{-- PROFILE HERO --}}
<div class="profile-hero">
  <div style="max-width:1360px;margin:0 auto;padding:0 24px;">
    {{-- Breadcrumb --}}
    <div style="margin-bottom:20px;">
      <a href="{{ route('tutors.index') }}" class="hero-breadcrumb">
        <span style="font-size:inherit;"><i class="fa-solid fa-arrow-left"></i></span> All Tutors
      </a>
    </div>

    {{-- Profile Header --}}
    <div style="display:flex;align-items:flex-start;gap:24px;flex-wrap:wrap;margin-bottom:32px;">
      {{-- Avatar --}}
      <div style="width:100px;height:100px;border-radius:22px;background:{{ $avColor }};display:flex;align-items:center;justify-content:center;font-family:'Fraunces',serif;font-size:44px;font-weight:700;color:#fff;flex-shrink:0;border:3px solid rgba(255,255,255,.15);">
        {{ strtoupper(substr($tutor->user->name,0,1)) }}
      </div>

      {{-- Info Column --}}
      <div style="flex:1;min-width:0;">
        {{-- Title and Badges --}}
        <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:12px;">
          <h1 class="hero-title">{{ $tutor->user->name }}</h1>
          <span class="hero-badge hero-badge-verified">
            <i class="fa-solid fa-scroll"></i> TSC Verified
          </span>
          @if($tutor->is_tsc_registered ?? false)
          <span class="hero-badge hero-badge-examiner">
            <i class="fa-solid fa-star"></i> National Examiner
          </span>
          @endif
        </div>

        {{-- Subject Pills --}}
        <div style="display:flex;flex-wrap:wrap;gap:7px;margin-bottom:16px;">
          @foreach($tutor->subjects->take(5) as $s)
          <span style="background:rgba(255,255,255,.1);color:rgba(255,255,255,.85);padding:5px 13px;border-radius:999px;font-size:12px;font-weight:500;border:1px solid rgba(255,255,255,.12);">{{ $s->name }}</span>
          @endforeach
          @if($tutor->subjects->count() > 5)
          <span style="background:rgba(255,255,255,.1);color:rgba(255,255,255,.85);padding:5px 13px;border-radius:999px;font-size:12px;font-weight:500;border:1px solid rgba(255,255,255,.12);">+{{ $tutor->subjects->count() - 5 }} more</span>
          @endif
        </div>

        {{-- Stats Row --}}
        <div class="hero-stats">
          <span class="hero-stat-item">
            <i class="fa-solid fa-star"></i>
            <strong>{{ number_format($tutor->rating_avg,1) }}</strong>
            <span>({{ $tutor->total_reviews }} reviews)</span>
          </span>
          <span class="hero-stat-item">
            <i class="fa-solid fa-chalkboard"></i>
            <strong>{{ number_format($tutor->total_sessions) }}</strong> sessions
          </span>
          <span class="hero-stat-item">
            <i class="fa-solid fa-clock"></i>
            Responds within <strong>2hr</strong>
          </span>
        </div>
      </div>
    </div>

    {{-- Tab Bar --}}
    <div style="overflow-x:auto;display:flex;-webkit-overflow-scrolling:touch;border-top:1px solid rgba(255,255,255,.1);">
      <div style="display:flex;gap:0;">
        <button class="ptab on" onclick="showTab('about',this)">
          <i class="fa-solid fa-user"></i> About
        </button>
        <button class="ptab" onclick="showTab('book',this)" id="book-tab-btn" style="color:#EEBE15;border-bottom-color:#EEBE15;">
          <i class="fa-solid fa-circle-check"></i> Book Session
        </button>
        <button class="ptab" onclick="showTab('availability',this)">
          <i class="fa-solid fa-calendar"></i> Availability
        </button>
        <button class="ptab" onclick="showTab('reviews',this)">
          <i class="fa-solid fa-star"></i> Reviews ({{ $tutor->total_reviews }})
        </button>
      </div>
    </div>
  </div>
</div>

{{-- MAIN CONTENT --}}
<div style="max-width:1360px;margin:0 auto;padding:32px 24px 64px;flex:1;width:100%;">
  <div class="show-layout">

    {{-- LEFT COLUMN: Tab Content --}}
    <div style="min-width:0;">

      {{-- ABOUT TAB --}}
      <div id="tab-about">
        {{-- Stats Cards Grid --}}
        <div style="display:grid;grid-template-columns:repeat(1,1fr);gap:18px;margin-bottom:32px;">
          @media (min-width: 640px) {
            <style>
              .stats-grid-desktop { grid-template-columns: repeat(3, 1fr) !important; }
            </style>
          }
          <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-bottom:32px;" class="stats-grid-desktop">
            @foreach([
            ['fa-chalkboard','#0A64BC',number_format($tutor->total_sessions),'Sessions Completed'],
            ['fa-star','#EEBE15',$tutor->total_reviews,'Total Reviews'],
            ['fa-reply','#E8522A','<2hr','Response Time'],
            ] as [$ic,$col,$val,$lbl])
            <div class="stat-card">
              <div class="stat-card-icon" style="background:{{ $col }};box-shadow:0 4px 12px {{ $col }}40;">
                <i class="fa-solid {{ $ic }}"></i>
              </div>
              <div>
                <div class="stat-card-value">{!! $val !!}</div>
                <div class="stat-card-label">{{ $lbl }}</div>
              </div>
            </div>
            @endforeach
          </div>
        </div>

        {{-- Bio Card --}}
        <div class="prof-card" style="margin-bottom:32px;">
          <h2 class="section-title">
            <span class="section-icon" style="color:#0A64BC;"><i class="fa-solid fa-user"></i></span>
            About {{ explode(' ',$tutor->user->name)[0] }}
          </h2>
          <p class="about-bio">{{ $tutor->bio ?? 'This tutor hasn\'t added a bio yet.' }}</p>

          @if($tutor->languages)
          <div style="margin-top:24px;padding-top:20px;border-top:1px solid rgba(21,40,31,.08);">
            <label class="field-label">Languages</label>
            <div style="display:flex;flex-wrap:wrap;gap:8px;">
              @foreach($tutor->languages as $lang)
              <span class="lang-pill">{{ $lang }}</span>
              @endforeach
            </div>
          </div>
          @endif
        </div>

        {{-- Subjects Card --}}
        <div class="prof-card">
          <h3 class="section-title">
            <span class="section-icon" style="color:#10B981;"><i class="fa-solid fa-book-open"></i></span>
            Subjects Taught
          </h3>
          <div class="subjects-grid">
            @foreach($tutor->subjects as $s)
            <div class="subject-item">
              <span class="subject-dot"></span>
              <span class="subject-name">{{ $s->name }}</span>
              @if($s->category)<span class="subject-category">{{ $s->category->name }}</span>@endif
            </div>
            @endforeach
          </div>
        </div>
      </div>

      {{-- AVAILABILITY TAB --}}
      <div id="tab-availability" style="display:none;">
        <div class="prof-card">
          <h2 class="section-title">
            <span class="section-icon" style="color:#E8522A;"><i class="fa-solid fa-calendar"></i></span>
            Weekly Schedule
          </h2>
          <p style="font-size:14px;color:rgba(21,40,31,.55);margin:0 0 24px;">Nairobi, Kenya Time (EAT – UTC+3)</p>
          
          @php
          $avails = $tutor->availabilities()->where('is_active',true)->orderBy('day_of_week')->get();
          $days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
          @endphp
          
          @if($avails->isEmpty())
          <div style="text-align:center;padding:40px;color:rgba(21,40,31,.45);">
            <i class="fa-solid fa-circle-xmark" style="font-size:32px;margin-bottom:12px;display:block;"></i>
            <p style="font-size:14px;">Availability not set. Book anyway - the tutor will confirm a time that works.</p>
          </div>
          @else
          @foreach($days as $i => $day)
          @php $av = $avails->firstWhere('day_of_week',$i); @endphp
          <div class="avail-row {{ $av ? 'avail-row-available' : 'avail-row-off' }}">
            <span class="avail-day">{{ $day }}</span>
            @if($av)
            <span class="avail-time">
              <i class="fa-solid fa-clock"></i>
              {{ substr($av->start_time,0,5) }} - {{ substr($av->end_time,0,5) }}
            </span>
            <span class="avail-badge avail-badge-available">
              <i class="fa-solid fa-circle-check"></i> Available
            </span>
            @else
            <span style="font-size:13px;color:rgba(21,40,31,.4);">Not available</span>
            <span class="avail-badge avail-badge-off">Off</span>
            @endif
          </div>
          @endforeach
          @endif
        </div>
      </div>

      {{-- REVIEWS TAB --}}
      <div id="tab-reviews" style="display:none;">
        {{-- Rating Overview --}}
        <div class="prof-card" style="margin-bottom:24px;">
          <div style="display:flex;align-items:flex-start;gap:32px;flex-wrap:wrap;">
            <div style="text-align:center;flex-shrink:0;">
              <div class="rating-number">{{ number_format($tutor->rating_avg,1) }}</div>
              <div style="display:flex;gap:3px;justify-content:center;margin:12px 0;">
                @for($i=1;$i<=5;$i++)
                <i class="fa-solid fa-star" style="color:{{ $i<=$tutor->rating_avg ? '#EEBE15' : '#E5E7EB' }};font-size:20px;"></i>
                @endfor
              </div>
              <div style="font-size:13px;color:rgba(21,40,31,.5);">{{ $tutor->total_reviews }} reviews</div>
            </div>
            <div style="flex:1;min-width:220px;">
              @foreach([5,4,3,2,1] as $star)
              @php $cnt=$tutor->reviews()->where('rating',$star)->count(); $pct=$tutor->total_reviews>0?round($cnt/$tutor->total_reviews*100):0; @endphp
              <div class="rating-bar">
                <span style="font-size:13px;color:rgba(21,40,31,.5);width:16px;text-align:right;">{{ $star }}</span>
                <i class="fa-solid fa-star" style="color:#EEBE15;font-size:14px;"></i>
                <div class="rbar-track"><div class="rbar-fill" style="width:{{ $pct }}%;"></div></div>
                <span style="font-size:12px;color:rgba(21,40,31,.4);width:24px;text-align:right;">{{ $cnt }}</span>
              </div>
              @endforeach
            </div>
          </div>
        </div>

        {{-- Review List --}}
        @forelse($reviews as $review)
        <div class="prof-card" style="margin-bottom:16px;">
          <div class="review-card">
            <div class="review-avatar" style="background:#{{ substr(md5($review->student->email),0,6) }};">
              {{ strtoupper(substr($review->student->name,0,1)) }}
            </div>
            <div style="flex:1;">
              <div class="review-header">
                <div>
                  <span class="review-name">{{ $review->student->name }}</span>
                  <span class="review-date">{{ $review->created_at->format('M Y') }}</span>
                </div>
                <div style="display:flex;gap:2px;">
                  @for($i=1;$i<=5;$i++)<i class="fa-solid fa-star" style="color:{{ $i<=$review->rating ? '#EEBE15' : '#E5E7EB' }};font-size:13px;"></i>@endfor
                </div>
              </div>
              @if($review->comment)
              <p class="review-comment">{{ $review->comment }}</p>
              @endif
            </div>
          </div>
        </div>
        @empty
        <div class="prof-card" style="text-align:center;padding:48px 28px;">
          <i class="fa-solid fa-star" style="font-size:40px;color:rgba(21,40,31,.2);margin-bottom:12px;display:block;"></i>
          <p style="color:rgba(21,40,31,.55);font-size:15px;margin:0;">No reviews yet. Book a session and be the first!</p>
        </div>
        @endforelse
        @if($reviews->hasPages())
        <div style="margin-top:16px;">{{ $reviews->links() }}</div>
        @endif
      </div>

      {{-- BOOK SESSION TAB --}}
      <div id="tab-book" style="display:none;" x-data="{
      step: 1,
      dur: 20,
      date: '',
      time: '',
      subject: '',
      notes: '',
      notesTouched: false,
      phone: '',
      mpesaCode: '',
      pendingVerification: false,
      pendingBookingUuid: '',
      processing: false,
      stkSent: false,
      stkMsg: '',
      confirmed: false,
      studentCode: '',
      bookingRef: '',
      slots: [],
      loadingSlots: false,
      prices: {{ json_encode($prices) }},
      get price() {
        const base = this.prices[this.dur] || 300;
        if (this.dur === 20 && {{ $isFreeFirstSession ? 'true' : 'false' }}) return 0;
        return base;
      },
      get isFree() { return this.price === 0; },
      get sessionName() {
        const names = {{ json_encode($sessNames) }};
        return names[this.dur] || 'Quick Help';
      },
      async fetchSlots() {
        if (!this.date) { this.slots = []; return; }
        this.loadingSlots = true;
        this.slots = [];
        this.time = '';
        try {
          const resp = await fetch('/api/tutors/{{ $tutor->id }}/availability?date=' + this.date, {
            headers: { 'Accept': 'application/json' }
          });
          const data = await resp.json();
          this.slots = data.slots || [];
        } catch(e) {
          console.error('Failed to fetch slots:', e);
        } finally {
          this.loadingSlots = false;
        }
      }
      }">

        {{-- Progress Bar --}}
        <div class="booking-progress">
          @foreach(['Choose Session','Review & Pay','Confirmed'] as $i => $label)
          <div class="booking-step">
            @if($i > 0)
            <div class="booking-step-connector" :style="step > {{ $i }} ? 'background:#0A64BC' : 'background:rgba(21,40,31,.1)'"></div>
            @endif
            <div style="display:flex;flex-direction:column;align-items:center;gap:6px;">
              <div class="booking-step-dot" :style="step > {{ $i+1 }} ? 'background:#10B981;color:#fff' : step === {{ $i+1 }} ? 'background:#0A64BC;color:#fff' : 'background:rgba(21,40,31,.08);color:rgba(21,40,31,.4)'">
                <template x-if="step > {{ $i+1 }}"><i class="fa-solid fa-circle-check"></i></template>
                <template x-if="step <= {{ $i+1 }}"><span>{{ $i+1 }}</span></template>
              </div>
              <span class="booking-step-label" :style="step === {{ $i+1 }} ? 'color:#0A64BC' : 'color:rgba(21,40,31,.4)'">{{ $label }}</span>
            </div>
          </div>
          @endforeach
        </div>

        {{-- STEP 1: Choose Session --}}
        <div x-show="step === 1">
          <div class="prof-card" style="margin-bottom:20px;">
            <h3 class="section-title">
              <span class="section-icon" style="color:#0A64BC;"><i class="fa-solid fa-clock"></i></span>
              1. Choose Session Type
            </h3>
            <div style="display:grid;grid-template-columns:1fr;gap:12px;" class="tier-grid">
              @foreach([20=>['fa-solid fa-zap', 'Quick Help','#0A64BC'],40=>['fa-solid fa-chalkboard', 'Guided Practice','#10B981'],60=>['fa-solid fa-book-open','Deep Dive','#E8522A']] as $mins => [$icon,$tname,$color])
              @php $actualPrice = ($mins===20 && $isFreeFirstSession) ? 0 : $prices[$mins]; @endphp
              <button @click="dur={{ $mins }}"
              :class="dur==={{ $mins }} ? 'tier-card selected' : 'tier-card'"
              style="position:relative;">
                @if($mins===20 && $isFreeFirstSession)
                <span style="position:absolute;top:-10px;right:10px;background:#10B981;color:#fff;font-size:10px;font-weight:800;padding:4px 12px;border-radius:999px;letter-spacing:.05em;">FREE FIRST SESSION</span>
                @endif
                <div class="tier-card-header">
                  <div style="display:flex;align-items:center;gap:10px;">
                    <div class="tier-card-icon" style="background:{{ $color }};">
                      <i class="{{ $icon }}"></i>
                    </div>
                    <div>
                      <div class="tier-card-name">{{ $tname }}</div>
                      <div class="tier-card-duration">{{ $mins }} min</div>
                    </div>
                  </div>
                  <div class="tier-card-price" :style="dur==={{ $mins }} ? 'color:#0A64BC;' : 'color:#15281F;'">
                    @if($actualPrice === 0)
                    <span style="font-size:20px;color:#10B981;">FREE</span>
                    <div style="font-size:11px;text-decoration:line-through;color:rgba(21,40,31,.35);">KSh {{ $prices[$mins] }}</div>
                    @else
                    <span style="font-size:18px;">KSh {{ $actualPrice }}</span>
                    @endif
                  </div>
                </div>
                <div class="tier-card-description">{{ $sessDesc[$mins] }}</div>
              </button>
              @endforeach
            </div>
          </div>

          <div class="prof-card" style="margin-bottom:20px;">
            <h3 class="section-title">
              <span class="section-icon" style="color:#10B981;"><i class="fa-solid fa-calendar"></i></span>
              2. Pick Date & Time
            </h3>
            <div style="margin-bottom:18px;">
              <label class="field-label">Date</label>
              <input type="date" x-model="date" @change="fetchSlots()" :min="new Date().toISOString().split('T')[0]"
              class="book-input">
            </div>

            {{-- Time Slots --}}
            <div style="margin-bottom:14px;">
              <label class="field-label">Available Time Slots (Nairobi, Kenya EAT UTC+3)</label>

              {{-- Loading state --}}
              <template x-if="loadingSlots">
              <div style="display:flex;align-items:center;gap:8px;padding:20px;color:rgba(21,40,31,.5);font-size:14px;">
                <i class="fa-solid fa-hourglass-half"></i> Checking availability...
              </div>
              </template>

              {{-- Slots available --}}
              <template x-if="!loadingSlots && slots.length > 0">
              <div class="time-slot-grid">
                <template x-for="slot in slots" :key="slot.time">
                <button @click="time = slot.time"
                :class="time === slot.time ? 'time-slot-btn selected' : 'time-slot-btn'"
                x-text="slot.label"
                onmouseover="if(!this.classList.contains('selected')) this.style.borderColor='#0A64BC'"
                onmouseout="if(!this.classList.contains('selected')) this.style.borderColor='rgba(21,40,31,.12)'">
                </button>
                </template>
              </div>
              </template>

              {{-- No slots --}}
              <template x-if="!loadingSlots && date && slots.length === 0">
              <div style="padding:20px;text-align:center;background:#F9FAF9;border-radius:12px;border:1px solid rgba(21,40,31,.08);">
                <i class="fa-solid fa-circle-xmark"></i>
                <p style="font-size:13px;color:rgba(21,40,31,.5);">No available slots for this date. Try another day.</p>
              </div>
              </template>

              {{-- No date selected --}}
              <template x-if="!loadingSlots && !date">
              <div style="padding:20px;text-align:center;background:#F9FAF9;border-radius:12px;border:1px solid rgba(21,40,31,.08);">
                <i class="fa-solid fa-calendar"></i>
                <p style="font-size:13px;color:rgba(21,40,31,.5);">Select a date to see available time slots.</p>
              </div>
              </template>
            </div>

            @if($tutor->subjects->isNotEmpty())
            <div style="margin-bottom:14px;">
              <label class="field-label">Subject (optional)</label>
              <select x-model="subject" class="book-select">
                <option value="">Select a subject</option>
                @foreach($tutor->subjects as $s)
                <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endforeach
              </select>
            </div>
            @endif

            <div>
              <label class="field-label">Notes for tutor <span style="color:#E8522A;">*</span></label>
              <textarea x-model="notes" rows="3" placeholder="Topics to cover, past exam areas, specific questions"
              required class="book-textarea"></textarea>
              <p x-show="notesTouched && !notes.trim()" style="font-size:12px;color:#E8522A;margin-top:4px;">Please describe what you need help with.</p>
            </div>
          </div>

          <button @click="notes.trim() ? (step=2) : (notesTouched=true)" :disabled="!date || !time || !notes.trim()"
          style="width:100%;height:88px;background:linear-gradient(135deg,#10B981 0%,#059669 100%);color:#fff;font-size:20px;font-weight:800;border:3px solid #10B981;cursor:pointer;font-family:inherit;transition:all .3s;display:flex;align-items:center;justify-content:center;gap:14px;border-radius:16px;box-shadow:0 10px 36px rgba(16,185,129,.4);margin-bottom:16px;"
          :style="(!date||!time||!notes.trim())?'opacity:.3;cursor:not-allowed;box-shadow:none;background:linear-gradient(135deg,#999 0%,#666 100%);border-color:#999':''"
          @mouseover="!(!date||!time||!notes.trim()) && (this.style.transform='translateY(-4px)',this.style.boxShadow='0 16px 48px rgba(16,185,129,.5)')"
          @mouseout="(!date||!time||!notes.trim()) ? (this.style.boxShadow='none') : (this.style.transform='translateY(0)',this.style.boxShadow='0 10px 36px rgba(16,185,129,.4)')">
            <i class="fa-solid fa-arrow-right" style="font-size:24px;"></i> Continue to Review
          </button>
          <p style="text-align:center;font-size:13px;color:rgba(21,40,31,.6);margin:16px 0 0;font-weight:500;display:flex;align-items:center;justify-content:center;gap:6px;">
            <i class="fa-solid fa-shield-halved" style="color:#10B981;"></i> 100% money-back if unsatisfied with your first lesson
          </p>
        </div>

        {{-- STEP 2: Review & Pay --}}
        <div x-show="step === 2" x-cloak>
          <div class="prof-card" style="margin-bottom:20px;">
            <h3 class="section-title">
              <span class="section-icon" style="color:#E8522A;"><i class="fa-solid fa-receipt"></i></span>
              Session Summary
            </h3>

            {{-- Summary rows --}}
            <div style="background:#F9FAF9;border-radius:14px;padding:18px;margin-bottom:18px;">
              @foreach([
              ['fa-user','Tutor',$tutor->user->name],
              ['fa-clock','Session','<span x-text="dur+\' min - \'+sessionName"></span>'],
              ['fa-calendar','Date','<span x-text="date ? new Date(date).toDateString() : \'-\'"></span>'],
              ['fa-clock','Time','<span x-text="time || \'-\'"></span> EAT'],
              ] as [$ic,$label,$val])
              <div style="display:flex;align-items:center;justify-content:space-between;padding:9px 0;border-bottom:1px solid rgba(21,40,31,.07);">
                <span style="display:flex;align-items:center;gap:8px;font-size:13px;color:rgba(21,40,31,.55);">
                  <i class="fa-solid {{ $ic }}"></i>{{ $label }}
                </span>
                <span style="font-size:13px;font-weight:600;color:#15281F;">{!! $val !!}</span>
              </div>
              @endforeach
              <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 0 0;">
                <span style="font-size:15px;font-weight:700;color:#15281F;">Total</span>
                <template x-if="isFree">
                <div style="text-align:right;">
                  <span style="font-family:'Fraunces',serif;font-size:26px;font-weight:800;color:#10B981;">FREE</span>
                  <div style="font-size:12px;text-decoration:line-through;color:rgba(21,40,31,.35);">KSh 300</div>
                </div>
                </template>
                <template x-if="!isFree">
                <span style="font-family:'Fraunces',serif;font-size:26px;font-weight:800;color:#0A64BC;">KSh <span x-text="price"></span></span>
                </template>
              </div>
            </div>

            {{-- Code notice --}}
            <div style="background:rgba(10,100,188,.05);border:1px solid rgba(10,100,188,.15);border-radius:14px;padding:14px 16px;margin-bottom:18px;">
              <p style="font-size:13px;font-weight:700;color:#0A64BC;margin-bottom:4px;display:flex;align-items:center;gap:6px;">
                <i class="fa-solid fa-key"></i> After payment
              </p>
              <p style="font-size:12px;color:rgba(21,40,31,.6);line-height:1.65;">Both you and {{ explode(' ',$tutor->user->name)[0] }} will receive a unique session access code by email. You'll use your <strong style="color:#15281F;">STU-XXXXXX</strong> code to join the live class.</p>
            </div>

            {{-- Payment section --}}
            @auth
            <div style="background:#F9FAF9;border-radius:14px;padding:18px;">
              <p style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:rgba(21,40,31,.45);margin-bottom:12px;">
                <i class="fa-solid fa-mobile-screen"></i> Pay Now
              </p>

              {{-- M-Pesa Paybill Instructions --}}
              <div x-show="!isFree" style="background:#F0FDF4;border:1.5px solid #10B981;border-radius:14px;padding:18px;margin-bottom:16px;">
                <div style="font-weight:700;font-size:13px;color:#065F46;margin-bottom:12px;">
                  <i class="fa-solid fa-mobile-screen-button"></i> Pay via M-Pesa Paybill
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px;">
                  <div style="background:#fff;border-radius:10px;padding:12px;text-align:center;border:1px solid rgba(16,185,129,.3);">
                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:rgba(21,40,31,.45);margin-bottom:4px;">Paybill Number</div>
                    <div style="font-family:'Fraunces',serif;font-size:26px;font-weight:800;color:#15281F;">542542</div>
                  </div>
                  <div style="background:#fff;border-radius:10px;padding:12px;text-align:center;border:1px solid rgba(16,185,129,.3);">
                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:rgba(21,40,31,.45);margin-bottom:4px;">Account Number</div>
                    <div style="font-family:'Fraunces',serif;font-size:26px;font-weight:800;color:#0A64BC;">313126</div>
                  </div>
                </div>
                <div style="font-size:12px;color:rgba(21,40,31,.55);text-align:center;margin-bottom:10px;">
                  Account Name: <strong style="color:#15281F;">OBARE OKOTH JACOB</strong>
                </div>
                <div style="background:rgba(16,185,129,.08);border-radius:8px;padding:10px 14px;font-size:12px;color:#065F46;line-height:1.6;">
                  <strong>Steps:</strong> M-Pesa  Lipa na M-Pesa  Pay Bill  Paybill: <strong>542542</strong>  Account: <strong>313126</strong>  Amount: KSh <span x-text="price"></span>
                </div>
              </div>

              {{-- Transaction Code Input --}}
              <div style="margin-bottom:14px;" x-show="!isFree">
                <label class="field-label">M-Pesa Transaction Code <span style="color:#E8522A;">*</span></label>
                <input type="text" x-model="mpesaCode"
                placeholder="e.g. RKA7X1Y2Z3"
                class="book-input"
                style="font-family:monospace;letter-spacing:.08em;text-transform:uppercase;"
                oninput="this.value=this.value.toUpperCase()" maxlength="20">
                <p style="font-size:11px;color:rgba(21,40,31,.5);margin-top:4px;">Enter the code from your M-Pesa confirmation SMS (e.g. RKA7X1Y2Z3)</p>
              </div>

              <button @click="processPayment($data)"
              :disabled="processing"
              :style="isFree ? 'background:linear-gradient(135deg,#10B981 0%,#059669 100%);border-color:#10B981;box-shadow:0 10px 36px rgba(16,185,129,.4);' : 'background:linear-gradient(135deg,#0A64BC 0%,#0052A3 100%);border-color:#0A64BC;box-shadow:0 10px 36px rgba(10,100,188,.4);'"
              style="width:100%;height:88px;border-radius:16px;color:#fff;font-size:22px;font-weight:800;border:3px solid;cursor:pointer;font-family:inherit;transition:all .3s;display:flex;align-items:center;justify-content:center;gap:12px;margin-bottom:16px;"
              @mouseover="this.style.transform='translateY(-4px)';this.style.boxShadow=(isFree ? '0 16px 48px rgba(16,185,129,.5)' : '0 16px 48px rgba(10,100,188,.5)')"
              @mouseout="this.style.transform='translateY(0)';this.style.boxShadow=(isFree ? '0 10px 36px rgba(16,185,129,.4)' : '0 10px 36px rgba(10,100,188,.4)')">
                <template x-if="!processing">
                <span style="display:flex;align-items:center;gap:12px;">
                  <i :class="isFree ? 'fa-solid fa-gift' : 'fa-solid fa-paper-plane'" style="font-size:26px;"></i>
                  <span x-text="isFree ? 'Book Free Session' : 'Submit Payment'" style="font-size:inherit;"></span>
                </span>
                </template>
                <template x-if="processing">
                <span style="display:flex;align-items:center;gap:12px;">
                  <i class="fa-solid fa-hourglass-half" style="font-size:26px;animation:spin 1s linear infinite;"></i> <span style="font-size:inherit;">Processing Payment</span>
                </span>
                </template>
              </button>
            </div>
            @else
            <div style="text-align:center;padding:20px;">
              <p style="font-size:14px;color:rgba(21,40,31,.6);margin-bottom:14px;">Sign in to complete your booking</p>
              <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap;">
                <a href="{{ route('login') }}" style="display:inline-flex;align-items:center;gap:7px;padding:12px 24px;border-radius:999px;background:#0A64BC;color:#fff;font-size:14px;font-weight:600;text-decoration:none;">
                  <i class="fa-solid fa-right-to-bracket"></i> Sign In
                </a>
                <a href="{{ route('register') }}" style="display:inline-flex;align-items:center;gap:7px;padding:12px 24px;border-radius:999px;border:2px solid rgba(21,40,31,.2);color:#15281F;font-size:14px;font-weight:600;text-decoration:none;">
                  <i class="fa-solid fa-user"></i> Create Account
                </a>
              </div>
            </div>
            @endauth

            <div style="display:flex;justify-content:flex-start;margin-top:16px;">
              <button @click="step=1" style="display:flex;align-items:center;gap:6px;background:none;border:none;color:rgba(21,40,31,.55);font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;padding:8px 12px;transition:all .2s;border-radius:8px;" onmouseover="this.style.color='#15281F';this.style.background='rgba(21,40,31,.05)'" onmouseout="this.style.color='rgba(21,40,31,.55)';this.style.background='none'">
                <i class="fa-solid fa-arrow-left"></i> Back
              </button>
            </div>
          </div>
        </div>

        {{-- STEP 3: Confirmed or Pending --}}
        <div x-show="step === 3" x-cloak>
          <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:40px;text-align:center;">

            {{-- PENDING VERIFICATION STATE --}}
            <div x-show="pendingVerification">
              <div style="font-size:52px;margin-bottom:16px;"><i class="fa-solid fa-hourglass-half"></i></div>
              <h2 style="font-family:'Fraunces',serif;font-size:24px;color:#15281F;margin-bottom:8px;">Booking Pending Confirmation</h2>
              <p style="font-size:14px;color:rgba(21,40,31,.6);line-height:1.7;margin-bottom:20px;">
                Your payment is being verified by our team.<br>
                This usually takes <strong>under 5 minutes</strong>.<br>
                You'll receive your session codes by email once confirmed.
              </p>
              <div style="background:#FEF3C7;border-radius:12px;padding:14px 16px;font-size:13px;color:#92400E;margin-bottom:20px;text-align:left;">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <strong>Keep your M-Pesa SMS</strong>  your transaction code may be requested during verification.
              </div>
              <a :href="'{{ url('/bookings/') }}/' + pendingBookingUuid" x-show="pendingBookingUuid"
              style="display:inline-flex;align-items:center;gap:8px;padding:13px 28px;background:#0A64BC;color:#fff;border-radius:999px;font-weight:700;font-size:14px;text-decoration:none;">
                <i class="fa-solid fa-eye"></i> View Booking Status
              </a>
            </div>

            {{-- CONFIRMED STATE --}}
            <div x-show="!pendingVerification">
              <div style="width:68px;height:68px;border-radius:50%;background:rgba(16,185,129,.12);display:flex;align-items:center;justify-content:center;margin:0 auto 18px;font-size:32px;color:#10B981;">
                <i class="fa-solid fa-circle-check"></i>
              </div>
              <h2 style="font-family:'Fraunces',serif;font-size:26px;color:#15281F;margin-bottom:8px;">Booking Confirmed!</h2>
              <p style="font-size:14px;color:rgba(21,40,31,.6);margin-bottom:28px;">Payment received. Your session codes have been emailed to you. Redirecting to your session in 3 seconds</p>

              {{-- Session code --}}
              <div style="background:rgba(10,100,188,.06);border:2px solid rgba(10,100,188,.25);border-radius:20px;padding:24px;margin-bottom:24px;">
                <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:rgba(21,40,31,.45);margin-bottom:10px;display:flex;align-items:center;justify-content:center;gap:6px;">
                  <i class="fa-solid fa-key"></i> Your Session Access Code
                </p>
                <p style="font-family:monospace;font-size:36px;font-weight:800;color:#0A64BC;letter-spacing:.12em;line-height:1;" x-text="studentCode">STU-XXXXXX</p>
                <p style="font-size:12px;color:rgba(21,40,31,.5);margin-top:10px;">Enter this code on session day to join your private live class</p>
              </div>

              {{-- What happens next --}}
              <div style="background:#F9FAF9;border-radius:14px;padding:16px;text-align:left;margin-bottom:24px;">
                <p style="font-size:12px;font-weight:700;color:#15281F;margin-bottom:10px;"><i class="fa-solid fa-clipboard"></i> What happens next</p>
                @foreach(['Your tutor has been notified and received their own TUT-XXXXXX code','A confirmation email with all details has been sent to you','On session day, go to your dashboard  click Join Session  enter your code','Your private classroom opens instantly in your browser - no download required'] as $step)
                <div style="display:flex;align-items:flex-start;gap:8px;font-size:13px;color:rgba(21,40,31,.7);margin-bottom:7px;">
                  <i class="fa-solid fa-circle-check"></i>{{ $step }}
                </div>
                @endforeach
              </div>

              <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
                <a :href="bookingRef ? '/bookings/' + bookingRef : '{{ route('dashboard') }}'"
                style="display:inline-flex;align-items:center;gap:7px;padding:13px 28px;border-radius:999px;background:#10B981;color:#fff;font-size:15px;font-weight:700;text-decoration:none;">
                  <i class="fa-solid fa-video"></i> Go to My Session
                </a>
                <a href="{{ route('dashboard') }}" style="display:inline-flex;align-items:center;gap:7px;padding:13px 24px;border-radius:999px;border:1.5px solid rgba(21,40,31,.18);color:#15281F;font-size:14px;font-weight:600;text-decoration:none;">
                  <i class="fa-solid fa-chart-bar"></i> Dashboard
                </a>
                <a href="{{ route('tutors.index') }}" style="display:inline-flex;align-items:center;gap:7px;padding:13px 24px;border-radius:999px;border:1.5px solid rgba(21,40,31,.18);color:#15281F;font-size:14px;font-weight:600;text-decoration:none;">
                  <i class="fa-solid fa-magnifying-glass"></i> Browse More Tutors
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- RIGHT COLUMN: Sticky Sidebar (Desktop Only) --}}
    <div class="sidebar-sticky">
      {{-- Sidebar Stats Card --}}
      <div class="sidebar-stats-card">
        <div class="sidebar-stat-row">
          <div class="sidebar-stat-icon" style="background:#0A64BC;">
            <i class="fa-solid fa-chalkboard"></i>
          </div>
          <div class="sidebar-stat-text">
            <div class="sidebar-stat-value">{{ number_format($tutor->total_sessions) }}</div>
            <div class="sidebar-stat-label">Sessions</div>
          </div>
        </div>
        <div class="sidebar-stat-row">
          <div class="sidebar-stat-icon" style="background:#EEBE15;">
            <i class="fa-solid fa-star"></i>
          </div>
          <div class="sidebar-stat-text">
            <div class="sidebar-stat-value">{{ number_format($tutor->rating_avg,1) }}</div>
            <div class="sidebar-stat-label">Rating</div>
          </div>
        </div>
        <div class="sidebar-stat-row">
          <div class="sidebar-stat-icon" style="background:#E8522A;">
            <i class="fa-solid fa-reply"></i>
          </div>
          <div class="sidebar-stat-text">
            <div class="sidebar-stat-value">&lt;2hr</div>
            <div class="sidebar-stat-label">Response Time</div>
          </div>
        </div>
      </div>

      {{-- Book Session CTA Button --}}
      <button class="sidebar-cta-btn" onclick="document.getElementById('book-tab-btn').click();document.getElementById('tab-book').scrollIntoView({behavior:'smooth'})">
        <i class="fa-solid fa-calendar-check" style="font-size:24px;"></i> Book a Session
      </button>

      {{-- Session Types List --}}
      <div class="sidebar-sessions-list">
        <label class="sidebar-sessions-title">Session Types</label>
        @foreach([20=>['fa-solid fa-zap', '#0A64BC'],40=>['fa-solid fa-chalkboard', '#10B981'],60=>['fa-solid fa-book-open', '#E8522A']] as $mins => [$icon, $color])
        <div class="sidebar-session-type">
          <div class="sidebar-session-icon" style="background:{{ $color }};">
            <i class="{{ $icon }}"></i>
          </div>
          <div class="sidebar-session-info">
            <span class="sidebar-session-name">{{ $sessNames[$mins] }}</span>
            <span class="sidebar-session-time">{{ $mins }} minutes</span>
          </div>
          <div class="sidebar-session-price">KSh {{ $prices[$mins] }}</div>
        </div>
        @endforeach
      </div>

      {{-- Trust Signals --}}
      <div style="background:#F0FDF4;border-radius:18px;border:1px solid rgba(16,185,129,.2);padding:18px;text-align:center;">
        <div style="font-size:22px;margin-bottom:8px;">
          <i class="fa-solid fa-shield-halved" style="color:#10B981;"></i>
        </div>
        <p style="font-size:11px;font-weight:700;color:#065F46;margin-bottom:6px;text-transform:uppercase;letter-spacing:.08em;">100% Money-Back</p>
        <p style="font-size:12px;color:#047857;margin:0;">Guarantee if unsatisfied with your first lesson</p>
      </div>
    </div>

  </div>
</div>

@include('partials.footer')
<script src="https://meet.jit.si/external_api.js" defer></script>
<script>
function showTab(name, btn) {
  ['about','availability','reviews','book'].forEach(t => {
    const el = document.getElementById('tab-'+t);
    if (el) el.style.display = t === name ? 'block' : 'none';
  });
  document.querySelectorAll('.ptab').forEach(b => {
    b.classList.remove('on');
    b.style.color = 'rgba(255,255,255,.45)';
    b.style.borderBottomColor = 'transparent';
  });
  if (btn) {
    btn.classList.add('on');
    btn.style.color = name === 'book' ? '#EEBE15' : '#fff';
    btn.style.borderBottomColor = name === 'book' ? '#EEBE15' : '#0A64BC';
  }
}

function showError(title, message) {
  const existing = document.getElementById('pay-error-toast');
  if (existing) existing.remove();

  const toast = document.createElement('div');
  toast.id = 'pay-error-toast';
  toast.style.cssText = 'position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:9999;background:#FEF2F2;border:1.5px solid #DC2626;border-radius:12px;padding:16px 24px;max-width:400px;width:90%;box-shadow:0 8px 32px rgba(0,0,0,.15);display:flex;align-items:flex-start;gap:12px;';
  toast.innerHTML = '<i class="fa-solid fa-circle-exclamation" style="color:#DC2626;font-size:20px;flex-shrink:0;margin-top:2px;"></i><div><div style="font-weight:700;color:#991B1B;font-size:14px;margin-bottom:4px;">' + title + '</div><div style="font-size:13px;color:#7F1D1D;">' + message + '</div></div>';
  document.body.appendChild(toast);
  setTimeout(function() { toast.remove(); }, 5000);
}

function processPayment(data) {
  if (!data || !data.date || !data.time) {
    showError('Missing Information', 'Please select a date and time.');
    return;
  }
  if (!data.isFree && (!data.mpesaCode || data.mpesaCode.trim().length < 6)) {
    showError('Transaction Code Required', 'Please enter the M-Pesa transaction code from your confirmation SMS.');
    return;
  }

  data.processing = true;

  fetch('/bookings', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
      'Accept': 'application/json',
    },
    body: JSON.stringify({
      tutor_profile_id: {{ $tutor->id }},
      duration_minutes: data.dur,
      scheduled_at: data.date + ' ' + data.time,
      subject_id: data.subject || null,
      notes: data.notes,
      mpesa_code: data.mpesaCode ? data.mpesaCode.trim().toUpperCase() : '',
    })
  })
  .then(r => r.json())
  .then(res => {
    data.processing = false;
    if (res.status === 'ok' && res.free) {
      data.studentCode = res.student_code || '';
      data.bookingRef = res.booking_uuid || '';
      data.step = 3;
      if (res.booking_url) {
        setTimeout(() => { window.location.href = res.booking_url; }, 3000);
      }
      return;
    }
    if (res.status === 'ok' && res.mode === 'manual') {
      window.location.href = res.pending_url || '/dashboard';
      return;
    }
    showError('Booking Failed', res.message || 'Could not process booking. Please try again.');
  })
  .catch(() => {
    data.processing = false;
    showError('Network Error', 'Please check your connection and try again.');
  });
}
</script>
@livewireScripts
</body>
</html>
