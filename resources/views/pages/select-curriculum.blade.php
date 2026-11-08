<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')
<title>Choose Your Curriculum " Mwalimu</title>
<meta name="description" content="Select your curriculum track: Kenyan (8-4-4, CBE) or International (IGCSE, IB). Find the right tutor for your learning path.">
<style>
.curr-page { min-height: 100vh; background: var(--cream); display: flex; flex-direction: column; }
.curr-hero { background: var(--ink); padding: 56px 0 40px; text-align: center; }
.curr-main { flex: 1; display: flex; align-items: center; justify-content: center; padding: 48px 20px 60px; }
.curr-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; max-width: 900px; width: 100%; }
@media (max-width: 700px) { .curr-grid { grid-template-columns: 1fr; } }

.curr-card {
 border-radius: 28px;
 padding: 40px 36px 36px;
 cursor: pointer;
 transition: transform .22s, box-shadow .22s;
 position: relative;
 overflow: hidden;
 display: flex;
 flex-direction: column;
 min-height: 380px;
}
.curr-card:hover { transform: translateY(-6px); box-shadow: 0 20px 56px rgba(0,0,0,.22); }
.curr-card-kenyan { background: #0E3D2A; }
.curr-card-intl { background: #0D1F4A; }

.curr-card__badge {
 display: inline-flex; align-items: center; gap: 6px;
 padding: 5px 14px; border-radius: 999px; font-size: 11px; font-weight: 700;
 text-transform: uppercase; letter-spacing: .07em; margin-bottom: 20px;
 width: fit-content;
}
.curr-card-kenyan .curr-card__badge { background: rgba(16,185,129,.25); color: #6EE7B7; }
.curr-card-intl .curr-card__badge { background: rgba(99,102,241,.3); color: #C7D2FE; }

.curr-card__title { font-family: 'Fraunces', serif; font-size: clamp(24px, 3.5vw, 34px); font-weight: 800; color: #fff; margin-bottom: 8px; line-height: 1.15; }
.curr-card__sub { font-size: 14px; color: rgba(255,255,255,.55); margin-bottom: 24px; }

.curr-tag-list { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 28px; flex: 1; }
.curr-tag {
 display: inline-flex; align-items: center; gap: 5px;
 padding: 5px 14px; border-radius: 999px; font-size: 12px; font-weight: 600;
}
.curr-card-kenyan .curr-tag { background: rgba(255,255,255,.1); color: rgba(255,255,255,.8); }
.curr-card-intl .curr-tag { background: rgba(255,255,255,.1); color: rgba(255,255,255,.8); }

.curr-card__cta {
 display: inline-flex; align-items: center; justify-content: center; gap: 8px;
 width: 100%; padding: 15px 24px; border-radius: 999px; font-size: 15px; font-weight: 700;
 border: none; cursor: pointer; font-family: inherit; transition: opacity .15s;
 text-decoration: none;
}
.curr-card-kenyan .curr-card__cta { background: #10B981; color: #fff; }
.curr-card-intl .curr-card__cta { background: #6366F1; color: #fff; }
.curr-card__cta:hover { opacity: .88; }

/* Decorative blob */
.curr-card::after {
 content: ''; position: absolute; bottom: -60px; right: -60px;
 width: 200px; height: 200px; border-radius: 50%;
 pointer-events: none;
}
.curr-card-kenyan::after { background: rgba(16,185,129,.12); }
.curr-card-intl::after { background: rgba(99,102,241,.14); }

.or-divider { display: flex; align-items: center; justify-content: center; padding: 16px 0; }
@media (min-width: 700px) {
 .or-divider { flex-direction: column; padding: 0 12px; }
 .or-divider::before, .or-divider::after {
 content: ''; flex: 1; width: 1px; background: rgba(21,40,31,.15); min-height: 60px;
 }
 .or-divider span { margin: 12px 0; }
}
.or-divider span {
 font-size: 11px; font-weight: 700; text-transform: uppercase;
 letter-spacing: .1em; color: var(--muted);
 background: var(--cream); padding: 6px 10px; border-radius: 999px;
}
</style>
</head>
<body>
<div class="curr-page">
 {{-- Header --}}
 @include('partials.nav')

 <div class="curr-hero">
 <div class="w" style="text-align:center;">
 <div class="badge bv" style="margin-bottom:16px;">
 <i class="fa-solid fa-graduation-cap"></i> Choose your learning path
 </div>
 <h1 style="font-family:'Fraunces',serif;font-size:clamp(28px,5vw,50px);color:#fff;margin-bottom:12px;letter-spacing:-.02em;">
 Which curriculum do you follow?
 </h1>
 <p style="font-size:16px;color:rgba(255,255,255,.55);max-width:500px;margin:0 auto;">
 Select your track so we can show you the right tutors, subjects and levels for your education system.
 </p>
 </div>
 </div>

 <div class="curr-main">
 <div class="curr-grid">

 {{-- KENYAN CURRICULUM CARD --}}
 <form action="{{ route('curriculum.store') }}" method="POST" style="display:contents;">
 @csrf
 <input type="hidden" name="track" value="kenyan">
 <button type="submit" class="curr-card curr-card-kenyan" style="text-align:left;border:none;">
 <div class="curr-card__badge">
 <span style="font-size:inherit;"><i class="fa-solid fa-flag"></i></span> Kenyan Curriculum
 </div>
 <div class="curr-card__title">Learn the<br>Kenyan Way</div>
 <p class="curr-card__sub">Kenya's national education system " from secondary school through senior school.</p>
 <div class="curr-tag-list">
 <span class="curr-tag"><i class="fa-solid fa-book-open"></i> 8-4-4</span>
 <span class="curr-tag"><i class="fa-solid fa-star"></i> CBC Junior Secondary</span>
 <span class="curr-tag"><i class="fa-solid fa-star"></i> CBC Senior School</span>
 <span class="curr-tag"><i class="fa-solid fa-graduation-cap"></i> KCSE / KNEC</span>
 </div>
 <span class="curr-card__cta">
 <span style="font-size:inherit;"></span> Find a Kenyan-curriculum tutor
 </span>
 </button>
 </form>

 {{-- DIVIDER --}}
 <div class="or-divider"><span>OR</span></div>

 {{-- INTERNATIONAL CURRICULUM CARD --}}
 <form action="{{ route('curriculum.store') }}" method="POST" style="display:contents;">
 @csrf
 <input type="hidden" name="track" value="international">
 <button type="submit" class="curr-card curr-card-intl" style="text-align:left;border:none;">
 <div class="curr-card__badge">
 <i class="fa-solid fa-globe"></i> International Curriculum
 </div>
 <div class="curr-card__title">Learn for<br>the World</div>
 <p class="curr-card__sub">Globally recognised qualifications from Cambridge, Pearson Edexcel and the IB.</p>
 <div class="curr-tag-list">
 <span class="curr-tag"><span style="font-size:inherit;"><i class="fa-solid fa-medal"></i></span> IGCSE Cambridge</span>
 <span class="curr-tag"><span style="font-size:inherit;"><i class="fa-solid fa-medal"></i></span> IGCSE Edexcel</span>
 <span class="curr-tag"><i class="fa-solid fa-graduation-cap"></i> IB MYP</span>
 <span class="curr-tag"><i class="fa-solid fa-graduation-cap"></i> IB Diploma (HL/SL)</span>
 </div>
 <span class="curr-card__cta">
 <span style="font-size:inherit;"></span> Find an international tutor
 </span>
 </button>
 </form>

 </div>
 </div>

 {{-- Skip link --}}
 <div style="text-align:center;padding-bottom:32px;">
 <a href="{{ route('tutors.index') }}" style="font-size:13px;color:var(--muted);text-decoration:underline;">
 Browse all tutors without filtering
 </a>
 </div>

 @include('partials.footer')
</div>
</body>
</html>

