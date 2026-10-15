<!DOCTYPE html>
<html lang="en">
<head>
<?php echo $__env->make('partials.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<title>How It Works – Mwalimu</title>
<meta name="description" content="Learn how Mwalimu connects students and parents with TSC-registered tutors in Kenya. Book, pay, and learn in 3 simple steps.">
<style>
.faq-answer{display:none;padding:0 22px 18px;font-size:14px;color:rgba(21,40,31,.65);line-height:1.85;}
.faq-toggle{cursor:pointer;}
.faq-toggle.open + .faq-answer{display:block;}
.faq-toggle.open .faq-chevron{transform:rotate(180deg);}
.faq-chevron{transition:transform .2s;}
</style>
</head>
<body>
<?php echo $__env->make('partials.nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<section style="background:linear-gradient(135deg,#111C16,#15281F);padding:80px 0;text-align:center;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-80px;left:50%;transform:translateX(-50%);width:600px;height:600px;background:rgba(109,40,217,.1);border-radius:50%;pointer-events:none;"></div>
    <div style="max-width:1360px;margin:0 auto;padding:0 24px;position:relative;z-index:1;">
        <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(109,40,217,.3);color:rgba(255,255,255,.9);padding:6px 18px;border-radius:999px;font-size:13px;font-weight:600;margin-bottom:20px;">
            <i class="fa-solid fa-circle-info fa-xs"></i> Simple · Secure · Effective
        </div>
        <h1 style="font-family:'Fraunces',serif;font-size:clamp(36px,6vw,64px);color:#fff;margin-bottom:16px;line-height:1.1;">Learning made <span style="color:#D4A017;">simple.</span></h1>
        <p style="font-size:18px;color:rgba(255,255,255,.6);max-width:560px;margin:0 auto 32px;line-height:1.8;">
            Mwalimu connects Kenyan students and parents with verified, TSC-registered expert tutors. Book in minutes, pay securely, join a live class from anywhere.
        </p>
        <div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center;margin-bottom:48px;">
            <a href="<?php echo e(route('tutors.index')); ?>" style="display:inline-flex;align-items:center;gap:8px;padding:14px 36px;border-radius:999px;background:#6D28D9;color:#fff;font-size:15px;font-weight:700;text-decoration:none;transition:all .18s;" onmouseover="this.style.background='#4C1D95';this.style.transform='translateY(-1px)'" onmouseout="this.style.background='#6D28D9';this.style.transform='translateY(0)'">
                <i class="fa-solid fa-magnifying-glass fa-sm"></i> Find a Tutor
            </a>
            <a href="<?php echo e(route('register')); ?>?role=tutor" style="display:inline-flex;align-items:center;gap:8px;padding:14px 36px;border-radius:999px;background:rgba(255,255,255,.1);color:#fff;border:1.5px solid rgba(255,255,255,.25);font-size:15px;font-weight:600;text-decoration:none;transition:all .18s;" onmouseover="this.style.background='rgba(255,255,255,.18)'" onmouseout="this.style.background='rgba(255,255,255,.1)'">
                <i class="fa-solid fa-chalkboard-user fa-sm"></i> Become a Tutor
            </a>
        </div>
        
        <div style="display:flex;flex-wrap:wrap;gap:32px;justify-content:center;padding-top:36px;border-top:1px solid rgba(255,255,255,.1);">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [['500+','Verified Tutors'],['4.9★','Average Rating'],['10,000+','Sessions Done'],['48hr','Max Approval Time'],['80+','Subjects']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$n,$l]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="text-align:center;">
                <div style="font-family:'Fraunces',serif;font-size:26px;font-weight:800;color:#fff;"><?php echo e($n); ?></div>
                <div style="font-size:12px;color:rgba(255,255,255,.45);margin-top:3px;"><?php echo e($l); ?></div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</section>


<section style="padding:80px 0;" x-data="{ role: 'student' }">
    <div style="max-width:1360px;margin:0 auto;padding:0 24px;">
        <div style="text-align:center;margin-bottom:36px;">
            <h2 style="font-family:'Fraunces',serif;font-size:clamp(26px,4vw,40px);color:#15281F;margin-bottom:10px;">How does it work?</h2>
            <p style="font-size:17px;color:rgba(21,40,31,.55);">Choose your role</p>
        </div>

        
        <div style="display:flex;background:#fff;border-radius:999px;padding:4px;border:1px solid rgba(21,40,31,.1);width:fit-content;margin:0 auto 52px;box-shadow:0 2px 12px rgba(21,40,31,.06);">
            <button @click="role='student'"
                    :style="role==='student' ? 'background:#6D28D9;color:#fff;' : 'background:transparent;color:rgba(21,40,31,.5);'"
                    style="padding:11px 32px;border-radius:999px;border:none;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;transition:all .2s;">
                <i class="fa-solid fa-graduation-cap fa-sm" style="margin-right:6px;"></i> I'm a Student / Parent
            </button>
            <button @click="role='tutor'"
                    :style="role==='tutor' ? 'background:#6D28D9;color:#fff;' : 'background:transparent;color:rgba(21,40,31,.5);'"
                    style="padding:11px 32px;border-radius:999px;border:none;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;transition:all .2s;">
                <i class="fa-solid fa-chalkboard-user fa-sm" style="margin-right:6px;"></i> I'm a Tutor
            </button>
        </div>

        
        <div x-show="role==='student'">
            <div style="display:grid;grid-template-columns:1fr;gap:16px;" class="steps-grid">
                <?php $studentSteps = [
                    ['1','fa-magnifying-glass','#6D28D9','Find Your Tutor','Browse TSC-registered tutors by subject, price, and availability. Read real reviews from students and parents who have already booked.','Filter by Kenyan curriculum subject','View ratings, reviews and availability','Choose from 500+ verified tutors','Background-checked for your safety'],
                    ['2','fa-clock','#10B981','Pick Session Type & Time','Choose your session block — Quick Help (15min), Concept Check (30min), Guided Practice (45min), or Deep Dive (60min). Fixed pricing, no surprises.','Quick Help – 15 min – KSh 300','Concept Check – 30 min – KSh 500','Guided Practice – 45 min – KSh 680','Deep Dive – 60 min – KSh 800'],
                    ['3','fa-mobile-screen-button','#E8522A','Pay via M-Pesa or Card','Complete your booking by paying through Paystack — Kenya\'s trusted payment gateway. Pay by M-Pesa, Visa, or Mastercard. Secure and instant.','M-Pesa, Visa & Mastercard accepted','Payment secured by Paystack','Full refund if tutor cancels','Booking confirmed instantly after payment'],
                    ['4','fa-envelope','#D4A017','Receive Your Session Code','Immediately after payment, you and the tutor both receive unique session access codes by email. Yours is STU-XXXXXX. The tutor\'s is TUT-XXXXXX.','Unique STU-XXXXXX code emailed to you','Tutor receives TUT-XXXXXX separately','Session date, time and duration confirmed','Reminder sent 1 hour before session'],
                    ['5','fa-video','#3B82F6','Join Your Live Session','On session day, open your booking and enter your STU-XXXXXX code. Your private Jitsi classroom opens instantly in your browser — no download needed.','Enter STU-XXXXXX code to verify identity','Private Jitsi video room opens instantly','Screen sharing and whiteboard available','No app download required'],
                    ['6','fa-star','#EC4899','Review & Rebook','After your session, leave a star rating and written review to help other students. Rebook your favourite tutor directly from your dashboard with one click.','Star rating + written review','Tutor appears in Saved for easy rebook','Session history tracked in dashboard','Message tutor directly for follow-up'],
                ]; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $studentSteps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$num,$icon,$color,$title,$desc,$p1,$p2,$p3,$p4]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:28px;display:flex;gap:20px;align-items:flex-start;box-shadow:0 2px 16px rgba(21,40,31,.04);">
                    <div style="width:52px;height:52px;border-radius:16px;background:<?php echo e($color); ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;position:relative;">
                        <i class="fa-solid <?php echo e($icon); ?>" style="color:#fff;font-size:20px;"></i>
                        <span style="position:absolute;top:-6px;right:-6px;width:20px;height:20px;background:#fff;border:2px solid <?php echo e($color); ?>;border-radius:50%;font-size:10px;font-weight:800;color:<?php echo e($color); ?>;display:flex;align-items:center;justify-content:center;"><?php echo e($num); ?></span>
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:<?php echo e($color); ?>;margin-bottom:4px;">Step <?php echo e($num); ?></div>
                        <h3 style="font-family:'Fraunces',serif;font-size:19px;color:#15281F;margin-bottom:8px;"><?php echo e($title); ?></h3>
                        <p style="font-size:14px;color:rgba(21,40,31,.6);line-height:1.8;margin-bottom:14px;"><?php echo e($desc); ?></p>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;" class="pt-g">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [$p1,$p2,$p3,$p4]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div style="display:flex;align-items:flex-start;gap:7px;font-size:13px;color:#15281F;">
                                <i class="fa-solid fa-check" style="color:<?php echo e($color); ?>;margin-top:2px;flex-shrink:0;font-size:11px;"></i><?php echo e($pt); ?>

                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <style>.pt-g{grid-template-columns:1fr!important;}@media(min-width:480px){.pt-g{grid-template-columns:1fr 1fr!important;}}</style>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        
        <div x-show="role==='tutor'" x-cloak>
            <div style="display:grid;grid-template-columns:1fr;gap:16px;" class="steps-grid">
                <?php $tutorSteps = [
                    ['1','fa-pen-to-square','#6D28D9','Register','Sign up and select Tutor as your role. Complete your profile with your name, email, phone, subjects (max 2), and level (JSS/SS/F3/F4).','Free to join — no upfront fees','Enter name exactly as on National ID','Select up to 2 subjects you teach','Set your availability schedule'],
                    ['2','fa-id-card-clip','#10B981','Upload Documents','Upload your National ID, Degree Certificate, TSC Certificate, Certificate of Good Conduct, and a recent passport photo. Our team reviews within 72 hours.','National ID / Passport required','TSC Certificate required','Degree Certificate recommended','Certificate of Good Conduct required'],
                    ['3','fa-clock','#D4A017','Get Verified','Standard verification takes up to 72 hours. Need it faster? Pay KSh 1,000 for Speedy Verification — approved within 24 hours.','Standard: up to 72 hours','Speedy: 24 hours (KSh 1,000 fee)','Email notification when approved','Action required if any doc rejected'],
                    ['4','fa-calendar-check','#E8522A','Profile Goes Live','Once approved, your profile is visible to thousands of students and parents searching for tutors. Students can book sessions with you instantly.','Profile listed in search results','Students can book & pay instantly','You receive booking notifications','Dashboard to manage all sessions'],
                    ['5','fa-bell','#3B82F6','Receive Bookings & Codes','When a student books and pays, you receive an email notification with session details, your earnings, and your unique TUT-XXXXXX session access code.','Email: "New Session Booked"','Your TUT-XXXXXX code included','Session details and date/time shown','Also visible in your dashboard'],
                    ['6','fa-video','#EC4899','Host Your Session','On session day, enter your TUT-XXXXXX code to open the private Jitsi classroom as the moderator. You control recording and screen sharing.','Enter TUT-XXXXXX to join as moderator','Full video, audio, screen share','Earnings credited after completion','Request payout via M-Pesa anytime'],
                    ['7','fa-money-bill-wave','#10B981','Get Paid','After each completed session, your earnings are automatically credited to your Mwalimu wallet (platform keeps 15%). Request payout to M-Pesa or bank anytime.','Earnings credited post-session','Platform fee: 15% — you keep 85%','Request payout to M-Pesa or bank','Processed within 2–3 business days'],
                ]; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tutorSteps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$num,$icon,$color,$title,$desc,$p1,$p2,$p3,$p4]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="background:#fff;border-radius:20px;border:1px solid rgba(21,40,31,.08);padding:28px;display:flex;gap:20px;align-items:flex-start;box-shadow:0 2px 16px rgba(21,40,31,.04);">
                    <div style="width:52px;height:52px;border-radius:16px;background:<?php echo e($color); ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;position:relative;">
                        <i class="fa-solid <?php echo e($icon); ?>" style="color:#fff;font-size:20px;"></i>
                        <span style="position:absolute;top:-6px;right:-6px;width:20px;height:20px;background:#fff;border:2px solid <?php echo e($color); ?>;border-radius:50%;font-size:10px;font-weight:800;color:<?php echo e($color); ?>;display:flex;align-items:center;justify-content:center;"><?php echo e($num); ?></span>
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:<?php echo e($color); ?>;margin-bottom:4px;">Step <?php echo e($num); ?></div>
                        <h3 style="font-family:'Fraunces',serif;font-size:19px;color:#15281F;margin-bottom:8px;"><?php echo e($title); ?></h3>
                        <p style="font-size:14px;color:rgba(21,40,31,.6);line-height:1.8;margin-bottom:14px;"><?php echo e($desc); ?></p>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;" class="pt-g">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [$p1,$p2,$p3,$p4]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div style="display:flex;align-items:flex-start;gap:7px;font-size:13px;color:#15281F;">
                                <i class="fa-solid fa-check" style="color:<?php echo e($color); ?>;margin-top:2px;flex-shrink:0;font-size:11px;"></i><?php echo e($pt); ?>

                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
    <style>.steps-grid{grid-template-columns:1fr!important;}@media(min-width:768px){.steps-grid{grid-template-columns:repeat(2,1fr)!important;}}@media(min-width:1024px){.steps-grid{grid-template-columns:repeat(3,1fr)!important;}}</style>
