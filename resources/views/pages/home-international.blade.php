<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')

<meta charset="utf-8">
 <link rel="icon" type="image/svg+xml" href="/favicon.svg">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Mwalimu - Expert Tutors for International Curriculum</title>
<meta name="description" content="Expert tutors for Cambridge, IB, Edexcel, and American curricula. Find qualified tutors for international exams and standards. 1-on-1 sessions with verified educators.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700;9..144,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">
<script src="{{ asset('js/curriculum-switcher.js') }}" defer></script>
@livewireStyles
<style>
:root{--navy:#1A2D3D;--amber:#F59E0B;--white:#FFFFFF;--cambridge:#1D4F8C;--ib:#003D7A;--edexcel:#F59E0B;--american:#1E40AF;--muted:rgba(26,45,61,.6);--light:rgba(26,45,61,.1);}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{background:var(--white);scroll-behavior:smooth;}
body{font-family:'DM Sans',sans-serif;color:var(--navy);background:var(--white);line-height:1.6;}
h1,h2,h3,h4{font-family:'Fraunces',Georgia,serif;line-height:1.1;}
a{color:inherit;text-decoration:none;}[x-cloak]{display:none!important;}
.w{max-width:1360px;margin:0 auto;padding:0 24px;}
@media(min-width:1024px){.w{padding:0 48px;}}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:13px 28px;border-radius:999px;font-weight:600;font-size:15px;cursor:pointer;transition:all .18s;border:none;font-family:inherit;text-decoration:none;}
.btn-primary{background:var(--navy);color:#fff;}.btn-primary:hover{background:#0f1e2d;transform:translateY(-1px);box-shadow:0 6px 20px rgba(26,45,61,.3);}
.btn-secondary{background:var(--amber);color:var(--navy);font-weight:700;}.btn-secondary:hover{opacity:.9;transform:translateY(-1px);}
.btn-white{background:#fff;color:var(--navy);border:2px solid var(--navy);}.btn-white:hover{background:var(--navy);color:#fff;}
.card{background:#fff;border-radius:22px;border:1px solid var(--light);box-shadow:0 2px 16px rgba(26,45,61,.06);}
.card-lift{transition:transform .2s,box-shadow .2s;}.card-lift:hover{transform:translateY(-5px);box-shadow:0 14px 36px rgba(26,45,61,.12);}
.badge{display:inline-flex;align-items:center;gap:5px;padding:5px 13px;border-radius:999px;font-size:12px;font-weight:600;}
.badge-navy{background:rgba(26,45,61,.1);color:var(--navy);}
</style>
</head>
<body>
@php
use App\Models\Category;
use App\Models\TutorProfile;
use App\Models\ExaminerClinic;
@endphp

@include('partials.nav')

{{-- HERO --}}
<section style="padding:100px 0 80px;background:#fff;position:relative;overflow:hidden;">
 {{-- Decorative elements --}}
 <div style="position:absolute;top:-100px;right:-100px;width:500px;height:500px;background:linear-gradient(135deg,rgba(26,45,61,.06) 0%,rgba(245,158,15,.03) 100%);border-radius:50%;pointer-events:none;"></div>
 <div style="position:absolute;bottom:-80px;left:-80px;width:400px;height:400px;background:linear-gradient(135deg,rgba(245,158,15,.04) 0%,rgba(26,45,61,.02) 100%);border-radius:50%;pointer-events:none;"></div>
 
 <div class="w">
 <div style="display:grid;grid-template-columns:1fr;gap:60px;align-items:center;max-width:1200px;position:relative;z-index:1;" class="hero-intl">
 
 {{-- Left: Headline + Tagline --}}
 <div>
 <div class="badge badge-navy" style="margin-bottom:24px;font-size:13px;background:linear-gradient(135deg,rgba(26,45,61,.12) 0%,rgba(245,158,15,.08) 100%);letter-spacing:.05em;">
 <i class="fa-solid fa-globe"></i> INTERNATIONAL CURRICULUM
 </div>
 <h1 style="font-size:clamp(40px,6.5vw,76px);color:var(--navy);margin-bottom:24px;letter-spacing:-.015em;font-family:'Fraunces',Georgia,serif;font-weight:800;line-height:1.1;">
 Expert tutors for every international exam.
 </h1>
 <p style="font-size:18px;color:var(--muted);margin-bottom:40px;max-width:560px;line-height:1.8;font-weight:400;">
 Mwalimu connects you with vetted educators specializing in Cambridge, IB, Edexcel, and American curricula. Expert guidance. Premium instruction. Results.
 </p>
 <div style="display:flex;flex-wrap:wrap;gap:14px;">
 <a href="{{ route('tutors.index') }}?curriculum=international" class="btn btn-primary" style="font-size:16px;padding:15px 36px;box-shadow:0 8px 24px rgba(26,45,61,.2);transition:all .3s;">
 <i class="fa-solid fa-magnifying-glass"></i> Find an international tutor
 </a>
 <a href="#exam-boards" class="btn btn-white" style="font-size:16px;padding:15px 36px;border-width:2px;transition:all .3s;">
 <i class="fa-solid fa-graduation-cap"></i> Explore exam boards
 </a>
 </div>
 </div>

 {{-- Right: Illustration + Find Your Tutor Panel --}}
 <div style="position:relative;display:flex;flex-direction:column;gap:20px;">
 {{-- SVG Illustration --}}
 <svg width="100%" height="280" viewBox="0 0 400 280" style="max-width:500px;margin-bottom:0px;filter:drop-shadow(0 8px 24px rgba(26,45,61,.1));">
 <defs>
 <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
 <stop offset="0%" style="stop-color:#1D4F8C;stop-opacity:1" />
 <stop offset="100%" style="stop-color:#0d3a71;stop-opacity:1" />
 </linearGradient>
 <linearGradient id="grad2" x1="0%" y1="0%" x2="100%" y2="100%">
 <stop offset="0%" style="stop-color:#F59E0B;stop-opacity:1" />
 <stop offset="100%" style="stop-color:#e08500;stop-opacity:1" />
 </linearGradient>
 <style>
 @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-12px); } }
 @keyframes rotate { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
 .book1 { animation: float 4s ease-in-out infinite; }
 .book2 { animation: float 5s ease-in-out infinite 0.2s; }
 .book3 { animation: float 4.5s ease-in-out infinite 0.4s; }
 .cap { animation: float 5.5s ease-in-out infinite 0.6s; }
 .globe { animation: rotate 8s linear infinite; }
 </style>
 </defs>
 {{-- Book 1 --}}
 <g class="book1">
 <rect x="20" y="120" width="50" height="140" rx="8" fill="url(#grad1)" opacity="0.9"/>
 <rect x="25" y="125" width="40" height="8" fill="#fff" opacity="0.6" />
 <rect x="25" y="140" width="40" height="6" fill="#fff" opacity="0.4" />
 <rect x="25" y="155" width="40" height="6" fill="#fff" opacity="0.4" />
 </g>
 {{-- Book 2 --}}
 <g class="book2">
 <rect x="90" y="140" width="55" height="120" rx="8" fill="url(#grad2)" opacity="0.85"/>
 <rect x="95" y="145" width="45" height="8" fill="#fff" opacity="0.7" />
 <rect x="95" y="160" width="45" height="6" fill="#fff" opacity="0.5" />
 </g>
 {{-- Book 3 --}}
 <g class="book3">
 <rect x="165" y="100" width="48" height="160" rx="8" fill="url(#grad1)" opacity="0.8"/>
 <rect x="170" y="105" width="38" height="8" fill="#fff" opacity="0.6" />
 <rect x="170" y="120" width="38" height="6" fill="#fff" opacity="0.4" />
 </g>
 {{-- Graduation cap --}}
 <g class="cap">
 <circle cx="280" cy="100" r="45" fill="url(#grad2)" opacity="0.9"/>
 <path d="M 235 100 L 280 130 L 325 100" fill="url(#grad1)" opacity="0.8" />
 <line x1="280" y1="130" x2="280" y2="160" stroke="url(#grad1)" stroke-width="4" opacity="0.7"/>
 </g>
 {{-- Globe --}}
 <g class="globe">
 <circle cx="350" cy="140" r="50" fill="url(#grad2)" opacity="0.75"/>
 <circle cx="350" cy="140" r="50" fill="none" stroke="#fff" stroke-width="2" opacity="0.3"/>
 <path d="M 320 120 Q 350 110 380 120" fill="none" stroke="#fff" stroke-width="2" opacity="0.5"/>
 <path d="M 310 140 Q 350 145 390 140" fill="none" stroke="#fff" stroke-width="2" opacity="0.5"/>
 </g>
 </svg>

 {{-- Find Your Tutor Panel --}}
 <div style="background:linear-gradient(135deg,#fff 0%,rgba(245,158,15,.04) 100%);border-radius:24px;border:1px solid rgba(26,45,61,.08);padding:40px;box-shadow:0 20px 60px rgba(26,45,61,.1);backdrop-filter:blur(10px);">
 <p style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--navy);margin-bottom:24px;opacity:.6;"><i class="fa-solid fa-sparkles"></i> FIND YOUR TUTOR</p>
 <form action="{{ route('tutors.index') }}" method="GET" style="display:flex;flex-direction:column;gap:16px;">
 <div>
 <label style="display:block;font-size:13px;font-weight:700;color:var(--navy);margin-bottom:8px;text-transform:uppercase;letter-spacing:.05em;opacity:.8;">Curriculum</label>
 <select name="curriculum" style="width:100%;height:46px;padding:0 14px;border-radius:12px;border:1.5px solid var(--light);background:#fff;font-size:14px;font-weight:500;font-family:inherit;outline:none;color:var(--navy);transition:all .2s;cursor:pointer;" onfocus="this.style.borderColor='var(--navy)';this.style.boxShadow='0 0 0 3px rgba(26,45,61,.1)'" onblur="this.style.borderColor='var(--light)';this.style.boxShadow=''" required>
 <option value="international" selected>International (IGCSE, IB, Edexcel, AP)</option>
 </select>
 </div>
 <div>
 <label style="display:block;font-size:13px;font-weight:700;color:var(--navy);margin-bottom:8px;text-transform:uppercase;letter-spacing:.05em;opacity:.8;">Level</label>
 <select name="level" style="width:100%;height:46px;padding:0 14px;border-radius:12px;border:1.5px solid var(--light);background:#fff;font-size:14px;font-weight:500;font-family:inherit;outline:none;color:var(--navy);transition:all .2s;cursor:pointer;" onfocus="this.style.borderColor='var(--navy)';this.style.boxShadow='0 0 0 3px rgba(26,45,61,.1)'" onblur="this.style.borderColor='var(--light)';this.style.boxShadow=''">
 <option value="">All levels</option>
 <option value="primary">Primary (K-6)</option>
 <option value="lower">Lower Secondary (Grades 7-9)</option>
 <option value="upper">Upper Secondary (Grades 10-11)</option>
 <option value="advanced">Advanced (Grades 12+)</option>
 </select>
 </div>
 <div>
 <label style="display:block;font-size:13px;font-weight:700;color:var(--navy);margin-bottom:8px;text-transform:uppercase;letter-spacing:.05em;opacity:.8;">Subject</label>
 <select name="subject" style="width:100%;height:46px;padding:0 14px;border-radius:12px;border:1.5px solid var(--light);background:#fff;font-size:14px;font-weight:500;font-family:inherit;outline:none;color:var(--navy);transition:all .2s;cursor:pointer;" onfocus="this.style.borderColor='var(--navy)';this.style.boxShadow='0 0 0 3px rgba(26,45,61,.1)'" onblur="this.style.borderColor='var(--light)';this.style.boxShadow=''">
 <option value="">All subjects</option>
 <option value="Mathematics">Mathematics</option>
 <option value="Physics">Physics</option>
 <option value="Chemistry">Chemistry</option>
 <option value="Biology">Biology</option>
 <option value="English">English Language & Literature</option>
 <option value="Business Studies">Business Studies</option>
 <option value="Economics">Economics</option>
 <option value="Computer Science">Computer Science</option>
 <option value="Languages">Foreign Languages</option>
 <option value="History">History</option>
 <option value="Geography">Geography</option>
 <option value="Art">Art & Design</option>
 </select>
 </div>
 <button type="submit" class="btn btn-primary" style="width:100%;margin-top:8px;font-size:15px;padding:14px;font-weight:700;box-shadow:0 8px 20px rgba(26,45,61,.15);transition:all .3s;">
 <i class="fa-solid fa-arrow-right"></i> Find tutors
 </button>
 </form>
 </div>

 </div>
 </div>
