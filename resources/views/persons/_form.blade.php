{{-- Shared form fields — used in both create and edit --}}
<div class="row g-3">

    {{-- ── Personal Information ── --}}
    <div class="col-12">
        <h6 class="fw-bold text-primary mb-0"><i class="bi bi-person me-1"></i> Personal Information</h6>
        <hr class="mt-1 mb-0" style="border-color:#e5e7eb;">
    </div>

    {{-- First Name --}}
    <div class="col-12 col-md-4">
        <label for="first_name" class="form-label fw-semibold" style="font-size:.875rem;">
            First Name <span class="text-danger">*</span>
        </label>
        <input type="text" name="first_name" id="first_name"
               class="form-control @error('first_name') is-invalid @enderror"
               value="{{ old('first_name', $person->first_name ?? '') }}"
               maxlength="100" required autocomplete="given-name"
               placeholder="e.g. John">
        @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Last Name --}}
    <div class="col-12 col-md-4">
        <label for="last_name" class="form-label fw-semibold" style="font-size:.875rem;">
            Last Name <span class="text-danger">*</span>
        </label>
        <input type="text" name="last_name" id="last_name"
               class="form-control @error('last_name') is-invalid @enderror"
               value="{{ old('last_name', $person->last_name ?? '') }}"
               maxlength="100" required autocomplete="family-name"
               placeholder="e.g. Smith">
        @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Date of Birth --}}
    <div class="col-12 col-md-4">
        <label for="date_of_birth" class="form-label fw-semibold" style="font-size:.875rem;">Date of Birth</label>
        <input type="date" name="date_of_birth" id="date_of_birth"
               class="form-control @error('date_of_birth') is-invalid @enderror"
               value="{{ old('date_of_birth', isset($person) && $person->date_of_birth ? $person->date_of_birth->format('Y-m-d') : '') }}">
        @error('date_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Occupation --}}
    <div class="col-12 col-md-6">
        <label for="occupation" class="form-label fw-semibold" style="font-size:.875rem;">Current Occupation / Organization</label>
        <input type="text" name="occupation" id="occupation"
               class="form-control @error('occupation') is-invalid @enderror"
               value="{{ old('occupation', $person->occupation ?? '') }}"
               maxlength="200" placeholder="e.g. Software Engineer at Google">
        @error('occupation') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Education --}}
    <div class="col-12 col-md-3">
        <label for="education" class="form-label fw-semibold" style="font-size:.875rem;">Education</label>
        <select name="education" id="education"
                class="form-select @error('education') is-invalid @enderror">
            <option value="">— Select —</option>
            @foreach(['High School','Associate\'s Degree','Bachelor\'s Degree','Master\'s Degree','PhD / Doctorate','Professional Degree','Other'] as $edu)
                <option value="{{ $edu }}" {{ old('education', $person->education ?? '') === $edu ? 'selected' : '' }}>{{ $edu }}</option>
            @endforeach
        </select>
        @error('education') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Gender --}}
    <div class="col-12 col-md-3">
        <label for="gender" class="form-label fw-semibold" style="font-size:.875rem;">Gender</label>
        <select name="gender" id="gender"
                class="form-select @error('gender') is-invalid @enderror">
            <option value="">— Select —</option>
            @foreach(['Male','Female','Other'] as $g)
                <option value="{{ $g }}" {{ old('gender', $person->gender ?? '') === $g ? 'selected' : '' }}>{{ $g }}</option>
            @endforeach
        </select>
        @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Headshot --}}
    <div class="col-12 col-md-6">
        <label for="headshot" class="form-label fw-semibold" style="font-size:.875rem;">
            <i class="bi bi-camera me-1"></i>Headshot Photo
        </label>
        @if(isset($person) && $person->headshot)
            <div class="mb-2 d-flex align-items-center gap-2">
                <img src="{{ $person->headshot_url }}" alt="Current headshot"
                     style="width:48px; height:48px; border-radius:50%; object-fit:cover; border:2px solid #e5e7eb;">
                <label class="form-check-label small text-muted">
                    <input type="checkbox" name="headshot_remove" value="1" class="form-check-input me-1">
                    Remove current photo
                </label>
            </div>
        @endif
        <input type="file" name="headshot" id="headshot"
               class="form-control @error('headshot') is-invalid @enderror"
               accept="image/jpeg,image/png,image/webp">
        <small class="text-muted">JPG, PNG or WebP. Max 5 MB.</small>
        @error('headshot') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <div id="headshot-preview" class="mt-2 d-none">
            <div class="d-flex align-items-center gap-2">
                <img id="headshot-preview-img" src="" alt="Preview"
                     style="width:56px; height:56px; border-radius:50%; object-fit:cover; border:2px solid #3b82f6;">
                <div style="min-width:0;">
                    <div id="headshot-preview-name" class="fw-semibold text-truncate" style="font-size:.8rem; max-width:200px;"></div>
                    <div id="headshot-preview-size" class="text-muted" style="font-size:.72rem;"></div>
                </div>
                <i class="bi bi-check-circle-fill text-success" style="font-size:1.1rem;"></i>
            </div>
        </div>
    </div>

    {{-- ── Contact Information ── --}}
    <div class="col-12 mt-3">
        <h6 class="fw-bold text-primary mb-0"><i class="bi bi-envelope me-1"></i> Contact Information</h6>
        <hr class="mt-1 mb-0" style="border-color:#e5e7eb;">
    </div>

    {{-- Email --}}
    <div class="col-12 col-md-6">
        <label for="email" class="form-label fw-semibold" style="font-size:.875rem;">Email Address</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" id="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', $person->email ?? '') }}"
                   maxlength="191" autocomplete="email"
                   placeholder="example@domain.com">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    {{-- WAEN Email --}}
    <div class="col-12 col-md-6">
        <label for="waen_email" class="form-label fw-semibold" style="font-size:.875rem;">WAEN Email Address</label>
        <div class="input-group">
            <span class="input-group-text" style="background:#fff3cd; border-color:#ffc107; color:#856404;">
                <i class="bi bi-envelope-at"></i>
            </span>
            <input type="email" name="waen_email" id="waen_email"
                   class="form-control @error('waen_email') is-invalid @enderror"
                   value="{{ old('waen_email', $person->waen_email ?? '') }}"
                   maxlength="191"
                   placeholder="name@waen.org">
            @error('waen_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    {{-- WhatsApp --}}
    <div class="col-12 col-md-6">
        <label for="whatsapp" class="form-label fw-semibold" style="font-size:.875rem;">WhatsApp</label>
        <div class="input-group">
            <span class="input-group-text" style="background:#dcfce7; border-color:#bbf7d0; color:#16a34a;">
                <i class="bi bi-whatsapp"></i>
            </span>
            <input type="tel" name="whatsapp" id="whatsapp"
                   class="form-control @error('whatsapp') is-invalid @enderror"
                   value="{{ old('whatsapp', $person->whatsapp ?? '') }}"
                   maxlength="30"
                   placeholder="+1 234 567 8900">
            @error('whatsapp') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    {{-- Phone --}}
    <div class="col-12 col-md-6">
        <label for="phone" class="form-label fw-semibold" style="font-size:.875rem;">Phone</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
            <input type="tel" name="phone" id="phone"
                   class="form-control @error('phone') is-invalid @enderror"
                   value="{{ old('phone', $person->phone ?? '') }}"
                   maxlength="30" autocomplete="tel"
                   placeholder="+1 234 567 8900">
            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    {{-- ── Address ── --}}
    <div class="col-12 mt-3">
        <h6 class="fw-bold text-primary mb-0"><i class="bi bi-geo-alt me-1"></i> Address</h6>
        <hr class="mt-1 mb-0" style="border-color:#e5e7eb;">
    </div>

    {{-- Street Address --}}
    <div class="col-12 col-md-8">
        <label for="street_address" class="form-label fw-semibold" style="font-size:.875rem;">Street Address</label>
        <input type="text" name="street_address" id="street_address"
               class="form-control @error('street_address') is-invalid @enderror"
               value="{{ old('street_address', $person->street_address ?? '') }}"
               maxlength="255" placeholder="123 Main Street">
        @error('street_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Apartment --}}
    <div class="col-12 col-md-4">
        <label for="apartment" class="form-label fw-semibold" style="font-size:.875rem;">Apartment, suite, etc.</label>
        <input type="text" name="apartment" id="apartment"
               class="form-control @error('apartment') is-invalid @enderror"
               value="{{ old('apartment', $person->apartment ?? '') }}"
               maxlength="100" placeholder="Apt 4B">
        @error('apartment') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- City --}}
    <div class="col-12 col-md-3">
        <label for="city" class="form-label fw-semibold" style="font-size:.875rem;">City</label>
        <input type="text" name="city" id="city"
               class="form-control @error('city') is-invalid @enderror"
               value="{{ old('city', $person->city ?? '') }}"
               maxlength="100" placeholder="e.g. New York">
        @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- State/Province --}}
    <div class="col-12 col-md-3">
        <label for="state_province" class="form-label fw-semibold" style="font-size:.875rem;">State/Province</label>
        <input type="text" name="state_province" id="state_province"
               class="form-control @error('state_province') is-invalid @enderror"
               value="{{ old('state_province', $person->state_province ?? '') }}"
               maxlength="100" placeholder="e.g. California">
        @error('state_province') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- ZIP Code --}}
    <div class="col-12 col-md-3">
        <label for="zip_code" class="form-label fw-semibold" style="font-size:.875rem;">ZIP / Postal Code</label>
        <input type="text" name="zip_code" id="zip_code"
               class="form-control @error('zip_code') is-invalid @enderror"
               value="{{ old('zip_code', $person->zip_code ?? '') }}"
               maxlength="20" placeholder="10001">
        @error('zip_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Country --}}
    <div class="col-12 col-md-3">
        <label for="country" class="form-label fw-semibold" style="font-size:.875rem;">Country</label>
        <input type="text" name="country" id="country"
               class="form-control @error('country') is-invalid @enderror"
               value="{{ old('country', $person->country ?? '') }}"
               maxlength="100" list="country-list"
               placeholder="e.g. United States">
        <datalist id="country-list">
            @foreach(['Afghanistan','Albania','Algeria','Argentina','Armenia','Australia','Austria','Azerbaijan','Bahrain','Bangladesh','Belarus','Belgium','Bolivia','Bosnia and Herzegovina','Brazil','Bulgaria','Cambodia','Canada','Chile','China','Colombia','Croatia','Cuba','Cyprus','Czech Republic','Denmark','Ecuador','Egypt','Ethiopia','Finland','France','Georgia','Germany','Ghana','Greece','Hungary','India','Indonesia','Iran','Iraq','Ireland','Israel','Italy','Japan','Jordan','Kazakhstan','Kenya','Kuwait','Kyrgyzstan','Latvia','Lebanon','Libya','Lithuania','Malaysia','Mexico','Moldova','Morocco','Netherlands','New Zealand','Nigeria','North Macedonia','Norway','Oman','Pakistan','Palestine','Peru','Philippines','Poland','Portugal','Qatar','Romania','Russia','Saudi Arabia','Serbia','Singapore','Slovakia','South Africa','South Korea','Spain','Sri Lanka','Sudan','Sweden','Switzerland','Syria','Tajikistan','Thailand','Tunisia','Turkey','Turkmenistan','Ukraine','United Arab Emirates','United Kingdom','United States','Uzbekistan','Venezuela','Vietnam','Yemen'] as $c)
                <option value="{{ $c }}">
            @endforeach
        </datalist>
        @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- ── Social Media ── --}}
    <div class="col-12 mt-3">
        <h6 class="fw-bold text-primary mb-0"><i class="bi bi-share me-1"></i> Social Media</h6>
        <hr class="mt-1 mb-0" style="border-color:#e5e7eb;">
    </div>

    {{-- Facebook --}}
    <div class="col-12 col-md-6">
        <label for="facebook" class="form-label fw-semibold" style="font-size:.875rem;">Facebook</label>
        <div class="input-group">
            <span class="input-group-text" style="background:#e8f0fe; color:#1877f2;"><i class="bi bi-facebook"></i></span>
            <input type="url" name="facebook" id="facebook"
                   class="form-control @error('facebook') is-invalid @enderror"
                   value="{{ old('facebook', $person->facebook ?? '') }}"
                   maxlength="255" placeholder="https://facebook.com/username">
            @error('facebook') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    {{-- Instagram --}}
    <div class="col-12 col-md-6">
        <label for="instagram" class="form-label fw-semibold" style="font-size:.875rem;">Instagram</label>
        <div class="input-group">
            <span class="input-group-text" style="background:#fce4ec; color:#e1306c;"><i class="bi bi-instagram"></i></span>
            <input type="url" name="instagram" id="instagram"
                   class="form-control @error('instagram') is-invalid @enderror"
                   value="{{ old('instagram', $person->instagram ?? '') }}"
                   maxlength="255" placeholder="https://instagram.com/username">
            @error('instagram') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    {{-- LinkedIn --}}
    <div class="col-12 col-md-6">
        <label for="linkedin" class="form-label fw-semibold" style="font-size:.875rem;">LinkedIn</label>
        <div class="input-group">
            <span class="input-group-text" style="background:#e8f4fd; color:#0a66c2;"><i class="bi bi-linkedin"></i></span>
            <input type="url" name="linkedin" id="linkedin"
                   class="form-control @error('linkedin') is-invalid @enderror"
                   value="{{ old('linkedin', $person->linkedin ?? '') }}"
                   maxlength="255" placeholder="https://linkedin.com/in/username">
            @error('linkedin') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    {{-- X (Twitter) --}}
    <div class="col-12 col-md-6">
        <label for="twitter" class="form-label fw-semibold" style="font-size:.875rem;">X (Twitter)</label>
        <div class="input-group">
            <span class="input-group-text" style="background:#f0f0f0; color:#000;"><i class="bi bi-twitter-x"></i></span>
            <input type="url" name="twitter" id="twitter"
                   class="form-control @error('twitter') is-invalid @enderror"
                   value="{{ old('twitter', $person->twitter ?? '') }}"
                   maxlength="255" placeholder="https://x.com/username">
            @error('twitter') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    {{-- ── Professional Details ── --}}
    <div class="col-12 mt-3">
        <h6 class="fw-bold text-primary mb-0"><i class="bi bi-briefcase me-1"></i> Professional Details</h6>
        <hr class="mt-1 mb-0" style="border-color:#e5e7eb;">
    </div>

    {{-- Biography --}}
    <div class="col-12">
        <label for="biography" class="form-label fw-semibold" style="font-size:.875rem;">Biography</label>
        <textarea name="biography" id="biography" rows="4"
                  class="form-control @error('biography') is-invalid @enderror"
                  maxlength="5000"
                  placeholder="Brief biography of the member …">{{ old('biography', $person->biography ?? '') }}</textarea>
        @error('biography') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- CV --}}
    <div class="col-12 col-md-6">
        <label for="cv_file" class="form-label fw-semibold" style="font-size:.875rem;">
            <i class="bi bi-file-earmark-pdf me-1"></i>CV / Resume
        </label>
        @if(isset($person) && $person->cv_file)
            <div class="mb-2 d-flex align-items-center gap-2">
                <a href="{{ $person->cv_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-download me-1"></i>Download current CV
                </a>
                <label class="form-check-label small text-muted">
                    <input type="checkbox" name="cv_remove" value="1" class="form-check-input me-1">
                    Remove
                </label>
            </div>
        @endif
        <input type="file" name="cv_file" id="cv_file"
               class="form-control @error('cv_file') is-invalid @enderror"
               accept=".pdf,.doc,.docx">
        <small class="text-muted">PDF, DOC or DOCX. Max 10 MB.</small>
        @error('cv_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <div id="cv-preview" class="mt-2 d-none">
            <div class="d-flex align-items-center gap-2">
                <div style="width:44px; height:44px; background:#f1f5f9; border-radius:10px;
                            display:flex; align-items:center; justify-content:center;">
                    <i class="bi bi-file-earmark-pdf-fill" style="font-size:1.4rem; color:#ef4444;" id="cv-preview-icon"></i>
                </div>
                <div style="min-width:0;">
                    <div id="cv-preview-name" class="fw-semibold text-truncate" style="font-size:.8rem; max-width:200px;"></div>
                    <div id="cv-preview-size" class="text-muted" style="font-size:.72rem;"></div>
                </div>
                <i class="bi bi-check-circle-fill text-success" style="font-size:1.1rem;"></i>
            </div>
        </div>
    </div>

    {{-- Areas of Expertise --}}
    <div class="col-12 col-md-6">
        <label for="areas_of_expertise" class="form-label fw-semibold" style="font-size:.875rem;">Areas of Expertise</label>
        <textarea name="areas_of_expertise" id="areas_of_expertise" rows="3"
                  class="form-control @error('areas_of_expertise') is-invalid @enderror"
                  maxlength="5000"
                  placeholder="e.g. Machine Learning, Public Health, Policy …">{{ old('areas_of_expertise', $person->areas_of_expertise ?? '') }}</textarea>
        @error('areas_of_expertise') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Proposed Initiatives --}}
    <div class="col-12">
        <label for="proposed_initiatives" class="form-label fw-semibold" style="font-size:.875rem;">Proposed Initiatives or Programs</label>
        <textarea name="proposed_initiatives" id="proposed_initiatives" rows="3"
                  class="form-control @error('proposed_initiatives') is-invalid @enderror"
                  maxlength="5000"
                  placeholder="Describe any proposed initiatives or programs …">{{ old('proposed_initiatives', $person->proposed_initiatives ?? '') }}</textarea>
        @error('proposed_initiatives') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