</section>


<section style="background:#0D1F14;padding:80px 0;">
    <div style="max-width:1360px;margin:0 auto;padding:0 24px;">
        <div style="text-align:center;margin-bottom:44px;">
            <h2 style="font-family:'Fraunces',serif;font-size:clamp(26px,4vw,40px);color:#fff;margin-bottom:10px;">Simple, fixed pricing</h2>
            <p style="font-size:17px;color:rgba(255,255,255,.5);">No negotiation, no surprises. Same price for every tutor.</p>
        </div>
        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;max-width:900px;margin:0 auto;" class="price-grid">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                ['15','Quick Help','⚡','KSh 300','Rapid revision drill, a single exam question, or a quick topic check.','Daily revision, quick questions, exam week'],
                ['30','Concept Check','📘','KSh 500','Targeted practice — work through a problem set or concept clarity.','Homework help, concept explanations, KCSE prep'],
                ['45','Guided Practice','🎯','KSh 680','Full structured lesson with warm-up, teaching, practice and Q&A.','Weekly lessons, exam preparation, new topics'],
                ['60','Deep Dive','🏆','KSh 800','Maximum learning. Best value per minute for multiple topics.','Holiday intensive prep, catching up, multiple topics'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$mins,$name,$icon,$price,$desc,$ideal]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);border-radius:20px;padding:26px;transition:background .2s;cursor:pointer;" onmouseover="this.style.background='rgba(255,255,255,.12)'" onmouseout="this.style.background='rgba(255,255,255,.07)'">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span style="font-size:26px;"><?php echo e($icon); ?></span>
                        <div>
                            <div style="font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:#fff;"><?php echo e($name); ?></div>
                            <div style="font-size:12px;color:rgba(255,255,255,.4);"><?php echo e($mins); ?> minutes</div>
                        </div>
                    </div>
                    <div style="font-family:'Fraunces',serif;font-size:24px;font-weight:800;color:#D4A017;"><?php echo e($price); ?></div>
                </div>
                <p style="font-size:13px;color:rgba(255,255,255,.55);line-height:1.65;margin-bottom:10px;"><?php echo e($desc); ?></p>
                <p style="font-size:12px;color:rgba(255,255,255,.35);font-style:italic;"><i class="fa-solid fa-check fa-xs" style="color:#10B981;margin-right:4px;"></i><?php echo e($ideal); ?></p>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div style="text-align:center;margin-top:28px;">
            <a href="<?php echo e(route('tutors.index')); ?>" style="display:inline-flex;align-items:center;gap:8px;padding:13px 36px;border-radius:999px;background:#D4A017;color:#15281F;font-size:15px;font-weight:700;text-decoration:none;transition:all .18s;" onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
                <i class="fa-solid fa-magnifying-glass fa-sm"></i> Browse Tutors & Book Now
            </a>
        </div>
    </div>
    <style>.price-grid{grid-template-columns:repeat(2,1fr)!important;}@media(min-width:768px){.price-grid{grid-template-columns:repeat(4,1fr)!important;}}</style>