</section>

{{-- EXAM BOARDS GRID --}}
<section id="exam-boards" style="padding:100px 0;background:linear-gradient(180deg,rgba(26,45,61,.01) 0%,rgba(245,158,15,.02) 100%);">
 <div class="w">
 <div style="text-align:center;margin-bottom:60px;">
 <p style="font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--amber);margin-bottom:12px;"><i class="fa-solid fa-book"></i> EXAM BOARD SPECIALISTS</p>
 <h2 style="font-size:clamp(32px,5vw,52px);color:var(--navy);margin-bottom:16px;font-family:'Fraunces',Georgia,serif;font-weight:800;line-height:1.1;">Choose your exam board</h2>
 <p style="font-size:16px;color:var(--muted);max-width:700px;margin:0 auto;line-height:1.7;">Expert tutors certified and experienced with each major international curriculum. Select your board for specialized support.</p>
 </div>
 
 <div style="display:grid;grid-template-columns:1fr;gap:24px;max-width:1200px;margin:0 auto;" class="exam-boards-grid">
 {{-- Cambridge --}}
 <div style="background:#fff;border-radius:24px;border:2px solid var(--light);padding:32px;transition:all .3s;box-shadow:0 4px 16px rgba(26,45,61,.06);" 
 onmouseover="this.style.borderColor='var(--cambridge)';this.style.boxShadow='0 16px 48px rgba(29,79,140,.18)';this.style.transform='translateY(-4px)'" 
 onmouseout="this.style.borderColor='var(--light)';this.style.boxShadow='0 4px 16px rgba(26,45,61,.06)';this.style.transform='translateY(0)'">
 <div style="display:flex;align-items:flex-start;gap:16px;margin-bottom:20px;">
 <div style="width:56px;height:56px;background:linear-gradient(135deg,var(--cambridge) 0%,#0d3a71 100%);border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:800;color:#fff;flex-shrink:0;box-shadow:0 8px 20px rgba(29,79,140,.2);">C</div>
 <div style="flex:1;">
 <h3 style="font-size:22px;font-weight:800;color:var(--navy);font-family:'Fraunces',Georgia,serif;margin-bottom:2px;">Cambridge</h3>
 <p style="font-size:13px;color:var(--muted);font-weight:500;">CAIE International Examinations</p>
 </div>
 </div>
 <p style="font-size:15px;color:var(--muted);margin-bottom:16px;line-height:1.7;">Expert preparation for Cambridge IGCSE, O Level, AS and A Level. Our tutors are Cambridge-trained with proven exam track records.</p>
 <div style="display:flex;flex-wrap:wrap;gap:7px;margin-bottom:20px;">
 <span style="background:linear-gradient(135deg,rgba(29,79,140,.12) 0%,rgba(29,79,140,.06) 100%);color:var(--cambridge);padding:6px 14px;border-radius:10px;font-size:12px;font-weight:700;border:1px solid rgba(29,79,140,.2);">Primary</span>
 <span style="background:linear-gradient(135deg,rgba(29,79,140,.12) 0%,rgba(29,79,140,.06) 100%);color:var(--cambridge);padding:6px 14px;border-radius:10px;font-size:12px;font-weight:700;border:1px solid rgba(29,79,140,.2);">Lower Secondary</span>
 <span style="background:linear-gradient(135deg,rgba(29,79,140,.12) 0%,rgba(29,79,140,.06) 100%);color:var(--cambridge);padding:6px 14px;border-radius:10px;font-size:12px;font-weight:700;border:1px solid rgba(29,79,140,.2);">IGCSE</span>
 <span style="background:linear-gradient(135deg,rgba(29,79,140,.12) 0%,rgba(29,79,140,.06) 100%);color:var(--cambridge);padding:6px 14px;border-radius:10px;font-size:12px;font-weight:700;border:1px solid rgba(29,79,140,.2);">AS & A Level</span>
 </div>
 <div style="display:flex;gap:12px;align-items:stretch;">
 <a href="{{ route('tutors.index') }}?exam_board=cambridge" class="btn btn-primary" style="flex:1;font-size:14px;padding:12px 16px;background:linear-gradient(135deg,var(--cambridge) 0%,#0d3a71 100%);border:none;box-shadow:0 6px 16px rgba(29,79,140,.2);">Browse tutors</a>
 <button onclick="alert('Waitlist feature coming soon')" class="btn" style="flex:0 1 auto;padding:12px 16px;border:2px solid var(--cambridge);color:var(--cambridge);background:transparent;font-weight:700;font-size:13px;cursor:pointer;transition:all .2s;">Waitlist</button>
 </div>
 </div>

 {{-- IB --}}
 <div style="background:#fff;border-radius:24px;border:2px solid var(--light);padding:32px;transition:all .3s;box-shadow:0 4px 16px rgba(26,45,61,.06);" 
 onmouseover="this.style.borderColor='var(--ib)';this.style.boxShadow='0 16px 48px rgba(0,61,122,.18)';this.style.transform='translateY(-4px)'" 
 onmouseout="this.style.borderColor='var(--light)';this.style.boxShadow='0 4px 16px rgba(26,45,61,.06)';this.style.transform='translateY(0)'">
 <div style="display:flex;align-items:flex-start;gap:16px;margin-bottom:20px;">
 <div style="width:56px;height:56px;background:linear-gradient(135deg,var(--ib) 0%,#001a4d 100%);border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:800;color:#fff;flex-shrink:0;box-shadow:0 8px 20px rgba(0,61,122,.2);">IB</div>
 <div style="flex:1;">
 <h3 style="font-size:22px;font-weight:800;color:var(--navy);font-family:'Fraunces',Georgia,serif;margin-bottom:2px;">International Baccalaureate</h3>
 <p style="font-size:13px;color:var(--muted);font-weight:500;">IB World Schools & Authorized Centers</p>
 </div>
 </div>
 <p style="font-size:15px;color:var(--muted);margin-bottom:16px;line-height:1.7;">Rigorous support for IB PYP, MYP, and Diploma programmes. We guide you through coursework, internal assessments, and extended essays.</p>
 <div style="display:flex;flex-wrap:wrap;gap:7px;margin-bottom:20px;">
 <span style="background:linear-gradient(135deg,rgba(0,61,122,.12) 0%,rgba(0,61,122,.06) 100%);color:var(--ib);padding:6px 14px;border-radius:10px;font-size:12px;font-weight:700;border:1px solid rgba(0,61,122,.2);">PYP (Ages 3-12)</span>
 <span style="background:linear-gradient(135deg,rgba(0,61,122,.12) 0%,rgba(0,61,122,.06) 100%);color:var(--ib);padding:6px 14px;border-radius:10px;font-size:12px;font-weight:700;border:1px solid rgba(0,61,122,.2);">MYP (Ages 11-16)</span>
 <span style="background:linear-gradient(135deg,rgba(0,61,122,.12) 0%,rgba(0,61,122,.06) 100%);color:var(--ib);padding:6px 14px;border-radius:10px;font-size:12px;font-weight:700;border:1px solid rgba(0,61,122,.2);">DP & EE</span>
 <span style="background:linear-gradient(135deg,rgba(0,61,122,.12) 0%,rgba(0,61,122,.06) 100%);color:var(--ib);padding:6px 14px;border-radius:10px;font-size:12px;font-weight:700;border:1px solid rgba(0,61,122,.2);">CAS</span>
 </div>
 <div style="display:flex;gap:12px;align-items:stretch;">
 <a href="{{ route('tutors.index') }}?exam_board=ib" class="btn btn-primary" style="flex:1;font-size:14px;padding:12px 16px;background:linear-gradient(135deg,var(--ib) 0%,#001a4d 100%);border:none;box-shadow:0 6px 16px rgba(0,61,122,.2);">Browse tutors</a>
 <button onclick="alert('Waitlist feature coming soon')" class="btn" style="flex:0 1 auto;padding:12px 16px;border:2px solid var(--ib);color:var(--ib);background:transparent;font-weight:700;font-size:13px;cursor:pointer;transition:all .2s;">Waitlist</button>
 </div>
 </div>

 {{-- Edexcel --}}
 <div style="background:#fff;border-radius:24px;border:2px solid var(--light);padding:32px;transition:all .3s;box-shadow:0 4px 16px rgba(26,45,61,.06);" 
 onmouseover="this.style.borderColor='var(--edexcel)';this.style.boxShadow='0 16px 48px rgba(245,158,15,.18)';this.style.transform='translateY(-4px)'" 
 onmouseout="this.style.borderColor='var(--light)';this.style.boxShadow='0 4px 16px rgba(26,45,61,.06)';this.style.transform='translateY(0)'">
 <div style="display:flex;align-items:flex-start;gap:16px;margin-bottom:20px;">
 <div style="width:56px;height:56px;background:linear-gradient(135deg,var(--edexcel) 0%,#e08500 100%);border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:800;color:var(--navy);flex-shrink:0;box-shadow:0 8px 20px rgba(245,158,15,.2);">E</div>
 <div style="flex:1;">
 <h3 style="font-size:22px;font-weight:800;color:var(--navy);font-family:'Fraunces',Georgia,serif;margin-bottom:2px;">Edexcel/Pearson</h3>
 <p style="font-size:13px;color:var(--muted);font-weight:500;">International GCSE & A Level</p>
 </div>
 </div>
 <p style="font-size:15px;color:var(--muted);margin-bottom:16px;line-height:1.7;">Complete preparation for International GCSE and International A Level. Access to past papers, mark schemes, and expert exam techniques.</p>
 <div style="display:flex;flex-wrap:wrap;gap:7px;margin-bottom:20px;">
 <span style="background:linear-gradient(135deg,rgba(245,158,15,.12) 0%,rgba(245,158,15,.06) 100%);color:var(--edexcel);padding:6px 14px;border-radius:10px;font-size:12px;font-weight:700;border:1px solid rgba(245,158,15,.2);">IGCSE (Grades 9-11)</span>
 <span style="background:linear-gradient(135deg,rgba(245,158,15,.12) 0%,rgba(245,158,15,.06) 100%);color:var(--edexcel);padding:6px 14px;border-radius:10px;font-size:12px;font-weight:700;border:1px solid rgba(245,158,15,.2);">A Level (Grades 12+)</span>
 <span style="background:linear-gradient(135deg,rgba(245,158,15,.12) 0%,rgba(245,158,15,.06) 100%);color:var(--edexcel);padding:6px 14px;border-radius:10px;font-size:12px;font-weight:700;border:1px solid rgba(245,158,15,.2);">Past Papers</span>
 <span style="background:linear-gradient(135deg,rgba(245,158,15,.12) 0%,rgba(245,158,15,.06) 100%);color:var(--edexcel);padding:6px 14px;border-radius:10px;font-size:12px;font-weight:700;border:1px solid rgba(245,158,15,.2);">Resources</span>
 </div>
 <div style="display:flex;gap:12px;align-items:stretch;">
 <a href="{{ route('tutors.index') }}?exam_board=edexcel" class="btn btn-primary" style="flex:1;font-size:14px;padding:12px 16px;background:linear-gradient(135deg,var(--edexcel) 0%,#e08500 100%);border:none;box-shadow:0 6px 16px rgba(245,158,15,.2);">Browse tutors</a>
 <button onclick="alert('Waitlist feature coming soon')" class="btn" style="flex:0 1 auto;padding:12px 16px;border:2px solid var(--edexcel);color:var(--edexcel);background:transparent;font-weight:700;font-size:13px;cursor:pointer;transition:all .2s;">Waitlist</button>
 </div>
 </div>

 {{-- American --}}
 <div style="background:#fff;border-radius:24px;border:2px solid var(--light);padding:32px;transition:all .3s;box-shadow:0 4px 16px rgba(26,45,61,.06);" 
 onmouseover="this.style.borderColor='var(--american)';this.style.boxShadow='0 16px 48px rgba(30,64,175,.18)';this.style.transform='translateY(-4px)'" 
 onmouseout="this.style.borderColor='var(--light)';this.style.boxShadow='0 4px 16px rgba(26,45,61,.06)';this.style.transform='translateY(0)'">
 <div style="display:flex;align-items:flex-start;gap:16px;margin-bottom:20px;">
 <div style="width:56px;height:56px;background:linear-gradient(135deg,var(--american) 0%,#1028a8 100%);border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:800;color:#fff;flex-shrink:0;box-shadow:0 8px 20px rgba(30,64,175,.2);">US</div>
 <div style="flex:1;">
 <h3 style="font-size:22px;font-weight:800;color:var(--navy);font-family:'Fraunces',Georgia,serif;margin-bottom:2px;">American System</h3>
 <p style="font-size:13px;color:var(--muted);font-weight:500;">US High School & College Prep</p>
 </div>
 </div>
 <p style="font-size:15px;color:var(--muted);margin-bottom:16px;line-height:1.7;">Comprehensive support for American high school, AP courses, and college admissions. SAT, ACT, and coursework excellence.</p>
 <div style="display:flex;flex-wrap:wrap;gap:7px;margin-bottom:20px;">
 <span style="background:linear-gradient(135deg,rgba(30,64,175,.12) 0%,rgba(30,64,175,.06) 100%);color:var(--american);padding:6px 14px;border-radius:10px;font-size:12px;font-weight:700;border:1px solid rgba(30,64,175,.2);">High School (Grades 9-12)</span>
 <span style="background:linear-gradient(135deg,rgba(30,64,175,.12) 0%,rgba(30,64,175,.06) 100%);color:var(--american);padding:6px 14px;border-radius:10px;font-size:12px;font-weight:700;border:1px solid rgba(30,64,175,.2);">AP Courses</span>
 <span style="background:linear-gradient(135deg,rgba(30,64,175,.12) 0%,rgba(30,64,175,.06) 100%);color:var(--american);padding:6px 14px;border-radius:10px;font-size:12px;font-weight:700;border:1px solid rgba(30,64,175,.2);">SAT & ACT</span>
 <span style="background:linear-gradient(135deg,rgba(30,64,175,.12) 0%,rgba(30,64,175,.06) 100%);color:var(--american);padding:6px 14px;border-radius:10px;font-size:12px;font-weight:700;border:1px solid rgba(30,64,175,.2);">Admissions</span>
 </div>
 <div style="display:flex;gap:12px;align-items:stretch;">
 <a href="{{ route('tutors.index') }}?exam_board=american" class="btn btn-primary" style="flex:1;font-size:14px;padding:12px 16px;background:linear-gradient(135deg,var(--american) 0%,#1028a8 100%);border:none;box-shadow:0 6px 16px rgba(30,64,175,.2);">Browse tutors</a>
 <button onclick="alert('Waitlist feature coming soon')" class="btn" style="flex:0 1 auto;padding:12px 16px;border:2px solid var(--american);color:var(--american);background:transparent;font-weight:700;font-size:13px;cursor:pointer;transition:all .2s;">Waitlist</button>
 </div>
 </div>

 </div>
 <style>
 .exam-boards-grid { grid-template-columns: 1fr !important; }
 @media(min-width:640px) { .exam-boards-grid { grid-template-columns: repeat(2, 1fr) !important; } }
 @media(min-width:1024px) { .exam-boards-grid { grid-template-columns: repeat(4, 1fr) !important; } }
 </style>
 </div>