</div>

<script>
(function() {
    function formatSize(bytes) {
        if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
        if (bytes >= 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return bytes + ' B';
    }

    var headshot = document.getElementById('headshot');
    if (headshot) {
        headshot.addEventListener('change', function() {
            var preview = document.getElementById('headshot-preview');
            var img = document.getElementById('headshot-preview-img');
            var nameEl = document.getElementById('headshot-preview-name');
            var sizeEl = document.getElementById('headshot-preview-size');
            if (this.files && this.files[0]) {
                var file = this.files[0];
                var reader = new FileReader();
                reader.onload = function(e) { img.src = e.target.result; };
                reader.readAsDataURL(file);
                nameEl.textContent = file.name;
                sizeEl.textContent = formatSize(file.size);
                preview.classList.remove('d-none');
            } else {
                preview.classList.add('d-none');
            }
        });
    }

    var cvFile = document.getElementById('cv_file');
    if (cvFile) {
        cvFile.addEventListener('change', function() {
            var preview = document.getElementById('cv-preview');
            var nameEl = document.getElementById('cv-preview-name');
            var sizeEl = document.getElementById('cv-preview-size');
            var iconEl = document.getElementById('cv-preview-icon');
            if (this.files && this.files[0]) {
                var file = this.files[0];
                nameEl.textContent = file.name;
                sizeEl.textContent = formatSize(file.size);
                var ext = file.name.split('.').pop().toLowerCase();
                if (ext === 'pdf') {
                    iconEl.className = 'bi bi-file-earmark-pdf-fill';
                    iconEl.style.color = '#ef4444';
                } else {
                    iconEl.className = 'bi bi-file-earmark-word-fill';
                    iconEl.style.color = '#2563eb';
                }
                preview.classList.remove('d-none');
            } else {
                preview.classList.add('d-none');
            }
        });
    }
})();
</script>
