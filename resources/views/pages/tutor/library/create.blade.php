<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Upload Resource – Mwalimu</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700;9..144,800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<style>
:root{--ink:#15281F;--cream:#FBF5E6;--trust-blue:#0A64BC;--coral:#E8522A;--mint:#10B981;--muted:rgba(21,40,31,.55);--hair:rgba(21,40,31,.1);--sidebar:#111C16;}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html,body{height:100%;font-family:'DM Sans',sans-serif;color:var(--ink);background:#F2F5F2;}
a{color:inherit;text-decoration:none;}
button{font-family:inherit;cursor:pointer;}
.layout{display:flex;height:100vh;overflow:hidden;}
.sidebar{width:256px;background:var(--sidebar);display:flex;flex-direction:column;flex-shrink:0;}
.sb-logo{padding:22px 18px;border-bottom:1px solid rgba(255,255,255,.06);display:flex;align-items:center;gap:10px;}
.sb-logo-text{font-family:'Fraunces',serif;font-size:19px;font-weight:700;color:#fff;}
.sb-nav{flex:1;padding:10px 8px;overflow-y:auto;}
.sb-section{margin-bottom:18px;}
.sb-label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.28);padding:0 10px;margin-bottom:5px;}
.sb-item{display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:10px;font-size:13.5px;font-weight:500;color:rgba(255,255,255,.58);transition:all .15s;margin-bottom:1px;cursor:pointer;border:none;background:none;width:100%;text-align:left;text-decoration:none;}
.sb-item:hover{background:rgba(255,255,255,.07);color:#fff;}
.sb-item.active{background:var(--trust-blue);color:#fff;}
.sb-footer{padding:14px;border-top:1px solid rgba(255,255,255,.06);}
.sb-user{display:flex;align-items:center;gap:10px;}
.sb-avatar{width:34px;height:34px;border-radius:10px;object-fit:cover;flex-shrink:0;}
.sb-uname{font-size:13px;font-weight:600;color:#fff;}
.sb-urole{font-size:11px;color:rgba(255,255,255,.35);}
.sb-logout{width:28px;height:28px;border-radius:8px;border:none;background:rgba(255,255,255,.08);color:rgba(255,255,255,.45);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.main{flex:1;display:flex;flex-direction:column;overflow:hidden;}
.topbar{height:60px;background:#fff;border-bottom:1px solid var(--hair);display:flex;align-items:center;justify-content:space-between;padding:0 24px;flex-shrink:0;}
.page-title{font-family:'Fraunces',serif;font-size:18px;font-weight:700;}
.content{flex:1;overflow-y:auto;padding:24px;}
.card{background:#fff;border-radius:16px;border:1px solid var(--hair);}
.card-p{padding:24px;}
.btn{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:10px;font-size:13px;font-weight:600;border:none;cursor:pointer;transition:all .15s;font-family:inherit;text-decoration:none;}
.btn-v{background:var(--trust-blue);color:#fff;}.btn-v:hover{background:#0850a0;}
.btn-o{background:#fff;color:var(--ink);border:1.5px solid var(--hair);}.btn-o:hover{border-color:var(--trust-blue);color:var(--trust-blue);}
.form-group{margin-bottom:20px;}
.form-label{display:block;font-size:13px;font-weight:700;color:var(--ink);margin-bottom:6px;}
.form-label span{color:var(--coral);margin-left:2px;}
.form-input{width:100%;height:44px;padding:0 14px;border:1.5px solid var(--hair);border-radius:10px;font-size:14px;font-family:inherit;color:var(--ink);background:#fff;transition:border-color .15s;outline:none;}
.form-input:focus{border-color:var(--trust-blue);}
textarea.form-input{height:auto;padding:12px 14px;resize:vertical;}
select.form-input{cursor:pointer;}
.form-hint{font-size:12px;color:var(--muted);margin-top:5px;}
.form-error{font-size:12px;color:var(--coral);margin-top:4px;font-weight:500;}
.type-selector{display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:10px;margin-top:8px;}
.type-option{border:2px solid var(--hair);border-radius:12px;padding:14px 12px;text-align:center;cursor:pointer;transition:all .15s;}
.type-option:hover{border-color:var(--trust-blue);}
.type-option.selected{border-color:var(--trust-blue);background:rgba(10,100,188,.06);}
.type-option input{display:none;}
.type-icon{font-size:28px;display:block;margin-bottom:6px;}
.type-name{font-size:13px;font-weight:600;color:var(--ink);}
.type-desc{font-size:11px;color:var(--muted);margin-top:2px;}
.upload-zone{border:2px dashed var(--hair);border-radius:12px;padding:32px;text-align:center;cursor:pointer;transition:all .2s;position:relative;}
.upload-zone:hover,.upload-zone.drag-over{border-color:var(--trust-blue);background:rgba(10,100,188,.04);}
.upload-zone input{position:absolute;inset:0;opacity:0;cursor:pointer;}
.flash{padding:12px 16px;border-radius:12px;font-size:13.5px;font-weight:500;margin-bottom:18px;display:flex;align-items:center;gap:8px;}
.flash-err{background:#FEE2E2;color:#991B1B;border:1px solid #FECACA;}
@media(max-width:1023px){.sidebar{display:none;}}
</style>
</head>
<body>
<div class="layout" x-data="{ resourceType: 'article', hasFile: false }">
<aside class="sidebar">
    <div class="sb-logo">
        <svg width="30" height="30" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="#FBF5E6"/><circle cx="50" cy="50" r="34" fill="#0A64BC"/><circle cx="38" cy="38" r="6" fill="#FBF5E6"/></svg>
        <div><div class="sb-logo-text">Mwalimu</div><div style="font-size:10px;color:rgba(255,255,255,.3);">Tutor Portal</div></div>
    </div>
    <nav class="sb-nav">
        <div class="sb-section">
            <div class="sb-label">Tutor Tools</div>
            <a href="{{ route('tutor.dashboard') }}" class="sb-item"><svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>Dashboard</a>
            <a href="{{ route('tutor.library.index') }}" class="sb-item"><svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>My Resources</a>
            <a href="{{ route('tutor.library.create') }}" class="sb-item active"><svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>Upload Resource</a>
        </div>
    </nav>
    <div class="sb-footer">
        <div class="sb-user">
            <img src="{{ auth()->user()->avatar_url }}" class="sb-avatar" alt="">
            <div style="flex:1;min-width:0;"><div class="sb-uname">{{ auth()->user()->name }}</div><div class="sb-urole">Tutor</div></div>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button type="submit" class="sb-logout"><svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg></button>
            </form>
        </div>
    </div>
</aside>

<div class="main">
    <header class="topbar">
        <div style="display:flex;align-items:center;gap:12px;">
            <a href="{{ route('tutor.library.index') }}" style="color:var(--muted);">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            </a>
            <div class="page-title">Upload Resource</div>
        </div>
    </header>

    <div class="content">
        @if($errors->any())
        <div class="flash flash-err">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <div>
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('tutor.library.store') }}" enctype="multipart/form-data" style="max-width:800px;">
            @csrf

            {{-- Resource type selector --}}
            <div class="card card-p" style="margin-bottom:20px;">
                <div class="form-label" style="margin-bottom:12px;">Resource Type <span>*</span></div>
                <div class="type-selector">
                    @php
                        $types = [
                            'article' => ['📄','Article','Written guide or tutorial'],
                            'pdf'     => ['📕','PDF','PDF document or eBook'],
                            'video'   => ['🎥','Video','Lesson video file'],
                            'notes'   => ['📝','Notes','Study notes or summaries'],
                            'past_paper' => ['📋','Past Paper','Exam past paper'],
                        ];
                    @endphp
                    @foreach($types as $value => [$icon, $label, $desc])
                    <label class="type-option" :class="resourceType === '{{ $value }}' ? 'selected' : ''" @click="resourceType = '{{ $value }}'">
                        <input type="radio" name="resource_type" value="{{ $value }}" x-model="resourceType" {{ old('resource_type','article') === $value ? 'checked' : '' }}>
                        <span class="type-icon">{{ $icon }}</span>
                        <div class="type-name">{{ $label }}</div>
                        <div class="type-desc">{{ $desc }}</div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="card card-p" style="margin-bottom:20px;">
                <h3 style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;margin-bottom:20px;">Resource Details</h3>

                <div class="form-group">
                    <label class="form-label">Title <span>*</span></label>
                    <input type="text" name="title" class="form-input" required value="{{ old('title') }}" placeholder="e.g. Form 4 Mathematics Past Paper 2023">
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;" class="two-col">
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <select name="blog_category_id" class="form-input">
                            <option value="">Select category…</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('blog_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Subject</label>
                        <select name="subject_id" class="form-input">
                            <option value="">Any subject…</option>
                            @foreach($subjects as $subj)
                            <option value="{{ $subj->id }}" {{ old('subject_id') == $subj->id ? 'selected' : '' }}>{{ $subj->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <style>.two-col{grid-template-columns:1fr!important;}@media(min-width:500px){.two-col{grid-template-columns:1fr 1fr!important;}}</style>

                <div class="form-group">
                    <label class="form-label">Grade Level</label>
                    <select name="grade_level" class="form-input">
                        <option value="">All Levels</option>
                        @foreach(['Form 1','Form 2','Form 3','Form 4','Primary','University','All Levels'] as $g)
                        <option value="{{ $g }}" {{ old('grade_level') === $g ? 'selected' : '' }}>{{ $g }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Summary / Excerpt</label>
                    <textarea name="excerpt" class="form-input" rows="3" placeholder="Brief description of what this resource covers…">{{ old('excerpt') }}</textarea>
                    <div class="form-hint">Shown on the library listing page. Keep it concise.</div>
                </div>

                {{-- Content only shown for articles/notes --}}
                <div class="form-group" x-show="resourceType === 'article' || resourceType === 'notes'" style="display:none;" x-cloak>
                    <label class="form-label">Full Content</label>
                    <textarea name="content" class="form-input" rows="10" placeholder="Write the full article, study notes, or explanation here…">{{ old('content') }}</textarea>
                    <div class="form-hint">HTML is supported for rich formatting.</div>
                </div>
            </div>

            {{-- File upload --}}
            <div class="card card-p" style="margin-bottom:20px;" x-show="resourceType !== 'article'">
                <h3 style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;margin-bottom:16px;">Upload File</h3>
                <div class="upload-zone" @dragover.prevent="$el.classList.add('drag-over')" @dragleave="$el.classList.remove('drag-over')" @drop.prevent="$el.classList.remove('drag-over');hasFile=true">
                    <input type="file" name="file" @change="hasFile=true" accept=".pdf,.doc,.docx,.ppt,.pptx,.mp4,.avi,.mov,.mkv">
                    <div x-show="!hasFile">
                        <div style="font-size:40px;margin-bottom:10px;">📎</div>
                        <p style="font-weight:600;font-size:14px;color:var(--ink);margin-bottom:4px;">Drop file here or click to browse</p>
                        <p style="font-size:12px;color:var(--muted);">PDF, DOC, PPT, MP4, AVI — max 50 MB</p>
                    </div>
                    <div x-show="hasFile" style="display:none;">
                        <div style="font-size:40px;margin-bottom:10px;">✅</div>
                        <p style="font-weight:600;font-size:14px;color:var(--mint);">File selected</p>
                    </div>
                </div>
            </div>

            {{-- Thumbnail --}}
            <div class="card card-p" style="margin-bottom:24px;">
                <h3 style="font-family:'Fraunces',serif;font-size:17px;font-weight:700;margin-bottom:16px;">Cover Image (optional)</h3>
                <div class="upload-zone">
                    <input type="file" name="thumbnail" accept="image/*">
                    <div style="font-size:32px;margin-bottom:8px;">🖼️</div>
                    <p style="font-weight:600;font-size:14px;color:var(--ink);margin-bottom:4px;">Upload a cover image</p>
                    <p style="font-size:12px;color:var(--muted);">JPG, PNG, WebP — max 5 MB. Recommended: 800×500px</p>
                </div>
            </div>

            <div style="display:flex;align-items:center;gap:12px;">
                <button type="submit" class="btn btn-v" style="padding:12px 28px;font-size:15px;">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    Publish Resource
                </button>
                <a href="{{ route('tutor.library.index') }}" class="btn btn-o">Cancel</a>
            </div>

            <div style="margin-top:16px;padding:14px 16px;background:rgba(10,100,188,.06);border-radius:12px;font-size:13px;color:var(--muted);">
                💡 Resources go live immediately. Ensure content is appropriate and accurate — the admin team may remove resources that violate guidelines.
            </div>
        </form>
    </div>
</div>
</div>
</body>
</html>