</section>

{{-- SUBJECT BROWSE --}}
<section style="padding:72px 0;background:#fff;">
 <div class="w">
 <div style="text-align:center;margin-bottom:40px;">
 <h2 style="font-size:clamp(28px,4vw,44px);color:var(--navy);margin-bottom:8px;font-family:'Fraunces',Georgia,serif;font-weight:800;">Browse by subject</h2>
 <p style="font-size:16px;color:var(--muted);max-width:600px;margin:0 auto;">Find tutors specialized in your subject. All international exam boards covered.</p>
 </div>
 
 <div style="display:flex;flex-wrap:wrap;gap:10px;justify-content:center;max-width:1000px;margin:0 auto;">
 @foreach(['Mathematics','Physics','Chemistry','Biology','English','Business Studies','Economics','Computer Science','French','History','Geography'] as $subject)
 <a href="{{ route('tutors.index') }}?subject={{ urlencode($subject) }}&curriculum=international"
 style="background:var(--navy);color:#fff;padding:12px 24px;border-radius:999px;font-size:14px;font-weight:600;text-decoration:none;transition:all .2s;display:flex;align-items:center;gap:8px;border:2px solid var(--navy);"
 onmouseover="this.style.background='transparent';this.style.color='var(--navy)'"
 onmouseout="this.style.background='var(--navy)';this.style.color='#fff'">
 <i class="fa-solid fa-book"></i> {{ $subject }}
 </a>
 @endforeach
 </div>
 </div>
