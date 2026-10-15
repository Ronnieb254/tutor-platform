<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'TutorLink'); ?> – Find Verified Tutors in Kenya</title>
    <meta name="description" content="<?php echo $__env->yieldContent('description', 'Book verified 1:1 tutors across 80+ subjects in Kenya.'); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700;9..144,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

    <style>
        :root {
            --ink: #15281F;
            --cream: #FBF5E6;
            --violet: #6D28D9;
            --coral: #E8522A;
            --gold: #D4A017;
            --mint: #10B981;
            --muted: rgba(21,40,31,0.55);
            --hairline: rgba(21,40,31,0.1);
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { background: var(--cream); }
        body { font-family: 'DM Sans', sans-serif; color: var(--ink); background: var(--cream); line-height: 1.6; }
        h1,h2,h3,h4 { font-family: 'Fraunces', Georgia, serif; line-height: 1.1; }
        a { color: inherit; text-decoration: none; }
        img { max-width: 100%; display: block; }
        [x-cloak] { display: none !important; }

        .container { max-width: 1360px; margin: 0 auto; padding: 0 24px; }
        @media(min-width:1024px) { .container { padding: 0 40px; } }

        /* Buttons */
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 28px; border-radius: 999px; font-weight: 600; font-size: 15px; cursor: pointer; transition: all .15s; border: none; }
        .btn-primary { background: var(--violet); color: #fff; }
        .btn-primary:hover { background: #4C1D95; transform: translateY(-1px); }
        .btn-outline { background: transparent; color: var(--ink); border: 2px solid var(--ink); }
        .btn-outline:hover { background: var(--ink); color: var(--cream); }
        .btn-coral { background: var(--coral); color: #fff; }
        .btn-white { background: #fff; color: var(--violet); font-weight: 700; }
        .btn-white:hover { background: var(--cream); }

        /* Cards */
        .card { background: #fff; border-radius: 24px; border: 1px solid var(--hairline); box-shadow: 0 2px 16px rgba(21,40,31,0.06); }
        .card-hover { transition: transform .2s, box-shadow .2s; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(21,40,31,0.12); }

        /* Badges */
        .badge { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; }
        .badge-violet { background: rgba(109,40,217,0.1); color: var(--violet); }
        .badge-mint { background: rgba(16,185,129,0.1); color: var(--mint); }
        .badge-coral { background: rgba(232,82,42,0.1); color: var(--coral); }
        .badge-gold { background: rgba(212,160,23,0.1); color: #8B6914; }

        /* Nav */
        nav.main-nav { position: sticky; top: 0; z-index: 100; background: rgba(251,245,230,0.92); backdrop-filter: blur(12px); border-bottom: 1px solid var(--hairline); }
        .nav-inner { display: flex; align-items: center; justify-content: space-between; height: 72px; gap: 16px; }
        .nav-logo { display: flex; align-items: center; gap: 10px; font-family: 'Fraunces', serif; font-size: 22px; font-weight: 700; color: var(--ink); flex-shrink: 0; }
        .nav-links { display: flex; align-items: center; gap: 4px; }
        .nav-links a { padding: 8px 16px; border-radius: 999px; font-size: 14px; font-weight: 500; color: var(--ink); transition: background .15s; }
        .nav-links a:hover { background: rgba(21,40,31,0.06); }
        .nav-actions { display: flex; align-items: center; gap: 10px; }
        .nav-login { padding: 8px 18px; border-radius: 999px; font-size: 14px; font-weight: 600; color: var(--ink); transition: background .15s; }
        .nav-login:hover { background: rgba(21,40,31,0.06); }
        .nav-hamburger { display: none; width: 40px; height: 40px; border-radius: 999px; border: 2px solid var(--hairline); background: none; cursor: pointer; align-items: center; justify-content: center; }
        @media(max-width:1023px) { .nav-links { display: none; } .nav-hamburger { display: flex; } }
        @media(max-width:640px) { .nav-login { display: none; } }

        /* Mobile menu */
        .mobile-menu { display: none; position: absolute; left: 0; right: 0; top: 100%; background: var(--cream); border-bottom: 1px solid var(--hairline); padding: 16px 24px; flex-direction: column; gap: 4px; z-index: 99; box-shadow: 0 8px 24px rgba(21,40,31,0.1); }
        .mobile-menu a { padding: 12px 0; font-size: 15px; font-weight: 500; border-bottom: 1px solid var(--hairline); }
        .mobile-menu a:last-child { border-bottom: none; }

        /* Hero */
        .hero { padding: 80px 0 60px; overflow: hidden; }
        @media(min-width:1024px) { .hero { padding: 100px 0 80px; } }
        .hero-grid { display: grid; grid-template-columns: 1fr; gap: 48px; align-items: center; }
        @media(min-width:1024px) { .hero-grid { grid-template-columns: 1fr 1fr; } }
        .hero h1 { font-size: clamp(40px, 6vw, 72px); color: var(--ink); margin-bottom: 20px; }
        .hero h1 span { color: var(--violet); }
        .hero p { font-size: 18px; color: var(--muted); margin-bottom: 32px; max-width: 520px; line-height: 1.7; }
        .hero-btns { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 40px; }
        .hero-search { display: flex; gap: 8px; max-width: 480px; }
        .hero-search input { flex: 1; height: 52px; padding: 0 20px; border-radius: 999px; border: 1.5px solid rgba(21,40,31,0.2); background: #fff; font-size: 15px; font-family: inherit; color: var(--ink); outline: none; transition: border-color .15s; }
        .hero-search input:focus { border-color: var(--violet); }
        .hero-trust { display: flex; flex-wrap: wrap; gap: 24px; margin-top: 36px; }
        .trust-item { display: flex; align-items: center; gap: 8px; }
        .trust-num { font-family: 'Fraunces', serif; font-size: 26px; font-weight: 700; color: var(--ink); }
        .trust-label { font-size: 13px; color: var(--muted); }

        /* Floating cards (hero visual) */
        .hero-visual { position: relative; height: 400px; display: none; }
        @media(min-width:1024px) { .hero-visual { display: block; } }
        .tutor-float-card { position: absolute; background: #fff; border-radius: 20px; border: 1px solid var(--hairline); padding: 14px; box-shadow: 0 8px 24px rgba(21,40,31,0.1); width: 180px; }
        .tutor-float-card .avatar { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 700; color: #fff; margin-bottom: 8px; }
        .tutor-float-card .name { font-family: 'Fraunces', serif; font-size: 14px; font-weight: 600; color: var(--ink); }
        .tutor-float-card .subject { font-size: 12px; color: var(--muted); margin-top: 2px; }
        .tutor-float-card .rating { display: flex; align-items: center; gap: 4px; margin-top: 6px; font-size: 12px; }

        /* Duration blocks */
        .duration-section { background: var(--ink); color: #fff; padding: 72px 0; }
        .duration-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-top: 40px; }
        @media(min-width:768px) { .duration-grid { grid-template-columns: repeat(4, 1fr); } }
        .duration-card { background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: 20px; padding: 24px; transition: background .2s; cursor: pointer; }
        .duration-card:hover { background: rgba(255,255,255,0.14); }
        .duration-card .icon { width: 44px; height: 44px; background: rgba(109,40,217,0.3); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; }
        .duration-card .mins { font-family: 'Fraunces', serif; font-size: 36px; font-weight: 700; color: #fff; line-height: 1; }
        .duration-card .mins span { font-size: 16px; font-weight: 400; color: rgba(255,255,255,0.5); }
        .duration-card .title { font-weight: 600; font-size: 14px; color: #fff; margin: 6px 0; }
        .duration-card .desc { font-size: 12px; color: rgba(255,255,255,0.55); line-height: 1.5; }
        .duration-card .price { font-size: 13px; font-weight: 700; color: var(--gold); margin-top: 12px; }

        /* Categories */
        .section { padding: 72px 0; }
        .section-header { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 40px; }
        .section-header h2 { font-size: clamp(28px, 4vw, 40px); color: var(--ink); }
        .section-header p { color: var(--muted); margin-top: 6px; font-size: 16px; }
        .view-all { font-size: 14px; font-weight: 600; color: var(--violet); display: flex; align-items: center; gap: 4px; white-space: nowrap; }
        .view-all:hover { text-decoration: underline; }
        .categories-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
        @media(min-width:640px) { .categories-grid { grid-template-columns: repeat(3, 1fr); } }
        @media(min-width:1024px) { .categories-grid { grid-template-columns: repeat(4, 1fr); } }
        .category-card { padding: 24px; cursor: pointer; transition: all .2s; }
        .category-card .icon { font-size: 36px; margin-bottom: 12px; }
        .category-card h3 { font-family: 'Fraunces', serif; font-size: 17px; font-weight: 600; color: var(--ink); transition: color .15s; }
        .category-card:hover h3 { color: var(--violet); }
        .category-card p { font-size: 12px; color: var(--muted); margin-top: 4px; }

        /* How it works */
        .how-section { background: rgba(21,40,31,0.03); padding: 72px 0; }
        .steps-grid { display: grid; grid-template-columns: 1fr; gap: 24px; margin-top: 48px; }
        @media(min-width:768px) { .steps-grid { grid-template-columns: repeat(3, 1fr); } }
        .step-card { padding: 32px; text-align: center; position: relative; }
        .step-num { position: absolute; top: -12px; left: -12px; width: 32px; height: 32px; background: var(--violet); color: #fff; border-radius: 999px; font-size: 13px; font-weight: 700; display: flex; align-items: center; justify-content: center; }
        .step-icon { width: 60px; height: 60px; background: rgba(109,40,217,0.1); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 28px; margin: 0 auto 20px; }
        .step-card h3 { font-family: 'Fraunces', serif; font-size: 20px; color: var(--ink); margin-bottom: 10px; }
        .step-card p { font-size: 14px; color: var(--muted); line-height: 1.7; }

        /* Tutors grid */
        .tutor-card { padding: 20px; cursor: pointer; }
        .tutor-card-top { display: flex; align-items: flex-start; gap: 16px; }
        .tutor-avatar { width: 64px; height: 64px; border-radius: 16px; object-fit: cover; flex-shrink: 0; font-size: 24px; font-weight: 700; color: #fff; display: flex; align-items: center; justify-content: center; }
        .tutor-name { font-family: 'Fraunces', serif; font-size: 17px; font-weight: 600; color: var(--ink); }
        .tutor-verified { font-size: 12px; color: var(--mint); font-weight: 600; }
        .tutor-subjects { display: flex; flex-wrap: wrap; gap: 4px; margin-top: 6px; }
        .tutor-meta { display: flex; align-items: center; gap: 10px; margin-top: 8px; font-size: 13px; color: var(--muted); }
        .star { color: var(--gold); }
        .tutor-rate { font-weight: 700; color: var(--ink); }
        .tutor-bio { font-size: 13px; color: var(--muted); margin-top: 12px; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .duration-pills { display: grid; grid-template-columns: repeat(4, 1fr); gap: 6px; margin-top: 16px; }
        .duration-pill { text-align: center; border: 1.5px solid var(--hairline); border-radius: 12px; padding: 6px 4px; font-size: 11px; transition: all .15s; cursor: pointer; }
        .duration-pill:hover { border-color: var(--violet); background: rgba(109,40,217,0.05); }
        .duration-pill .d-min { font-weight: 700; color: var(--ink); }
        .duration-pill .d-price { color: var(--muted); margin-top: 1px; }
        .tutors-grid { display: grid; grid-template-columns: 1fr; gap: 20px; }
        @media(min-width:640px) { .tutors-grid { grid-template-columns: repeat(2, 1fr); } }
        @media(min-width:1024px) { .tutors-grid { grid-template-columns: repeat(3, 1fr); } }

        /* Tutor CTA */
        .tutor-cta { background: var(--violet); padding: 72px 0; }
        .tutor-cta-grid { display: grid; grid-template-columns: 1fr; gap: 48px; align-items: center; }
        @media(min-width:1024px) { .tutor-cta-grid { grid-template-columns: 1fr 1fr; } }
        .tutor-cta h2 { font-size: clamp(28px, 4vw, 44px); color: #fff; margin-bottom: 16px; }
        .tutor-cta p { color: rgba(255,255,255,0.75); font-size: 17px; line-height: 1.7; margin-bottom: 24px; }
        .cta-points { display: flex; flex-direction: column; gap: 10px; margin-bottom: 32px; }
        .cta-point { display: flex; align-items: center; gap: 10px; font-size: 14px; color: rgba(255,255,255,0.9); }
        .cta-point-dot { width: 20px; height: 20px; background: rgba(255,255,255,0.2); border-radius: 999px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .cta-stats { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
        .cta-stat { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 20px; text-align: center; }
        .cta-stat-num { font-family: 'Fraunces', serif; font-size: 32px; font-weight: 700; color: #fff; }
        .cta-stat-label { font-size: 12px; color: rgba(255,255,255,0.6); margin-top: 4px; }

        /* Testimonials */
        .testimonials-grid { display: grid; grid-template-columns: 1fr; gap: 20px; }
        @media(min-width:768px) { .testimonials-grid { grid-template-columns: repeat(3, 1fr); } }
        .testimonial-card { padding: 24px; }
        .stars { color: var(--gold); font-size: 16px; letter-spacing: 2px; margin-bottom: 14px; }
        .testimonial-text { font-size: 14px; color: var(--ink); line-height: 1.8; margin-bottom: 20px; font-style: italic; }
        .testimonial-author { display: flex; align-items: center; gap: 12px; padding-top: 16px; border-top: 1px solid var(--hairline); }
        .author-avatar { width: 36px; height: 36px; border-radius: 999px; background: rgba(109,40,217,0.15); display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--violet); font-size: 14px; }
        .author-name { font-weight: 600; font-size: 13px; color: var(--ink); }
        .author-note { font-size: 11px; color: var(--muted); margin-top: 2px; }

        /* Final CTA */
        .final-cta { background: var(--ink); color: #fff; padding: 80px 0; text-align: center; }
        .final-cta h2 { font-size: clamp(28px, 5vw, 52px); color: #fff; margin-bottom: 16px; }
        .final-cta p { color: rgba(255,255,255,0.6); font-size: 18px; margin-bottom: 36px; max-width: 500px; margin-left: auto; margin-right: auto; }
        .final-cta-btns { display: flex; flex-wrap: wrap; justify-content: center; gap: 12px; }

        /* Footer */
        footer { background: var(--ink); color: rgba(255,255,255,0.7); padding: 64px 0 32px; border-top: 1px solid rgba(255,255,255,0.08); }
        .footer-grid { display: grid; grid-template-columns: 1fr; gap: 40px; margin-bottom: 48px; }
        @media(min-width:640px) { .footer-grid { grid-template-columns: repeat(2, 1fr); } }
        @media(min-width:1024px) { .footer-grid { grid-template-columns: 2fr 1fr 1fr 1fr; } }
        .footer-brand p { font-size: 14px; line-height: 1.7; margin-top: 12px; max-width: 280px; color: rgba(255,255,255,0.5); }
        .footer-col h4 { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .12em; color: rgba(255,255,255,0.4); margin-bottom: 16px; }
        .footer-col a { display: block; font-size: 14px; color: rgba(255,255,255,0.65); margin-bottom: 8px; transition: color .15s; }
        .footer-col a:hover { color: #fff; }
        .footer-bottom { border-top: 1px solid rgba(255,255,255,0.08); padding-top: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; font-size: 12px; color: rgba(255,255,255,0.35); }

        /* Flash messages */
        .flash { padding: 12px 20px; border-radius: 16px; font-size: 14px; font-weight: 500; margin: 16px 0; }
        .flash-success { background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); color: var(--mint); }
        .flash-error { background: rgba(232,82,42,0.1); border: 1px solid rgba(232,82,42,0.3); color: var(--coral); }

        /* User dropdown */
        .user-menu { position: relative; }
        .user-trigger { display: flex; align-items: center; gap: 8px; padding: 6px 12px 6px 6px; border-radius: 999px; border: 1.5px solid var(--hairline); cursor: pointer; background: none; font-family: inherit; font-size: 14px; font-weight: 500; color: var(--ink); transition: border-color .15s; }
        .user-trigger:hover { border-color: rgba(109,40,217,0.4); }
        .user-trigger img { width: 28px; height: 28px; border-radius: 999px; object-fit: cover; }
        .user-dropdown { position: absolute; right: 0; top: calc(100% + 8px); width: 200px; background: #fff; border-radius: 16px; border: 1px solid var(--hairline); box-shadow: 0 8px 32px rgba(21,40,31,0.14); padding: 6px; z-index: 200; }
        .user-dropdown a, .user-dropdown button { display: flex; align-items: center; gap: 10px; width: 100%; padding: 10px 12px; border-radius: 10px; font-size: 14px; font-weight: 500; color: var(--ink); background: none; border: none; cursor: pointer; font-family: inherit; transition: background .15s; text-align: left; }
        .user-dropdown a:hover, .user-dropdown button:hover { background: var(--cream); }
        .user-dropdown .divider { height: 1px; background: var(--hairline); margin: 4px 0; }
        .user-dropdown .danger { color: var(--coral); }

        /* Notification bell */
        .notif-btn { position: relative; width: 40px; height: 40px; border-radius: 999px; border: 1.5px solid var(--hairline); background: none; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: border-color .15s; }
        .notif-btn:hover { border-color: rgba(109,40,217,0.4); }
        .notif-badge { position: absolute; top: -2px; right: -2px; width: 16px; height: 16px; background: var(--coral); color: #fff; border-radius: 999px; font-size: 9px; font-weight: 700; display: flex; align-items: center; justify-content: center; }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>


<div x-data="{ show: true }" x-show="show" x-cloak style="background:var(--ink);color:var(--cream);font-size:13px;padding:8px 16px;display:flex;align-items:center;justify-content:space-between;gap:12px;">
    <span style="margin:0 auto;">🎓 Find verified tutors across Kenya — book 15, 30, 45 or 60-minute sessions</span>
    <button @click="show=false" style="background:none;border:none;color:rgba(251,245,230,0.6);cursor:pointer;font-size:18px;line-height:1;flex-shrink:0;">×</button>
</div>


<nav class="main-nav" x-data="{ open: false }">
    <div class="container">
        <div class="nav-inner">
            
            <a href="<?php echo e(route('home')); ?>" class="nav-logo">
                <svg width="36" height="36" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="50" fill="#15281F"/>
                    <circle cx="50" cy="50" r="34" fill="#6D28D9"/>
                    <circle cx="38" cy="38" r="6" fill="#FBF5E6"/>
                </svg>
                TutorLink
            </a>

            
            <div class="nav-links">
                <a href="<?php echo e(route('tutors.index')); ?>">Find a Tutor</a>
                <a href="<?php echo e(route('courses.index')); ?>">Courses</a>
                <a href="<?php echo e(route('blog.index')); ?>">Blog</a>
                <a href="<?php echo e(route('home')); ?>#how-it-works">How It Works</a>
            </div>

            
            <div class="nav-actions">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('shared.notification-bell');

$__key = null;

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1809344140-0', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key);

echo $__html;

unset($__html);
unset($__key);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                    <div class="user-menu" x-data="{ open: false }">
                        <button class="user-trigger" @click="open=!open" @click.outside="open=false">
                            <img src="<?php echo e(auth()->user()->avatar_url); ?>" alt="<?php echo e(auth()->user()->name); ?>">
                            <span class="hidden-mobile"><?php echo e(Str::limit(auth()->user()->name, 16)); ?></span>
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" :style="open ? 'transform:rotate(180deg)' : ''" style="transition:.2s"><path d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                        </button>
                        <div class="user-dropdown" x-show="open" x-transition x-cloak>
                            <a href="<?php echo e(route('dashboard')); ?>">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                                Dashboard
                            </a>
                            <div class="divider"></div>
                            <form method="POST" action="<?php echo e(route('logout')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="danger">
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="nav-login">Sign In</a>
                    <a href="<?php echo e(route('register')); ?>" class="btn btn-primary" style="padding:10px 20px;font-size:14px;">Get Started</a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <button class="nav-hamburger" @click="open=!open" aria-label="Menu">
                    <svg x-show="!open" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="open" x-cloak width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        
        <div class="mobile-menu" x-show="open" x-cloak style="display:flex">
            <a href="<?php echo e(route('tutors.index')); ?>">Find a Tutor</a>
            <a href="<?php echo e(route('courses.index')); ?>">Courses</a>
            <a href="<?php echo e(route('blog.index')); ?>">Blog</a>
            <a href="<?php echo e(route('home')); ?>#how-it-works">How It Works</a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->guest()): ?>
            <a href="<?php echo e(route('login')); ?>" style="color:var(--violet);font-weight:600;">Sign In</a>
            <a href="<?php echo e(route('register')); ?>" style="margin-top:8px;" class="btn btn-primary">Create Free Account</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</nav>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
<div class="container"><div class="flash flash-success"><?php echo e(session('success')); ?></div></div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
<div class="container"><div class="flash flash-error"><?php echo e(session('error')); ?></div></div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<main><?php echo $__env->yieldContent('content'); ?></main>


<footer>
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="<?php echo e(route('home')); ?>" style="display:flex;align-items:center;gap:10px;color:#fff;font-family:'Fraunces',serif;font-size:20px;font-weight:700;">
                    <svg width="30" height="30" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="#FBF5E6"/><circle cx="50" cy="50" r="34" fill="#6D28D9"/><circle cx="38" cy="38" r="6" fill="#FBF5E6"/></svg>
                    TutorLink
                </a>
                <p>Connecting learners and parents with verified tutors across Kenya. Book sessions in flexible time blocks.</p>
            </div>
            <div class="footer-col">
                <h4>For Students</h4>
                <a href="<?php echo e(route('tutors.index')); ?>">Find a Tutor</a>
                <a href="<?php echo e(route('courses.index')); ?>">Browse Courses</a>
                <a href="<?php echo e(route('register')); ?>">Sign Up Free</a>
                <a href="<?php echo e(route('home')); ?>#how-it-works">How It Works</a>
            </div>
            <div class="footer-col">
                <h4>For Tutors</h4>
                <a href="<?php echo e(route('register')); ?>?role=tutor">Become a Tutor</a>
                <a href="#">Tutor Guidelines</a>
                <a href="#">Earnings</a>
                <a href="#">Verification</a>
            </div>
            <div class="footer-col">
                <h4>Support</h4>
                <a href="#">Help Centre</a>
                <a href="#">Contact Us</a>
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; <?php echo e(date('Y')); ?> TutorLink. All rights reserved.</span>
            <span>Made with ❤ for learners across Kenya</span>
        </div>
    </div>
</footer>


<div x-data="{ show: !localStorage.getItem('cookie_ok') }" x-show="show" x-cloak
     style="position:fixed;bottom:24px;left:50%;transform:translateX(-50%);z-index:9999;width:calc(100% - 32px);max-width:440px;">
    <div style="background:#FFFDF5;border:2px solid var(--ink);border-radius:20px;padding:18px;box-shadow:0 14px 40px rgba(21,40,31,0.22);">
        <div style="display:flex;gap:12px;align-items:flex-start;">
            <span style="font-size:24px;flex-shrink:0;">🍪</span>
            <div style="flex:1;">
                <p style="font-weight:700;font-size:15px;color:var(--ink);margin-bottom:4px;">We value your privacy</p>
                <p style="font-size:12px;color:var(--muted);">We use cookies to personalise your experience and improve the platform.</p>
            </div>
        </div>
        <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:14px;">
            <button @click="localStorage.setItem('cookie_ok','none');show=false" style="padding:8px 16px;border-radius:999px;font-size:13px;font-weight:600;background:#fff;color:var(--ink);border:1.5px solid var(--ink);cursor:pointer;">Necessary Only</button>
            <button @click="localStorage.setItem('cookie_ok','all');show=false" style="padding:8px 18px;border-radius:999px;font-size:13px;font-weight:700;background:var(--violet);color:#fff;border:1.5px solid var(--ink);cursor:pointer;">Accept All</button>
        </div>
    </div>
</div>

<?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\Users\Administrator\Downloads\mediacity.co.in-2026-10-13\mediacity.co.in-2026-10-13\tutor-platform\resources\views/layouts/app.blade.php ENDPATH**/ ?>