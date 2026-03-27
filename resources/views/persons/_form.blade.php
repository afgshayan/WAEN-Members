{{-- Shared form fields — used in both create and edit --}}
<div class="row g-3">

    {{-- First Name --}}
    <div class="col-12 col-md-6">
        <label for="name" class="form-label fw-semibold" style="font-size:.875rem;">
            First Name <span class="text-danger">*</span>
        </label>
        <input type="text" name="name" id="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $person->name ?? '') }}"
               maxlength="100" required autocomplete="given-name"
               placeholder="e.g. John">
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Last Name --}}
    <div class="col-12 col-md-6">
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

    {{-- Province --}}
    <div class="col-12 col-md-6">
        <label for="province" class="form-label fw-semibold" style="font-size:.875rem;">Province / Region</label>
        <input type="text" name="province" id="province"
               class="form-control @error('province') is-invalid @enderror"
               value="{{ old('province', $person->province ?? '') }}"
               maxlength="100" list="province-list" autocomplete="address-level1"
               placeholder="e.g. Kabul">
        <datalist id="province-list">
            @foreach(['Kabul','Herat','Kandahar','Balkh','Nangarhar','Kunduz','Ghazni','Badakhshan','Baghlan','Helmand','Bamyan','Takhar','Jawzjan','Samangan','Faryab','Ghor','Farah','Paktia','Logar','Wardak','Parwan','Kunar','Nuristan','Laghman','Khost','Panjshir','Zabul','Uruzgan','Nimroz','Daikundi','Badghis','Sar-e-Pol'] as $p)
                <option value="{{ $p }}">
            @endforeach
        </datalist>
        @error('province') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Education --}}
    <div class="col-12 col-md-6">
        <label for="education" class="form-label fw-semibold" style="font-size:.875rem;">Education Level</label>
        <input type="text" name="education" id="education"
               class="form-control @error('education') is-invalid @enderror"
               value="{{ old('education', $person->education ?? '') }}"
               maxlength="100" list="education-list"
               placeholder="e.g. Bachelor's Degree">
        <datalist id="education-list">
            @foreach(['Primary','Middle School','High School Diploma','Associate Degree','Bachelor\'s Degree','Master\'s Degree','Doctorate (PhD)','Seminary'] as $e)
                <option value="{{ $e }}">
            @endforeach
        </datalist>
        @error('education') <div class="invalid-feedback">{{ $message }}</div> @enderror
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

    {{-- Phone --}}
    <div class="col-12 col-md-6">
        <label for="phone" class="form-label fw-semibold" style="font-size:.875rem;">Phone Number</label>
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

    {{-- WhatsApp --}}
    <div class="col-12 col-md-6">
        <label for="whatsapp" class="form-label fw-semibold" style="font-size:.875rem;">WhatsApp Number</label>
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

    {{-- Country --}}
    <div class="col-12 col-md-6">
        <label for="country" class="form-label fw-semibold" style="font-size:.875rem;">Country</label>
        <input type="text" name="country" id="country"
               class="form-control @error('country') is-invalid @enderror"
               value="{{ old('country', $person->country ?? '') }}"
               maxlength="100" list="country-list"
               placeholder="e.g. Afghanistan">
        <datalist id="country-list">
            @foreach(['Afghanistan','Albania','Algeria','Argentina','Armenia','Australia','Austria','Azerbaijan','Bahrain','Bangladesh','Belarus','Belgium','Bolivia','Bosnia and Herzegovina','Brazil','Bulgaria','Cambodia','Canada','Chile','China','Colombia','Croatia','Cuba','Cyprus','Czech Republic','Denmark','Ecuador','Egypt','Ethiopia','Finland','France','Georgia','Germany','Ghana','Greece','Hungary','India','Indonesia','Iran','Iraq','Ireland','Israel','Italy','Japan','Jordan','Kazakhstan','Kenya','Kuwait','Kyrgyzstan','Latvia','Lebanon','Libya','Lithuania','Malaysia','Mexico','Moldova','Morocco','Netherlands','New Zealand','Nigeria','North Macedonia','Norway','Oman','Pakistan','Palestine','Peru','Philippines','Poland','Portugal','Qatar','Romania','Russia','Saudi Arabia','Serbia','Singapore','Slovakia','South Africa','South Korea','Spain','Sri Lanka','Sudan','Sweden','Switzerland','Syria','Tajikistan','Thailand','Tunisia','Turkey','Turkmenistan','Ukraine','United Arab Emirates','United Kingdom','United States','Uzbekistan','Venezuela','Vietnam','Yemen'] as $c)
                <option value="{{ $c }}">
            @endforeach
        </datalist>
        @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Gender --}}
    <div class="col-12 col-md-4">
        <label class="form-label fw-semibold" style="font-size:.875rem;">Gender</label>
        <select name="gender" class="form-select @error('gender') is-invalid @enderror">
            <option value="">— Select —</option>
            @foreach(['Male','Female','Other'] as $g)
                <option value="{{ $g }}" {{ old('gender', $person->gender ?? '') === $g ? 'selected' : '' }}>
                    {{ $g }}
                </option>
            @endforeach
        </select>
        @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Event Name --}}
    <div class="col-12 col-md-4">
        <label for="event_name" class="form-label fw-semibold" style="font-size:.875rem;">Event Name</label>
        <input type="text" name="event_name" id="event_name"
               class="form-control @error('event_name') is-invalid @enderror"
               value="{{ old('event_name', $person->event_name ?? '') }}"
               maxlength="150"
               placeholder="e.g. Annual Conference 2025">
        @error('event_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Notes --}}
    <div class="col-12">
        <label for="notes" class="form-label fw-semibold" style="font-size:.875rem;">Notes</label>
        <textarea name="notes" id="notes" rows="3"
                  class="form-control @error('notes') is-invalid @enderror"
                  maxlength="5000"
                  placeholder="Any additional notes about this member …">{{ old('notes', $person->notes ?? '') }}</textarea>
        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

</div>
