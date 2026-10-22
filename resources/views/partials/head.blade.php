<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700;9..144,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">
<script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<style>
/* ── MWALIMU DESIGN SYSTEM ─────────────────────────────────────────── */
:root{
  --ink:#15281F; --cream:#FBF5E6; --violet:#0A64BC;
  --coral:#E8522A; --gold:#EEBE15; --mint:#10B981;
  --muted:rgba(21,40,31,.55); --hair:rgba(21,40,31,.1);
  --sidebar:#111C16;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{background:var(--cream);scroll-behavior:smooth;}
body{font-family:'DM Sans',sans-serif;color:var(--ink);background:var(--cream);line-height:1.6;-webkit-font-smoothing:antialiased;}
h1,h2,h3,h4{font-family:'Fraunces',Georgia,serif;line-height:1.1;}
a{color:inherit;text-decoration:none;} img{max-width:100%;display:block;}
[x-cloak]{display:none!important;} button{font-family:inherit;}

/* ── LAYOUT ── */
.w{max-width:1360px;margin:0 auto;padding:0 24px;}
@media(min-width:1024px){.w{padding:0 48px;}}
.section{padding:72px 0;} .section-alt{padding:72px 0;background:rgba(21,40,31,.03);}
.section-dark{padding:72px 0;background:var(--ink);}

/* ── BUTTONS ── */
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:12px 26px;border-radius:999px;font-weight:600;font-size:14px;cursor:pointer;transition:all .18s;border:none;font-family:inherit;text-decoration:none;white-space:nowrap;}
.btn-v{background:var(--violet);color:#fff;} .btn-v:hover{background:#120A8F;transform:translateY(-1px);box-shadow:0 6px 20px rgba(10,100,188,.3);}
.btn-o{background:transparent;color:var(--ink);border:2px solid var(--ink);} .btn-o:hover{background:var(--ink);color:var(--cream);}
.btn-coral{background:var(--coral);color:#fff;} .btn-coral:hover{opacity:.9;transform:translateY(-1px);}
.btn-mint{background:var(--mint);color:#fff;} .btn-mint:hover{background:#059669;}
.btn-gold{background:var(--gold);color:var(--ink);font-weight:700;} .btn-gold:hover{opacity:.9;transform:translateY(-1px);}
.btn-w{background:#fff;color:var(--violet);font-weight:700;} .btn-w:hover{background:var(--cream);}
.btn-ghost{background:rgba(255,255,255,.12);color:#fff;border:1.5px solid rgba(255,255,255,.25);} .btn-ghost:hover{background:rgba(255,255,255,.2);}
.btn-sm{padding:8px 18px;font-size:13px;}
.btn-lg{padding:15px 36px;font-size:16px;}

/* ── CARDS ── */
.card{background:#fff;border-radius:20px;border:1px solid var(--hair);box-shadow:0 2px 16px rgba(21,40,31,.06);}
.card-lift{transition:transform .2s,box-shadow .2s;} .card-lift:hover{transform:translateY(-5px);box-shadow:0 14px 36px rgba(21,40,31,.12);}
.card-p{padding:24px;}

/* ── BADGES ── */
.badge{display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:999px;font-size:11px;font-weight:600;}
.badge-v{background:rgba(10,100,188,.1);color:var(--violet);}
.badge-m{background:rgba(16,185,129,.1);color:var(--mint);}
.badge-c{background:rgba(232,82,42,.1);color:var(--coral);}
.badge-g{background:rgba(212,160,23,.12);color:#B8920F;}
.badge-dark{background:rgba(21,40,31,.08);color:var(--ink);}
.badge-white{background:rgba(255,255,255,.18);color:#fff;}

/* ── FORM INPUTS ── */
.field{margin-bottom:15px;}
.field-label{font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;display:flex;align-items:center;gap:5px;margin-bottom:5px;}
.field-label i{color:var(--violet);font-size:11px;}
.input{width:100%;height:47px;padding:0 15px;border-radius:12px;border:1.5px solid rgba(21,40,31,.2);background:#fff;font-size:14px;font-family:inherit;color:var(--ink);outline:none;transition:all .15s;}
.input:focus{border-color:var(--violet);box-shadow:0 0 0 3px rgba(10,100,188,.1);}
.input.err{border-color:var(--coral);}
.input-icon{position:relative;} .input-icon i{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:13px;pointer-events:none;}
.input-icon .input{padding-left:38px;}
textarea.input{height:auto;padding:12px 15px;resize:vertical;line-height:1.65;}
select.input{appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' fill='none'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%2315281F' stroke-width='1.5' stroke-linecap='round'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 14px center;padding-right:36px;}
.field-err{font-size:12px;color:var(--coral);margin-top:4px;display:flex;align-items:center;gap:4px;}

/* ── FLASH MESSAGES ── */
.flash{padding:13px 18px;border-radius:14px;font-size:14px;font-weight:500;margin-bottom:18px;display:flex;align-items:center;gap:10px;}
.flash-ok{background:#DCFCE7;color:#166534;border:1px solid #BBF7D0;}
.flash-err{background:#FEE2E2;color:#991B1B;border:1px solid #FECACA;}
.flash-info{background:rgba(10,100,188,.08);color:var(--violet);border:1px solid rgba(10,100,188,.2);}

/* ── SECTION HEADINGS ── */
.sec-eyebrow{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;margin-bottom:14px;}
.sec-eyebrow-v{color:var(--violet);}
.sec-eyebrow-gold{color:var(--gold);}
.sec-title{font-size:clamp(26px,4vw,42px);color:var(--ink);margin-bottom:10px;}
.sec-title-white{color:#fff;}
.sec-sub{font-size:17px;color:var(--muted);line-height:1.7;}
.sec-sub-white{color:rgba(255,255,255,.6);}
.sec-hdr{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:36px;}
.view-all{font-size:14px;font-weight:600;color:var(--violet);display:flex;align-items:center;gap:4px;white-space:nowrap;}
.view-all:hover{text-decoration:underline;}

/* ── STATS ── */
.stat-card{background:#fff;border-radius:16px;border:1px solid var(--hair);padding:20px;}
.stat-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;font-size:18px;}
.stat-label{font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;}
.stat-value{font-family:'Fraunces',serif;font-size:28px;font-weight:700;color:var(--ink);line-height:1;}
.stat-sub{font-size:12px;color:var(--muted);margin-top:5px;}

/* ── TABLE ── */
.tbl-wrap{overflow-x:auto;}
table{width:100%;border-collapse:collapse;}
thead th{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);padding:10px 16px;text-align:left;border-bottom:1.5px solid var(--hair);white-space:nowrap;}
tbody tr{border-bottom:1px solid rgba(21,40,31,.05);transition:background .1s;}
tbody tr:hover{background:#FAFCFA;} tbody tr:last-child{border-bottom:none;}
td{padding:13px 16px;font-size:13.5px;vertical-align:middle;}

/* ── AVATAR ── */
.av{display:flex;align-items:center;justify-content:center;border-radius:12px;font-weight:700;color:#fff;flex-shrink:0;}

/* ── NAV ── */
.mw-nav{position:sticky;top:0;z-index:100;background:rgba(251,245,230,.93);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);border-bottom:1px solid var(--hair);}
.mw-nav-inner{display:flex;align-items:center;justify-content:space-between;height:70px;gap:16px;}
.mw-logo{display:flex;align-items:center;gap:10px;font-family:'Fraunces',serif;font-size:22px;font-weight:800;color:var(--ink);}
.mw-logo em{font-style:normal;color:var(--violet);}
.mw-logo svg{flex-shrink:0;}
.mw-desk-links{display:none;align-items:center;gap:2px;}
@media(min-width:1024px){.mw-desk-links{display:flex;}}
.mw-desk-links a{padding:8px 15px;border-radius:999px;font-size:14px;font-weight:500;transition:background .15s;color:var(--ink);}
.mw-desk-links a:hover,.mw-desk-links a.active{background:rgba(21,40,31,.07);}
.mw-desk-links a.active{font-weight:700;color:var(--violet);}
.mw-hamburger{display:flex;width:42px;height:42px;border-radius:999px;border:2px solid var(--hair);background:none;cursor:pointer;align-items:center;justify-content:center;color:var(--ink);}
@media(min-width:1024px){.mw-hamburger{display:none;}}
.mw-mobile-menu{position:absolute;left:0;right:0;top:100%;background:var(--cream);border-bottom:1px solid var(--hair);padding:14px 24px;display:flex;flex-direction:column;gap:4px;z-index:99;box-shadow:0 8px 32px rgba(21,40,31,.1);}
.mw-mobile-menu a{padding:12px 0;font-size:15px;font-weight:500;border-bottom:1px solid var(--hair);display:flex;align-items:center;gap:8px;}
.mw-mobile-menu a:last-child{border-bottom:none;}
.mw-mobile-menu a i{color:var(--violet);width:16px;text-align:center;}
.mw-desk-signin{display:none!important;}
@media(min-width:1024px){.mw-desk-signin{display:block!important;}}

/* ── FOOTER ── */
.mw-footer{background:var(--ink);color:rgba(255,255,255,.65);}
.mw-footer-main{padding:56px 0 40px;}
.mw-footer-grid{display:grid;grid-template-columns:1fr;gap:36px;margin-bottom:40px;}
@media(min-width:640px){.mw-footer-grid{grid-template-columns:repeat(2,1fr);}}
@media(min-width:1024px){.mw-footer-grid{grid-template-columns:2fr 1fr 1fr 1fr;}}
.mw-footer-brand p{font-size:14px;color:rgba(255,255,255,.45);line-height:1.75;margin-top:12px;max-width:280px;}
.mw-footer-col h4{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.35);margin-bottom:14px;}
.mw-footer-col a{display:block;font-size:14px;color:rgba(255,255,255,.6);margin-bottom:9px;transition:color .15s;}
.mw-footer-col a:hover{color:#fff;}
.mw-footer-bottom{border-top:1px solid rgba(255,255,255,.08);padding:20px 0;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;font-size:12px;color:rgba(255,255,255,.3);}

/* ── ANNOUNCEMENT BAR ── */
.mw-ann{background:var(--violet);color:#fff;font-size:13px;padding:9px 24px;display:flex;align-items:center;justify-content:center;gap:8px;text-align:center;position:relative;}
.mw-ann button{position:absolute;right:16px;background:none;border:none;color:rgba(255,255,255,.6);cursor:pointer;font-size:18px;line-height:1;}

/* ── COOKIE BANNER ── */
.cookie-banner{position:fixed;bottom:20px;left:50%;transform:translateX(-50%);z-index:9999;width:calc(100% - 32px);max-width:440px;}
.cookie-inner{background:#FFFDF5;border:2px solid var(--ink);border-radius:20px;padding:18px 20px;box-shadow:0 14px 40px rgba(21,40,31,.22);}

/* ── SCROLLBAR ── */
::-webkit-scrollbar{width:6px;height:6px;}
::-webkit-scrollbar-track{background:transparent;}
::-webkit-scrollbar-thumb{background:rgba(21,40,31,.2);border-radius:3px;}
</style>