</section>


<section style="padding:80px 0;">
    <div style="max-width:1100px;margin:0 auto;padding:0 24px;">
        <div style="display:grid;grid-template-columns:1fr;gap:48px;align-items:center;" class="code-grid">
            <div>
                <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(109,40,217,.1);color:#6D28D9;padding:6px 16px;border-radius:999px;font-size:13px;font-weight:600;margin-bottom:22px;">
                    <i class="fa-solid fa-key fa-xs"></i> Secure Session Access
                </div>
                <h2 style="font-family:'Fraunces',serif;font-size:clamp(26px,4vw,40px);color:#15281F;margin-bottom:16px;">Your unique session access code</h2>
                <p style="font-size:16px;color:rgba(21,40,31,.6);line-height:1.85;margin-bottom:28px;">After booking and payment, both tutor and student receive a unique one-time access code by email. This ensures only the right people join each private session — no uninvited guests.</p>
                <div style="display:flex;flex-direction:column;gap:16px;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                        ['fa-envelope','Sent instantly by email','Codes arrive in your inbox immediately after payment confirmation.'],
                        ['fa-lock','Unique per booking','Each session generates a fresh STU-XXXXXX (student) and TUT-XXXXXX (tutor) code.'],
                        ['fa-video','Opens private Jitsi room','Enter your code on the booking page to launch a private, encrypted video classroom.'],
                        ['fa-shield-halved','Prevents unauthorised access','Only the code holder can enter. No public links. No sharing of contacts.'],
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$icon,$title,$desc]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="display:flex;align-items:flex-start;gap:14px;">
                        <div style="width:44px;height:44px;background:rgba(109,40,217,.08);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fa-solid <?php echo e($icon); ?>" style="color:#6D28D9;font-size:17px;"></i>
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:15px;color:#15281F;"><?php echo e($title); ?></div>
                            <div style="font-size:13px;color:rgba(21,40,31,.55);margin-top:3px;line-height:1.65;"><?php echo e($desc); ?></div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <div>
                <div style="background:#fff;border-radius:24px;border:1px solid rgba(21,40,31,.08);padding:36px;text-align:center;box-shadow:0 8px 40px rgba(21,40,31,.07);">
                    <p style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:rgba(21,40,31,.4);margin-bottom:20px;">Two codes. One session.</p>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:24px;">
                        <div style="background:rgba(109,40,217,.06);border:2px solid rgba(109,40,217,.2);border-radius:16px;padding:20px;">
                            <p style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:rgba(21,40,31,.45);margin-bottom:8px;">Student Code</p>
                            <p style="font-family:monospace;font-size:20px;font-weight:800;color:#6D28D9;letter-spacing:.08em;">STU-XXXXXX</p>
                            <p style="font-size:11px;color:rgba(21,40,31,.4);margin-top:6px;">Sent to student/parent</p>
                        </div>
                        <div style="background:rgba(16,185,129,.06);border:2px solid rgba(16,185,129,.2);border-radius:16px;padding:20px;">
                            <p style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:rgba(21,40,31,.45);margin-bottom:8px;">Tutor Code</p>
                            <p style="font-family:monospace;font-size:20px;font-weight:800;color:#10B981;letter-spacing:.08em;">TUT-XXXXXX</p>
                            <p style="font-size:11px;color:rgba(21,40,31,.4);margin-top:6px;">Sent to tutor</p>
                        </div>
                    </div>
                    <div style="background:#F9FAF9;border-radius:14px;padding:16px;text-align:left;">
                        <p style="font-size:12px;font-weight:700;color:#15281F;margin-bottom:10px;display:flex;align-items:center;gap:6px;"><i class="fa-solid fa-list-check" style="color:#6D28D9;"></i> How to join</p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['1. Open your booking in your Mwalimu dashboard','2. Click "Join Session" button','3. Enter your code (STU- or TUT-)','4. Jitsi classroom opens instantly']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <p style="font-size:13px;color:rgba(21,40,31,.6);margin-bottom:5px;"><?php echo e($step); ?></p>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>.code-grid{grid-template-columns:1fr!important;}@media(min-width:1024px){.code-grid{grid-template-columns:1fr 1fr!important;}}</style>
