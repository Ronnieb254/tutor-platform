<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')
<title>Complete Your Tutor Profile – Mwalimu</title>
<style>
html,body{min-height:100%;}
.page{min-height:100vh;background:var(--cream);display:flex;flex-direction:column;}
.ob-head{background:var(--ink);padding:16px 24px;display:flex;align-items:center;justify-content:space-between;}
.ob-logo{display:flex;align-items:center;gap:10px;font-family:'Fraunces',serif;font-size:20px;font-weight:800;color:#fff;}
.ob-logo em{font-style:normal;color:#60a5fa;}
.ob-main{flex:1;padding:32px 16px 60px;display:flex;justify-content:center;}
.ob-wrap{width:100%;max-width:720px;}

/* Progress steps */
.prog-bar{display:flex;align-items:center;justify-content:center;gap:0;margin-bottom:36px;}
.pstep{display:flex;flex-direction:column;align-items:center;gap:6px;}
.pstep-dot{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;transition:all .2s;}
.pstep-label{font-size:11px;font-weight:600;white-space:nowrap;}
.pline{height:2px;width:48px;transition:background .3s;margin-bottom:18px;}

/* Cards */
.ob-card{background:#fff;border-radius:22px;border:1px solid rgba(21,40,31,.1);padding:32px;margin-bottom:16px;box-shadow:0 2px 20px rgba(21,40,31,.06);}
.ob-card h2{font-family:'Fraunces',serif;font-size:22px;color:var(--ink);margin-bottom:6px;}
.ob-card .sub{font-size:14px;color:var(--muted);margin-bottom:24px;}

/* Fields */
.field{margin-bottom:16px;}
.flabel{font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;display:flex;align-items:center;gap:5px;margin-bottom:6px;}
.flabel i{color:var(--Trust Blue);font-size:11px;}
.finput{width:100%;height:48px;padding:0 15px;border-radius:13px;border:1.5px solid rgba(21,40,31,.18);background:#fff;font-size:14px;font-family:inherit;color:var(--ink);outline:none;transition:all .18s;}
.finput:focus{border-color:var(--Trust Blue);box-shadow:0 0 0 3px rgba(10,100,188,.1);}
textarea.finput{height:auto;padding:12px 15px;resize:vertical;line-height:1.65;}
select.finput{appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%2315281F' stroke-width='1.5' fill='none'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 14px center;padding-right:36px;}
.ferr{font-size:12px;color:var(--coral);margin-top:4px;display:flex;align-items:center;gap:4px;}
.row2{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
@media(max-width:540px){.row2{grid-template-columns:1fr;}}

/* File upload zones */
.upload-zone{border:2px dashed rgba(21,40,31,.2);border-radius:16px;padding:24px;text-align:center;cursor:pointer;transition:all .2s;position:relative;overflow:hidden;}
.upload-zone:hover,.upload-zone.drag{border-color:var(--Trust Blue);background:rgba(10,100,188,.04);}
.upload-zone.has-file{border-style:solid;border-color:var(--mint);background:rgba(16,185,129,.04);}
.upload-zone.rejected{border-color:var(--coral);background:rgba(232,82,42,.04);}
.upload-zone input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;}
.upload-icon{font-size:28px;margin-bottom:8px;}
.upload-title{font-size:14px;font-weight:600;color:var(--ink);}
.upload-hint{font-size:12px;color:var(--muted);margin-top:3px;}
.upload-badge{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700;margin-top:8px;}

/* Speedy verification */
.speedy-card{border:2px solid var(--gold);border-radius:18px;padding:22px;background:rgba(212,160,23,.05);position:relative;overflow:hidden;}
.speedy-card::before{content:'';position:absolute;top:-30px;right:-30px;width:120px;height:120px;background:rgba(212,160,23,.1);border-radius:50%;}
.speedy-badge{display:inline-flex;align-items:center;gap:6px;background:var(--gold);color:var(--ink);padding:4px 12px;border-radius:999px;font-size:12px;font-weight:700;margin-bottom:12px;}

/* Subject chips */
.subject-grid{display:flex;flex-wrap:wrap;gap:6px;}
.s-chip{display:inline-flex;align-items:center;gap:5px;border:1.5px solid rgba(21,40,31,.15);border-radius:999px;padding:6px 14px;font-size:13px;font-weight:500;cursor:pointer;transition:all .18s;background:#fff;}
.s-chip:hover{border-color:rgba(10,100,188,.4);}
.s-chip.sel{border-color:var(--Trust Blue);background:var(--Trust Blue);color:#fff;}
.s-chip.sel input{display:none;}

/* Buttons */
.btn-next{width:100%;height:52px;border-radius:999px;background:var(--Trust Blue);color:#fff;font-size:15px;font-weight:700;font-family:inherit;border:none;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:8px;}
.btn-next:hover{background:#120A8F;transform:translateY(-1px);box-shadow:0 8px 20px rgba(10,100,188,.3);}
.btn-next:disabled{opacity:.5;cursor:not-allowed;transform:none;box-shadow:none;}
.btn-back{background:none;border:1.5px solid rgba(21,40,31,.18);color:var(--ink);border-radius:999px;padding:10px 24px;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;transition:all .15s;}
.btn-back:hover{border-color:rgba(21,40,31,.4);}
</style>
</head>
<body>
<div class="page">
    {{-- Header --}}
    <div class="ob-head">
        <a href="{{ route('home') }}" class="ob-logo">
            <svg width="30" height="30" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="#FBF5E6"/><circle cx="50" cy="50" r="34" fill="#0A64BC"/><circle cx="38" cy="38" r="6" fill="#FBF5E6"/></svg>
            <em>Mw</em>alimu
        </a>
        <span style="font-size:13px;color:rgba(255,255,255,.5);">Tutor Onboarding</span>
    </div>

    <div class="ob-main" x-data="tutorOnboarding()" x-init="init()">
        <div class="ob-wrap">

            {{-- Progress --}}
            <div class="prog-bar">
                @foreach(['Profile','Subjects','Documents','Verification'] as $i => $label)
                <div class="pstep">
                    <div class="pstep-dot"
                         :style="step > {{ $i+1 }} ? 'background:#10B981;color:#fff;' : step === {{ $i+1 }} ? 'background:#0A64BC;color:#fff;' : 'background:rgba(21,40,31,.08);color:rgba(21,40,31,.4);'">
                        <template x-if="step > {{ $i+1 }}"><i class="fa-solid fa-check fa-xs"></i></template>
                        <template x-if="step <= {{ $i+1 }}"><span>{{ $i+1 }}</span></template>
                    </div>
                    <span class="pstep-label"
                          :style="step === {{ $i+1 }} ? 'color:#0A64BC;' : 'color:rgba(21,40,31,.4);'">
                        {{ $label }}
                    </span>
                </div>
                @if(!$loop->last)
                <div class="pline" :style="step > {{ $i+1 }} ? 'background:#10B981;' : 'background:rgba(21,40,31,.1);'"></div>
                @endif
                @endforeach
            </div>

            {{-- ── STEP 1: Profile ── --}}
            <div x-show="step === 1">
                <div class="ob-card">
                    <h2><i class="fa-solid fa-user" style="color:var(--Trust Blue);font-size:18px;margin-right:8px;"></i>Your Profile</h2>
                    <p class="sub">Tell students about yourself. This will appear on your public tutor profile.</p>

                    <div class="row2">
                        <div class="field">
                            <div class="flabel"><i class="fa-solid fa-user"></i> Full Name <span style="color:var(--coral);">*</span></div>
                            <input type="text" x-model="form.name" class="finput" placeholder="As on National ID" required>
                            <p class="ferr" x-show="errors.name" x-text="errors.name"></p>
                        </div>
                        <div class="field">
                            <div class="flabel"><i class="fa-solid fa-phone"></i> Phone Number <span style="color:var(--coral);">*</span></div>
                            <input type="tel" x-model="form.phone" class="finput" placeholder="+254 7XX XXX XXX" required>
                        </div>
                    </div>

                    <div class="row2">
                        <div class="field">
                            <div class="flabel"><i class="fa-solid fa-location-dot"></i> City</div>
                            <input type="text" x-model="form.city" class="finput" placeholder="e.g. Nairobi">
                        </div>
                        <div class="field">
                            <div class="flabel"><i class="fa-solid fa-clock"></i> Years of Experience</div>
                            <input type="number" x-model="form.experience_years" class="finput" placeholder="e.g. 5" min="0" max="50">
                        </div>
                    </div>

                    <div class="field">
                        <div class="flabel"><i class="fa-solid fa-layer-group"></i> Level(s) You Teach <span style="color:var(--coral);">*</span></div>
                        <div style="display:flex;flex-wrap:wrap;gap:8px;">
                            @foreach(['JSS (Grade 7-9)','Senior School (Grade 10-12)','Form 3','Form 4','Primary (Class 1-6)'] as $level)
                            <label style="cursor:pointer;">
                                <input type="checkbox" :value="'{{ $level }}'" x-model="form.levels" style="display:none;">
                                <span :class="form.levels.includes('{{ $level }}') ? 's-chip sel' : 's-chip'"
                                      @click="toggleLevel('{{ $level }}')">
                                    <i class="fa-solid fa-graduation-cap fa-xs" x-show="form.levels.includes('{{ $level }}')"></i>
                                    {{ $level }}
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="field">
                        <div class="flabel"><i class="fa-solid fa-pen-to-square"></i> Professional Bio <span style="color:var(--coral);">*</span></div>
                        <textarea x-model="form.bio" class="finput" rows="4"
                                  placeholder="Describe your teaching experience, qualifications, approach and what makes your lessons effective... (minimum 50 characters)"></textarea>
                        <div style="display:flex;justify-content:space-between;margin-top:4px;">
                            <p class="ferr" x-show="errors.bio" x-text="errors.bio"></p>
                            <span style="font-size:11px;color:var(--muted);" x-text="(form.bio||'').length + ' / 50 min'"></span>
                        </div>
                    </div>

                    <div class="field">
                        <div class="flabel"><i class="fa-solid fa-coins"></i> Hourly Rate (KSh) <span style="color:var(--coral);">*</span></div>
                        <div style="position:relative;">
                            <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);font-size:13px;font-weight:600;color:var(--muted);">KSh</span>
                            <input type="number" x-model="form.hourly_rate" class="finput" style="padding-left:46px;" placeholder="e.g. 2000" min="500">
                        </div>
                        <p style="font-size:11px;color:var(--muted);margin-top:4px;"><i class="fa-solid fa-info-circle fa-xs" style="color:var(--Trust Blue);"></i> Session prices are fixed by Mwalimu. Your hourly rate is used for reference only.</p>
                    </div>
                </div>
                <div style="display:flex;gap:12px;">
                    <button class="btn-next" @click="validateStep1()">
                        Continue <i class="fa-solid fa-arrow-right fa-sm"></i>
                    </button>
                </div>
            </div>

            {{-- ── STEP 2: Subjects ── --}}
            <div x-show="step === 2" x-cloak>
                <div class="ob-card">
                    <h2><i class="fa-solid fa-book-open" style="color:var(--Trust Blue);font-size:18px;margin-right:8px;"></i>Subjects You Teach</h2>
                    <p class="sub">Select up to 2 subjects. Students will be matched with you based on these.</p>

                    @php $subjectsByCategory = \App\Models\Subject::active()->with('category')->get()->groupBy('category.name'); @endphp

                    <div style="max-height:400px;overflow-y:auto;padding-right:4px;">
                        @foreach($subjectsByCategory as $catName => $subjects)
                        <div style="margin-bottom:18px;">
                            <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:8px;">{{ $catName }}</p>
                            <div class="subject-grid">
                                @foreach($subjects as $subject)
                                <span :class="form.subjects.includes({{ $subject->id }}) ? 's-chip sel' : 's-chip'"
                                      @click="toggleSubject({{ $subject->id }})"
                                      :style="!form.subjects.includes({{ $subject->id }}) && form.subjects.length >= 2 ? 'opacity:.4;cursor:not-allowed;' : ''">
                                    <i class="fa-solid fa-check fa-xs" x-show="form.subjects.includes({{ $subject->id }})"></i>
                                    {{ $subject->name }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div style="margin-top:14px;padding:12px 14px;background:rgba(10,100,188,.05);border-radius:12px;border:1px solid rgba(10,100,188,.12);">
                        <span style="font-size:13px;font-weight:600;color:var(--Trust Blue);">
                            <i class="fa-solid fa-circle-info fa-xs"></i>
                            <span x-text="form.subjects.length"></span>/2 subjects selected
                            <span x-show="form.subjects.length === 2" style="color:var(--mint);margin-left:8px;"><i class="fa-solid fa-check fa-xs"></i> Maximum reached</span>
                        </span>
                    </div>
                    <p class="ferr" x-show="errors.subjects" x-text="errors.subjects" style="margin-top:8px;"></p>
                </div>
                <div style="display:flex;gap:12px;">
                    <button class="btn-back" @click="step=1"><i class="fa-solid fa-arrow-left fa-xs"></i> Back</button>
                    <button class="btn-next" @click="validateStep2()">Continue <i class="fa-solid fa-arrow-right fa-sm"></i></button>
                </div>
            </div>

            {{-- ── STEP 3: Documents ── --}}
            <div x-show="step === 3" x-cloak>
                <div class="ob-card">
                    <h2><i class="fa-solid fa-file-shield" style="color:var(--Trust Blue);font-size:18px;margin-right:8px;"></i>Verification Documents</h2>
                    <p class="sub">Upload your documents so we can verify your credentials. All files are stored securely. Accepted: JPG, PNG, PDF — max 5MB each.</p>

                    <div style="display:flex;flex-direction:column;gap:16px;">

                        {{-- National ID --}}
                        <div>
                            <div class="flabel"><i class="fa-solid fa-id-card"></i> National ID / Passport <span style="color:var(--coral);">*</span></div>
                            <div class="upload-zone" :class="files.national_id ? 'has-file' : ''"
                                 @dragover.prevent="$el.classList.add('drag')"
                                 @dragleave="$el.classList.remove('drag')"
                                 @drop.prevent="handleDrop($event,'national_id')">
                                <input type="file" @change="handleFile($event,'national_id')" accept=".jpg,.jpeg,.png,.pdf">
                                <template x-if="!files.national_id">
                                    <div>
                                        <div class="upload-icon">🪪</div>
                                        <div class="upload-title">Click to upload or drag & drop</div>
                                        <div class="upload-hint">National ID (front & back) or Passport bio page</div>
                                    </div>
                                </template>
                                <template x-if="files.national_id">
                                    <div>
                                        <div class="upload-icon">✅</div>
                                        <div class="upload-title" x-text="files.national_id.name"></div>
                                        <div class="upload-hint" x-text="formatSize(files.national_id.size)"></div>
                                        <span class="upload-badge" style="background:rgba(16,185,129,.1);color:var(--mint);"><i class="fa-solid fa-check fa-xs"></i> Ready to upload</span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Degree / Academic --}}
                        <div>
                            <div class="flabel"><i class="fa-solid fa-graduation-cap"></i> Degree Certificate <span style="color:var(--coral);">*</span></div>
                            <div class="upload-zone" :class="files.degree_certificate ? 'has-file' : ''"
                                 @dragover.prevent="$el.classList.add('drag')"
                                 @dragleave="$el.classList.remove('drag')"
                                 @drop.prevent="handleDrop($event,'degree_certificate')">
                                <input type="file" @change="handleFile($event,'degree_certificate')" accept=".jpg,.jpeg,.png,.pdf">
                                <template x-if="!files.degree_certificate">
                                    <div>
                                        <div class="upload-icon">🎓</div>
                                        <div class="upload-title">Click to upload or drag & drop</div>
                                        <div class="upload-hint">Bachelor's degree or higher academic qualification</div>
                                    </div>
                                </template>
                                <template x-if="files.degree_certificate">
                                    <div>
                                        <div class="upload-icon">✅</div>
                                        <div class="upload-title" x-text="files.degree_certificate.name"></div>
                                        <div class="upload-hint" x-text="formatSize(files.degree_certificate.size)"></div>
                                        <span class="upload-badge" style="background:rgba(16,185,129,.1);color:var(--mint);"><i class="fa-solid fa-check fa-xs"></i> Ready to upload</span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- TSC Certificate --}}
                        <div>
                            <div class="flabel"><i class="fa-solid fa-certificate"></i> TSC Certificate <span style="color:var(--coral);">*</span></div>
                            <div class="upload-zone" :class="files.tsc_certificate ? 'has-file' : ''"
                                 @dragover.prevent="$el.classList.add('drag')"
                                 @dragleave="$el.classList.remove('drag')"
                                 @drop.prevent="handleDrop($event,'tsc_certificate')">
                                <input type="file" @change="handleFile($event,'tsc_certificate')" accept=".jpg,.jpeg,.png,.pdf">
                                <template x-if="!files.tsc_certificate">
                                    <div>
                                        <div class="upload-icon">📜</div>
                                        <div class="upload-title">Click to upload or drag & drop</div>
                                        <div class="upload-hint">Teachers Service Commission registration certificate</div>
                                    </div>
                                </template>
                                <template x-if="files.tsc_certificate">
                                    <div>
                                        <div class="upload-icon">✅</div>
                                        <div class="upload-title" x-text="files.tsc_certificate.name"></div>
                                        <div class="upload-hint" x-text="formatSize(files.tsc_certificate.size)"></div>
                                        <span class="upload-badge" style="background:rgba(16,185,129,.1);color:var(--mint);"><i class="fa-solid fa-check fa-xs"></i> Ready to upload</span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Certificate of Good Conduct --}}
                        <div>
                            <div class="flabel"><i class="fa-solid fa-shield-halved"></i> Certificate of Good Conduct <span style="color:var(--coral);">*</span></div>
                            <div class="upload-zone" :class="files.certificate_of_good_conduct ? 'has-file' : ''"
                                 @dragover.prevent="$el.classList.add('drag')"
                                 @dragleave="$el.classList.remove('drag')"
                                 @drop.prevent="handleDrop($event,'certificate_of_good_conduct')">
                                <input type="file" @change="handleFile($event,'certificate_of_good_conduct')" accept=".jpg,.jpeg,.png,.pdf">
                                <template x-if="!files.certificate_of_good_conduct">
                                    <div>
                                        <div class="upload-icon">🛡️</div>
                                        <div class="upload-title">Click to upload or drag & drop</div>
                                        <div class="upload-hint">DCI Certificate of Good Conduct (must be current)</div>
                                    </div>
                                </template>
                                <template x-if="files.certificate_of_good_conduct">
                                    <div>
                                        <div class="upload-icon">✅</div>
                                        <div class="upload-title" x-text="files.certificate_of_good_conduct.name"></div>
                                        <div class="upload-hint" x-text="formatSize(files.certificate_of_good_conduct.size)"></div>
                                        <span class="upload-badge" style="background:rgba(16,185,129,.1);color:var(--mint);"><i class="fa-solid fa-check fa-xs"></i> Ready to upload</span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Passport Photo --}}
                        <div>
                            <div class="flabel"><i class="fa-solid fa-camera"></i> Passport Size Photo <span style="color:var(--coral);">*</span></div>
                            <div class="upload-zone" :class="files.passport_photo ? 'has-file' : ''"
                                 @dragover.prevent="$el.classList.add('drag')"
                                 @dragleave="$el.classList.remove('drag')"
                                 @drop.prevent="handleDrop($event,'passport_photo')">
                                <input type="file" @change="handleFile($event,'passport_photo')" accept=".jpg,.jpeg,.png">
                                <template x-if="!files.passport_photo">
                                    <div>
                                        <div class="upload-icon">📷</div>
                                        <div class="upload-title">Click to upload or drag & drop</div>
                                        <div class="upload-hint">Recent clear photo, white background preferred</div>
                                    </div>
                                </template>
                                <template x-if="files.passport_photo">
                                    <div>
                                        <div class="upload-icon">✅</div>
                                        <div class="upload-title" x-text="files.passport_photo.name"></div>
                                        <div class="upload-hint" x-text="formatSize(files.passport_photo.size)"></div>
                                        <span class="upload-badge" style="background:rgba(16,185,129,.1);color:var(--mint);"><i class="fa-solid fa-check fa-xs"></i> Ready to upload</span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <p class="ferr" x-show="errors.documents" x-text="errors.documents" style="margin-top:10px;"></p>
                </div>
                <div style="display:flex;gap:12px;">
                    <button class="btn-back" @click="step=2"><i class="fa-solid fa-arrow-left fa-xs"></i> Back</button>
                    <button class="btn-next" @click="validateStep3()">Continue <i class="fa-solid fa-arrow-right fa-sm"></i></button>
                </div>
            </div>

            {{-- ── STEP 4: Verification ── --}}
            <div x-show="step === 4" x-cloak>
                <div class="ob-card">
                    <h2><i class="fa-solid fa-clock-rotate-left" style="color:var(--Trust Blue);font-size:18px;margin-right:8px;"></i>Choose Verification Speed</h2>
                    <p class="sub">Your documents will be reviewed by our academic team. Choose how quickly you want to get started.</p>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:24px;" class="ver-g">
                        {{-- Standard --}}
                        <div style="border:2px solid rgba(21,40,31,.12);border-radius:18px;padding:22px;cursor:pointer;transition:all .2s;"
                             :class="form.speedy ? '' : 'selected-ver'"
                             :style="!form.speedy ? 'border-color:#0A64BC;background:rgba(10,100,188,.04);' : ''"
                             @click="form.speedy=false">
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
                                <div style="width:18px;height:18px;border-radius:50%;border:2px solid;" :style="!form.speedy ? 'border-color:#0A64BC;background:#0A64BC;' : 'border-color:rgba(21,40,31,.3);'">
                                    <div x-show="!form.speedy" style="width:100%;height:100%;border-radius:50%;background:#fff;transform:scale(.45);"></div>
                                </div>
                                <span style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;color:var(--ink);">Standard</span>
                            </div>
                            <div style="font-family:'Fraunces',serif;font-size:28px;font-weight:800;color:var(--mint);margin-bottom:6px;">Free</div>
                            <div style="font-size:13px;font-weight:700;color:var(--ink);margin-bottom:6px;"><i class="fa-solid fa-clock fa-xs" style="color:var(--muted);margin-right:4px;"></i> Up to 3 working days</div>
                            <p style="font-size:13px;color:var(--muted);line-height:1.6;">Our team reviews your documents and verifies your credentials within 3 business days.</p>
                        </div>

                        {{-- Speedy --}}
                        <div class="speedy-card" style="cursor:pointer;transition:all .2s;"
                             :style="form.speedy ? 'border-color:var(--gold);' : 'border-color:rgba(212,160,23,.4);'"
                             @click="form.speedy=true">
                            <div style="position:relative;z-index:1;">
                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                                    <div style="width:18px;height:18px;border-radius:50%;border:2px solid;" :style="form.speedy ? 'border-color:var(--gold);background:var(--gold);' : 'border-color:rgba(21,40,31,.3);'">
                                        <div x-show="form.speedy" style="width:100%;height:100%;border-radius:50%;background:#fff;transform:scale(.45);"></div>
                                    </div>
                                    <span class="speedy-badge"><i class="fa-solid fa-bolt fa-xs"></i> Priority Queue</span>
                                </div>
                                <div style="font-family:'Fraunces',serif;font-size:28px;font-weight:800;color:var(--ink);margin-bottom:6px;">KSh 1,000</div>
                                <div style="font-size:13px;font-weight:700;color:var(--ink);margin-bottom:6px;"><i class="fa-solid fa-rocket fa-xs" style="color:var(--gold);margin-right:4px;"></i> Within 24 hours</div>
                                <p style="font-size:13px;color:rgba(21,40,31,.65);line-height:1.6;">Skip the queue. Your application is prioritised and reviewed within 24 hours by a senior verifier.</p>
                            </div>
                        </div>
                    </div>
                    <style>.ver-g{grid-template-columns:1fr!important;}@media(min-width:540px){.ver-g{grid-template-columns:1fr 1fr!important;}}</style>

                    {{-- Speedy payment form --}}
                    <div x-show="form.speedy" x-cloak style="background:rgba(212,160,23,.08);border:1px solid rgba(212,160,23,.3);border-radius:16px;padding:20px;margin-bottom:0;">
                        <p style="font-size:13px;font-weight:700;color:var(--ink);margin-bottom:10px;display:flex;align-items:center;gap:6px;">
                            <i class="fa-solid fa-credit-card" style="color:var(--gold);"></i>
                            Pay KSh 1,000 via M-Pesa or card to activate priority verification
                        </p>
                        <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:10px;">
                            @foreach(['M-Pesa','Visa','Mastercard'] as $pm)
                            <span style="background:#fff;border:1px solid rgba(21,40,31,.12);border-radius:8px;padding:5px 12px;font-size:12px;font-weight:600;color:var(--ink);display:flex;align-items:center;gap:5px;">
                                <i class="fa-solid {{ $pm==='M-Pesa'?'fa-mobile-screen-button':'fa-credit-card' }}" style="color:var(--gold);font-size:11px;"></i>{{ $pm }}
                            </span>
                            @endforeach
                        </div>
                        <p style="font-size:12px;color:rgba(21,40,31,.6);">Payment will be processed after you submit your application via Paystack.</p>
                    </div>

                    {{-- What happens next --}}
                    <div style="margin-top:18px;padding:16px;background:#F9FAF9;border-radius:14px;">
                        <p style="font-size:12px;font-weight:700;color:var(--ink);margin-bottom:10px;display:flex;align-items:center;gap:6px;"><i class="fa-solid fa-list-check" style="color:var(--Trust Blue);"></i> What happens after submission</p>
                        @foreach(['We review your profile, subjects and all uploaded documents','You receive an email when approved (or if action is required)','Your profile goes live and students can book sessions with you','Earnings are credited to your wallet after each completed session'] as $item)
                        <div style="display:flex;align-items:flex-start;gap:8px;font-size:13px;color:rgba(21,40,31,.7);margin-bottom:7px;">
                            <i class="fa-solid fa-circle-check" style="color:var(--mint);margin-top:1px;flex-shrink:0;font-size:11px;"></i>{{ $item }}
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Upload progress --}}
                <div x-show="submitting" style="background:#fff;border-radius:16px;border:1px solid rgba(21,40,31,.1);padding:20px;margin-bottom:16px;text-align:center;">
                    <div style="width:48px;height:48px;border:3px solid rgba(10,100,188,.2);border-top-color:var(--Trust Blue);border-radius:50%;animation:spin 1s linear infinite;margin:0 auto 12px;"></div>
                    <p style="font-size:14px;font-weight:600;color:var(--ink);" x-text="uploadStatus"></p>
                    <p style="font-size:12px;color:var(--muted);margin-top:4px;">Please don't close this page</p>
                </div>

                <div style="display:flex;gap:12px;" x-show="!submitting">
                    <button class="btn-back" @click="step=3"><i class="fa-solid fa-arrow-left fa-xs"></i> Back</button>
                    <button class="btn-next" @click="submitApplication()" :disabled="submitting">
                        <i class="fa-solid fa-paper-plane fa-sm" x-show="!submitting"></i>
                        <span x-text="form.speedy ? 'Submit & Pay KSh 1,000' : 'Submit Application'"></span>
                    </button>
                </div>
            </div>

            {{-- ── STEP 5: Success ── --}}
            <div x-show="step === 5" x-cloak>
                <div class="ob-card" style="text-align:center;padding:48px 32px;">
                    <div style="width:72px;height:72px;border-radius:50%;background:rgba(16,185,129,.12);display:flex;align-items:center;justify-content:center;margin:0 auto 18px;">
                        <i class="fa-solid fa-circle-check" style="font-size:32px;color:var(--mint);"></i>
                    </div>
                    <h2 style="margin-bottom:8px;">Application Submitted!</h2>
                    <p style="font-size:15px;color:var(--muted);max-width:440px;margin:0 auto 28px;line-height:1.75;">
                        Thank you for applying. Our team will review your documents and profile.
                        <strong style="color:var(--ink);" x-text="form.speedy ? 'You\'ll hear from us within 24 hours.' : 'Verification takes up to 3 working days.'"></strong>
                    </p>

                    {{-- Timeline --}}
                    <div style="background:#F9FAF9;border-radius:16px;padding:20px;text-align:left;margin-bottom:24px;">
                        <p style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:14px;">What happens next</p>
                        <div style="display:flex;flex-direction:column;gap:12px;">
                            @foreach([
                                ['fa-magnifying-glass','#0A64BC','Documents reviewed','Our academic team checks all your uploaded documents'],
                                ['fa-envelope','#10B981','Email notification','You\'ll receive an email with your approval or any actions needed'],
                                ['fa-globe','#E8522A','Profile goes live','Students can find and book sessions with you immediately'],
                                ['fa-money-bill-wave','#EEBE15','Start earning','Accept bookings and earn per completed session'],
                            ] as [$ic,$col,$title,$desc])
                            <div style="display:flex;align-items:flex-start;gap:12px;">
                                <div style="width:32px;height:32px;border-radius:10px;background:{{ $col }}18;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fa-solid {{ $ic }}" style="color:{{ $col }};font-size:13px;"></i>
                                </div>
                                <div>
                                    <div style="font-size:13px;font-weight:600;color:var(--ink);">{{ $title }}</div>
                                    <div style="font-size:12px;color:var(--muted);margin-top:1px;">{{ $desc }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <a href="{{ route('tutor.dashboard') }}"
                       style="display:inline-flex;align-items:center;gap:8px;padding:13px 32px;border-radius:999px;background:var(--Trust Blue);color:#fff;font-size:14px;font-weight:700;text-decoration:none;transition:all .18s;"
                       onmouseover="this.style.background='#120A8F'" onmouseout="this.style.background='var(--Trust Blue)'">
                        <i class="fa-solid fa-gauge-high fa-sm"></i> Go to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes spin{to{transform:rotate(360deg)}}
</style>
<script>
function tutorOnboarding() {
    return {
        step: 1,
        submitting: false,
        uploadStatus: 'Uploading documents…',
        form: {
            name:            '{{ auth()->user()?->name ?? '' }}',
            phone:           '{{ auth()->user()?->phone ?? '' }}',
            city:            '',
            experience_years:'',
            levels:          [],
            bio:             '',
            hourly_rate:     '',
            subjects:        [],
            speedy:          false,
        },
        files: {
            national_id: null,
            degree_certificate: null,
            tsc_certificate: null,
            certificate_of_good_conduct: null,
            passport_photo: null,
        },
        errors: {},

        init() {},

        toggleLevel(level) {
            const i = this.form.levels.indexOf(level);
            if (i > -1) this.form.levels.splice(i, 1);
            else this.form.levels.push(level);
        },

        toggleSubject(id) {
            const i = this.form.subjects.indexOf(id);
            if (i > -1) {
                this.form.subjects.splice(i, 1);
            } else if (this.form.subjects.length < 2) {
                this.form.subjects.push(id);
            }
        },

        handleFile(event, field) {
            const file = event.target.files[0];
            if (!file) return;
            if (file.size > 5 * 1024 * 1024) {
                alert('File too large. Maximum 5MB allowed.');
                return;
            }
            this.files[field] = file;
        },

        handleDrop(event, field) {
            event.currentTarget.classList.remove('drag');
            const file = event.dataTransfer.files[0];
            if (!file) return;
            if (file.size > 5 * 1024 * 1024) { alert('File too large. Max 5MB.'); return; }
            this.files[field] = file;
        },

        formatSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024*1024) return (bytes/1024).toFixed(1) + ' KB';
            return (bytes/(1024*1024)).toFixed(1) + ' MB';
        },

        validateStep1() {
            this.errors = {};
            if (!this.form.name.trim()) { this.errors.name = 'Full name is required'; return; }
            if (!this.form.bio.trim() || this.form.bio.length < 50) { this.errors.bio = 'Bio must be at least 50 characters'; return; }
            if (!this.form.hourly_rate || parseInt(this.form.hourly_rate) < 500) { this.errors.hourly_rate = 'Minimum hourly rate is KSh 500'; return; }
            this.step = 2;
        },

        validateStep2() {
            this.errors = {};
            if (this.form.subjects.length === 0) { this.errors.subjects = 'Please select at least 1 subject'; return; }
            this.step = 3;
        },

        validateStep3() {
            this.errors = {};
            const required = ['national_id','degree_certificate','tsc_certificate','certificate_of_good_conduct','passport_photo'];
            const missing = required.filter(f => !this.files[f]);
            if (missing.length > 0) {
                this.errors.documents = 'Please upload all required documents (' + missing.join(', ').replace(/_/g,' ') + ')';
                return;
            }
            this.step = 4;
        },

        async submitApplication() {
            this.submitting = true;
            this.uploadStatus = 'Uploading documents…';

            try {
                const fd = new FormData();
                // Profile data
                Object.entries(this.form).forEach(([k,v]) => {
                    if (Array.isArray(v)) v.forEach(i => fd.append(k+'[]', i));
                    else fd.append(k, v);
                });
                // Files
                Object.entries(this.files).forEach(([k,v]) => {
                    if (v) fd.append('doc_'+k, v, v.name);
                });
                fd.append('_token', document.querySelector('meta[name=csrf-token]').content);

                this.uploadStatus = 'Saving profile…';
                const res = await fetch('/tutor/onboarding/submit', {
                    method: 'POST',
                    body: fd,
                });
                const data = await res.json();

                if (data.status === 'ok') {
                    this.submitting = false;
                    if (this.form.speedy && data.paystack_access_code) {
                        // Open Paystack for speedy fee
                        const handler = PaystackPop.setup({
                            key: '{{ config('paystack.public_key') }}',
                            email: '{{ auth()->user()?->email }}',
                            amount: 100000, // KSh 1000 in cents
                            currency: 'KES',
                            ref: data.reference,
                            metadata: { purpose: 'speedy_verification', tutor_id: data.tutor_id },
                            onSuccess: () => { this.step = 5; },
                            onCancel: () => { this.step = 5; } // Still submitted, just not speedy
                        });
                        handler.openIframe();
                    } else {
                        this.step = 5;
                    }
                } else {
                    this.submitting = false;
                    alert(data.message || 'Submission failed. Please try again.');
                }
            } catch (e) {
                this.submitting = false;
                console.error(e);
                alert('An error occurred. Please try again.');
            }
        }
    }
}
</script>
<script src="https://js.paystack.co/v1/inline.js"></script>
@livewireScripts
</body>
</html>