</section>

{{-- FEATURES SECTION (Dark Navy) --}}
<section style="padding:72px 0;background:var(--navy);">
 <div class="w">
 <div style="display:grid;grid-template-columns:1fr;gap:20px;max-width:1100px;margin:0 auto;" class="features-grid">
 @foreach([
 ['fa-certificate','Exam Board Expertise','Expert tutors certified in Cambridge, IB, Edexcel, and American curricula.'],
 ['fa-file-pdf','Past Papers & Mark Schemes','Access comprehensive past paper libraries with detailed mark schemes.'],
 ['fa-zap','Exam-Series Intensives','Intensive sessions focused on exam technique and high-frequency questions.'],
 ['fa-coins','Clear Pricing','Transparent pricing in KES or USD. No hidden fees, no surprises.'],
 ] as [$icon,$title,$desc])
 <div style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);border-radius:18px;padding:28px;transition:all .2s;"
 onmouseover="this.style.background='rgba(255,255,255,.12)';this.style.transform='translateY(-4px)'" onmouseout="this.style.background='rgba(255,255,255,.08)';this.style.transform='translateY(0)'">
 <div style="width:56px;height:56px;background:linear-gradient(135deg,var(--amber) 0%,#e08500 100%);border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:16px;flex-shrink:0;box-shadow:0 6px 16px rgba(245,158,15,.3);">
 <i class="fa-solid {{ $icon }}" style="font-size:24px;color:var(--navy);"></i>
 </div>
 <h3 style="font-size:16px;font-weight:800;color:#fff;margin-bottom:8px;font-family:'Fraunces',Georgia,serif;">{{ $title }}</h3>
 <p style="font-size:13px;color:rgba(255,255,255,.65);line-height:1.7;margin:0;">{{ $desc }}</p>
 </div>
 @endforeach
 </div>
 <style>
 .features-grid { grid-template-columns: 1fr !important; }
 @media(min-width:640px) { .features-grid { grid-template-columns: repeat(2, 1fr) !important; } }
 @media(min-width:1024px) { .features-grid { grid-template-columns: repeat(4, 1fr) !important; } }
 </style>
 </div>