</section>


<section style="padding:80px 0;background:rgba(21,40,31,.03);">
    <div style="max-width:900px;margin:0 auto;padding:0 24px;">
        <div style="text-align:center;margin-bottom:44px;">
            <h2 style="font-family:'Fraunces',serif;font-size:clamp(26px,4vw,40px);color:#15281F;margin-bottom:10px;">Mwalimu vs Traditional Tutoring</h2>
        </div>
        <div style="background:#fff;border-radius:22px;border:1px solid rgba(21,40,31,.08);overflow:hidden;box-shadow:0 4px 24px rgba(21,40,31,.06);">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:#F9FAF9;">
                        <th style="text-align:left;padding:14px 20px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:rgba(21,40,31,.5);width:40%;border-bottom:1.5px solid rgba(21,40,31,.08);">Feature</th>
                        <th style="text-align:left;padding:14px 20px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#6D28D9;border-bottom:1.5px solid rgba(21,40,31,.08);">✓ Mwalimu</th>
                        <th style="text-align:left;padding:14px 20px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:rgba(21,40,31,.4);border-bottom:1.5px solid rgba(21,40,31,.08);">Traditional</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                        ['Tutor verification','TSC-registered & background-checked','Often unverified'],
                        ['Booking process','Online, instant, 24/7','Phone calls, scheduling hassle'],
                        ['Payment security','Paystack — M-Pesa & cards','Cash, often no receipt'],
                        ['Session lengths','15, 30, 45 or 60 min fixed blocks','Usually 1hr fixed, wasted time'],
                        ['Access codes','Unique code per session','No access control'],
                        ['Live classroom','Jitsi — no download','Physical or WhatsApp'],
                        ['Session records','Full dashboard history','No records'],
                        ['Reviews & ratings','Verified student reviews','No accountability'],
                        ['Pricing','Clear KSh fixed pricing upfront','Negotiated, opaque'],
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$feature,$mw,$trad]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr style="border-bottom:1px solid rgba(21,40,31,.05);" onmouseover="this.style.background='#FAFCFA'" onmouseout="this.style.background='transparent'">
                        <td style="padding:13px 20px;font-size:14px;font-weight:500;color:#15281F;"><?php echo e($feature); ?></td>
                        <td style="padding:13px 20px;font-size:13px;color:#15281F;"><span style="color:#10B981;margin-right:6px;font-weight:700;">✓</span><?php echo e($mw); ?></td>
                        <td style="padding:13px 20px;font-size:13px;color:rgba(21,40,31,.5);"><span style="color:#DC2626;margin-right:6px;font-weight:700;">✗</span><?php echo e($trad); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>