</section>

{{-- TRANSITION BANNER --}}
<section style="padding:60px 0;background:rgba(26,45,61,.02);">
 <div class="w">
 <div style="background:var(--navy);border-radius:24px;padding:40px;text-align:center;border:2px solid var(--amber);">
 <div class="badge badge-navy" style="background:rgba(245,158,15,.2);color:var(--amber);margin-bottom:16px;width:fit-content;margin-left:auto;margin-right:auto;"><i class="fa-solid fa-exchange"></i> Curriculum Transition</div>
 <h2 style="font-size:clamp(24px,4vw,38px);color:#fff;margin-bottom:14px;font-family:'Fraunces',Georgia,serif;font-weight:800;">Switching from CBC to IGCSE?</h2>
 <p style="font-size:15px;color:rgba(255,255,255,.75);margin-bottom:24px;max-width:700px;margin-left:auto;margin-right:auto;line-height:1.8;">
 Moving to an international curriculum can feel overwhelming. Our specialized tutors understand the transition, the content gaps, and exactly how to bridge them quickly. Get expert guidance tailored to your background.
 </p>
 <a href="#" class="btn btn-secondary"><i class="fa-solid fa-arrow-right"></i> See the transition guide</a>
 </div>
 </div>
</section>

@include('partials.footer')

</body>
</html>

<style>
@media(min-width:1024px){
 .hero-intl{grid-template-columns:1fr 480px!important;grid-auto-flow:column;}
}
</style>

@livewireScripts