<section style="padding:80px 0;">
    <div style="max-width:760px;margin:0 auto;padding:0 24px;">
        <div style="text-align:center;margin-bottom:44px;">
            <h2 style="font-family:'Fraunces',serif;font-size:clamp(26px,4vw,40px);color:#15281F;margin-bottom:10px;">Frequently Asked Questions</h2>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
            ['How do I know tutors are trustworthy?','Every tutor on Mwalimu is TSC-registered, has submitted a National ID, and passes a background check including a Certificate of Good Conduct. Our academic team manually reviews all documents before approving any tutor profile.'],
            ['What if I\'m not happy with my session?','Contact our support team within 24 hours of your session. We review all disputes fairly. First-time bookings come with a 100% money-back guarantee — no questions asked.'],
            ['Do I need to download anything to join a session?','No. Sessions run in your browser using Jitsi Meet. Just enter your access code on the booking page and your session opens in a new browser tab. Works on mobile and desktop.'],
            ['Can parents book sessions for their children?','Yes. Select "Parent" when creating your account. You can add child profiles (name, age, grade level) and book sessions on their behalf. Your session code is sent to your email.'],
            ['How are session prices calculated?','Mwalimu uses fixed pricing for all sessions: Quick Help (15min) = KSh 300, Concept Check (30min) = KSh 500, Guided Practice (45min) = KSh 680, Deep Dive (60min) = KSh 800. The same price applies regardless of which tutor you choose.'],
            ['How and when do tutors get paid?','After a session is completed, the tutor\'s earnings (85% of the session fee — Mwalimu keeps 15%) are credited to their in-app wallet. Tutors can request a payout to M-Pesa or bank at any time. Payouts are processed within 2–3 business days.'],
            ['What happens if a tutor cancels?','If a tutor cancels a confirmed session, you receive a full refund automatically. We flag repeated cancellations and may suspend tutors who cancel excessively.'],
            ['How does the Speedy Verification work for tutors?','New tutors can skip the standard 72-hour queue and receive verification within 24 hours by paying a one-time KSh 1,000 Speedy Verification fee during the onboarding process.'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$q,$a]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div style="background:#fff;border-radius:16px;border:1px solid rgba(21,40,31,.08);overflow:hidden;margin-bottom:8px;">
            <div class="faq-toggle" onclick="this.classList.toggle('open')"
                 style="display:flex;align-items:center;justify-content:space-between;padding:18px 22px;font-weight:600;font-size:15px;color:#15281F;transition:background .15s;"
                 onmouseover="this.style.background='#FAFCFA'" onmouseout="this.style.background='transparent'">
                <span><?php echo e($q); ?></span>
                <i class="fa-solid fa-chevron-down faq-chevron" style="font-size:12px;color:rgba(21,40,31,.4);flex-shrink:0;margin-left:12px;"></i>
            </div>
            <div class="faq-answer"><?php echo e($a); ?></div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</section>


<section style="background:linear-gradient(135deg,#15281F,#0D1F14);padding:90px 0;text-align:center;">
    <div style="max-width:1360px;margin:0 auto;padding:0 24px;">
        <h2 style="font-family:'Fraunces',serif;font-size:clamp(28px,5vw,52px);color:#fff;margin-bottom:14px;">Ready to get started?</h2>
        <p style="font-size:18px;color:rgba(255,255,255,.6);margin-bottom:36px;max-width:480px;margin-left:auto;margin-right:auto;">Join thousands of students, parents and tutors on Kenya's leading tutoring platform.</p>
        <div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center;">
            <a href="<?php echo e(route('tutors.index')); ?>" style="display:inline-flex;align-items:center;gap:8px;padding:15px 40px;border-radius:999px;background:#6D28D9;color:#fff;font-size:16px;font-weight:700;text-decoration:none;transition:all .18s;" onmouseover="this.style.background='#4C1D95';this.style.transform='translateY(-1px)'" onmouseout="this.style.background='#6D28D9';this.style.transform='translateY(0)'">
                <i class="fa-solid fa-magnifying-glass fa-sm"></i> Find a Tutor
            </a>
            <a href="<?php echo e(route('register')); ?>?role=tutor" style="display:inline-flex;align-items:center;gap:8px;padding:15px 40px;border-radius:999px;background:rgba(255,255,255,.1);color:#fff;border:1.5px solid rgba(255,255,255,.3);font-size:16px;font-weight:600;text-decoration:none;transition:all .18s;" onmouseover="this.style.background='rgba(255,255,255,.18)'" onmouseout="this.style.background='rgba(255,255,255,.1)'">
                <i class="fa-solid fa-chalkboard-user fa-sm"></i> Become a Tutor
            </a>
        </div>
    </div>
</section>

<?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<script>
// FAQ accordion
document.querySelectorAll('.faq-toggle').forEach(t => {
    t.addEventListener('click', () => {
        const isOpen = t.classList.contains('open');
        document.querySelectorAll('.faq-toggle').forEach(x => x.classList.remove('open'));
        if (!isOpen) t.classList.add('open');
    });
});
</script>
<?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

</body>
</html>
<?php /**PATH C:\Users\Administrator\Downloads\mediacity.co.in-2026-10-13\mediacity.co.in-2026-10-13\tutor-platform\resources\views/pages/how-it-works.blade.php ENDPATH**/ ?>