<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('review.page.submit_title') }} — {{ config('app.name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: #f5f5f5; color: #333; min-height: 100vh; }
        .page { max-width: 640px; margin: 0 auto; padding: 24px 16px 64px; }

        /* ── Card ── */
        .card { background: #fff; border-radius: 16px; box-shadow: 0 2px 16px rgba(0,0,0,.08); overflow: hidden; }
        .card-header { background: linear-gradient(135deg, #7c3aed, #4f46e5); padding: 32px 28px; text-align: center; color: #fff; }
        .card-header h1 { font-size: 22px; font-weight: 700; margin-bottom: 6px; }
        .card-header p  { font-size: 14px; opacity: .85; }
        .card-body { padding: 28px; }

        /* ── Late-review banner ── */
        .late-banner { background: #fef3c7; border: 1px solid #fcd34d; border-radius: 10px; padding: 12px 16px; margin-bottom: 20px; font-size: 13px; color: #92400e; }

        /* ── Booking summary ── */
        .booking-summary { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; margin-bottom: 24px; }
        .booking-summary h3 { font-size: 13px; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; margin-bottom: 12px; }
        .summary-row { display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 8px; }
        .summary-row:last-child { margin-bottom: 0; }
        .summary-label { color: #6b7280; }
        .summary-value { font-weight: 600; color: #111827; text-align: end; }

        /* ── Star rating ── */
        .section-title { font-size: 15px; font-weight: 600; margin-bottom: 12px; color: #111827; }
        .stars-wrapper { display: flex; gap: 8px; justify-content: center; margin-bottom: 8px; }
        .star-btn { background: none; border: none; cursor: pointer; font-size: 40px; line-height: 1; transition: transform .1s; padding: 4px; }
        .star-btn:hover, .star-btn.active { transform: scale(1.15); }
        .star-btn .star { color: #d1d5db; transition: color .15s; }
        .star-btn.active .star,
        .star-btn.hover-active .star { color: #f59e0b; }
        .star-labels { display: flex; justify-content: space-between; font-size: 11px; color: #9ca3af; margin-bottom: 20px; }

        /* ── Sub-ratings ── */
        .sub-ratings { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px; }
        .sub-rating label { font-size: 13px; color: #374151; display: block; margin-bottom: 6px; }
        .mini-stars { display: flex; gap: 4px; }
        .mini-star-btn { background: none; border: none; cursor: pointer; font-size: 20px; padding: 2px; }
        .mini-star-btn .star { color: #d1d5db; transition: color .15s; }
        .mini-star-btn.active .star { color: #f59e0b; }

        /* ── Textarea ── */
        .field { margin-bottom: 20px; }
        .field label { font-size: 13px; font-weight: 600; color: #374151; display: block; margin-bottom: 6px; }
        .field textarea, .field input[type="text"] {
            width: 100%; border: 1px solid #d1d5db; border-radius: 8px;
            padding: 10px 12px; font-size: 14px; resize: vertical;
            transition: border-color .15s, box-shadow .15s;
        }
        .field textarea:focus, .field input:focus {
            outline: none; border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,.15);
        }
        .field .hint { font-size: 11px; color: #9ca3af; margin-top: 4px; }

        /* ── Photo upload ── */
        .upload-area { border: 2px dashed #d1d5db; border-radius: 10px; padding: 24px; text-align: center; cursor: pointer; transition: border-color .15s; }
        .upload-area:hover { border-color: #7c3aed; }
        .upload-area input[type="file"] { display: none; }
        .upload-area .icon { font-size: 32px; margin-bottom: 8px; }
        .upload-area p { font-size: 13px; color: #6b7280; }
        .upload-area .btn-link { color: #7c3aed; font-weight: 600; text-decoration: underline; cursor: pointer; }
        .photo-previews { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }
        .photo-preview { width: 72px; height: 72px; border-radius: 8px; object-fit: cover; border: 2px solid #e5e7eb; }

        /* ── Consent ── */
        .consent-row { display: flex; gap: 10px; align-items: flex-start; margin-bottom: 24px; }
        .consent-row input[type="checkbox"] { width: 18px; height: 18px; margin-top: 2px; flex-shrink: 0; accent-color: #7c3aed; cursor: pointer; }
        .consent-row label { font-size: 13px; color: #4b5563; cursor: pointer; }

        /* ── Submit ── */
        .btn-submit {
            width: 100%; padding: 14px; background: linear-gradient(135deg, #7c3aed, #4f46e5);
            color: #fff; font-size: 16px; font-weight: 700; border: none;
            border-radius: 10px; cursor: pointer; transition: opacity .15s;
        }
        .btn-submit:hover { opacity: .9; }
        .btn-submit:disabled { opacity: .5; cursor: not-allowed; }

        /* ── Validation messages ── */
        .error-msg { color: #dc2626; font-size: 12px; margin-top: 4px; }
        .field-error { border-color: #dc2626 !important; }

        /* ── Session messages ── */
        .alert-info { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 14px 16px; font-size: 14px; color: #1d4ed8; margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="page">

    {{-- ─── SESSION FLASH ─── --}}
    @if(session('info'))
        <div class="alert-info">{{ session('info') }}</div>
    @endif

    <div class="card">
        {{-- ─── HEADER ─── --}}
        <div class="card-header">
            <div style="font-size: 48px; margin-bottom: 12px;">⭐</div>
            <h1>{{ __('review.page.submit_heading') }}</h1>
            <p>{{ __('review.page.submit_subheading') }}</p>
        </div>

        <div class="card-body">

            {{-- ─── LATE REVIEW BANNER ─── --}}
            @if($isLate)
                <div class="late-banner">
                    ⏰ {{ __('review.messages.late_review_notice') }}
                </div>
            @endif

            {{-- ─── BOOKING SUMMARY ─── --}}
            @php
                $locale   = app()->getLocale();
                $hallName = is_array($booking->hall->name)
                    ? ($booking->hall->name[$locale] ?? $booking->hall->name['en'] ?? '')
                    : $booking->hall->name;
            @endphp
            <div class="booking-summary">
                <h3>{{ __('review.page.booking_summary') }}</h3>
                <div class="summary-row">
                    <span class="summary-label">{{ __('review.fields.booking') }}</span>
                    <span class="summary-value">{{ $booking->booking_number }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">{{ __('review.fields.hall') }}</span>
                    <span class="summary-value">{{ $hallName }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">{{ __('review.page.event_date') }}</span>
                    <span class="summary-value">{{ $booking->booking_date->format('l, d M Y') }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">{{ __('review.page.time_slot') }}</span>
                    <span class="summary-value">{{ ucfirst(str_replace('_', ' ', $booking->time_slot)) }}</span>
                </div>
            </div>

            {{-- ─── REVIEW FORM ─── --}}
            <form method="POST" action="{{ route('reviews.store') }}" enctype="multipart/form-data" id="reviewForm" novalidate>
                @csrf
                <input type="hidden" name="booking" value="{{ $booking->id }}">
                <input type="hidden" name="token"   value="{{ $token }}">
                <input type="hidden" name="rating"  id="ratingInput" value="{{ old('rating', '') }}">

                {{-- ─── OVERALL STAR RATING ─── --}}
                <div class="field" style="text-align: center;">
                    <div class="section-title">{{ __('review.page.overall_rating') }}</div>
                    <div class="stars-wrapper" role="group" aria-label="{{ __('review.page.overall_rating') }}">
                        @foreach([1 => __('review.page.star_label_1'), 2 => __('review.page.star_label_2'), 3 => __('review.page.star_label_3'), 4 => __('review.page.star_label_4'), 5 => __('review.page.star_label_5')] as $val => $label)
                            <button type="button"
                                    class="star-btn {{ old('rating') >= $val ? 'active' : '' }}"
                                    data-value="{{ $val }}"
                                    aria-label="{{ $label }}"
                                    title="{{ $label }}">
                                <span class="star">★</span>
                            </button>
                        @endforeach
                    </div>
                    <div class="star-labels">
                        <span>{{ __('review.page.star_label_1') }}</span>
                        <span>{{ __('review.page.star_label_5') }}</span>
                    </div>
                    @error('rating')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                </div>

                {{-- ─── OPTIONAL TEXT REVIEW ─── --}}
                <div class="field">
                    <label for="comment">
                        {{ __('review.fields.comment') }}
                        <span id="commentRequired" style="color: #dc2626; display: none;">*</span>
                    </label>
                    <textarea id="comment" name="comment" rows="4"
                              placeholder="{{ __('review.page.comment_placeholder') }}"
                              class="{{ $errors->has('comment') ? 'field-error' : '' }}">{{ old('comment') }}</textarea>
                    <div class="hint" id="commentHint">{{ __('review.page.comment_hint') }}</div>
                    @error('comment')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                </div>

                {{-- ─── DETAILED RATINGS ─── --}}
                <div class="section-title">{{ __('review.sections.detailed_ratings') }}</div>
                <div class="sub-ratings">
                    @foreach([
                        'cleanliness_rating' => __('review.fields.cleanliness_rating'),
                        'service_rating'     => __('review.fields.service_rating'),
                        'value_rating'       => __('review.fields.value_rating'),
                        'location_rating'    => __('review.fields.location_rating'),
                    ] as $field => $label)
                        <div>
                            <label>{{ $label }}</label>
                            <input type="hidden" name="{{ $field }}" id="{{ $field }}Input" value="{{ old($field, '') }}">
                            <div class="mini-stars" data-field="{{ $field }}">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button"
                                            class="mini-star-btn {{ old($field, 0) >= $i ? 'active' : '' }}"
                                            data-value="{{ $i }}">
                                        <span class="star">★</span>
                                    </button>
                                @endfor
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- ─── PHOTO UPLOAD ─── --}}
                <div class="field">
                    <label>{{ __('review.page.photos_label') }}</label>
                    <div class="upload-area" onclick="document.getElementById('photoInput').click()">
                        <input type="file" id="photoInput" name="photos[]"
                               accept="image/jpeg,image/png,image/webp"
                               multiple
                               onchange="handlePhotos(this)">
                        <div class="icon">📷</div>
                        <p>
                            <span class="btn-link">{{ __('review.page.photos_choose') }}</span>
                            {{ __('review.page.photos_or_drag') }}
                        </p>
                        <p style="margin-top: 4px; font-size: 11px;">{{ __('review.page.photos_hint') }}</p>
                    </div>
                    <div class="photo-previews" id="photoPreviews"></div>
                    @error('photos')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                    @error('photos.*')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                </div>

                {{-- ─── MARKETING CONSENT ─── --}}
                <div class="consent-row">
                    <input type="checkbox" id="marketingConsent" name="marketing_consent" value="1"
                           {{ old('marketing_consent') ? 'checked' : '' }}>
                    <label for="marketingConsent">{{ __('review.page.marketing_consent_label') }}</label>
                </div>

                {{-- ─── SUBMIT ─── --}}
                <button type="submit" class="btn-submit" id="submitBtn">
                    {{ __('review.page.submit_button') }}
                </button>
            </form>

        </div>{{-- /card-body --}}
    </div>{{-- /card --}}

    <p style="text-align: center; font-size: 12px; color: #9ca3af; margin-top: 24px;">
        {{ __('review.page.powered_by', ['app' => config('app.name')]) }}
    </p>

</div>{{-- /page --}}

<script>
// ── Overall star rating ──────────────────────────────────────────────────────
const starBtns   = document.querySelectorAll('.star-btn');
const ratingInput = document.getElementById('ratingInput');
const commentHint = document.getElementById('commentHint');
const commentRequired = document.getElementById('commentRequired');

starBtns.forEach(btn => {
    btn.addEventListener('mouseenter', () => highlightStars(parseInt(btn.dataset.value)));
    btn.addEventListener('mouseleave', () => highlightStars(parseInt(ratingInput.value) || 0));
    btn.addEventListener('click', () => {
        ratingInput.value = btn.dataset.value;
        highlightStars(parseInt(btn.dataset.value));
        toggleCommentRequirement(parseInt(btn.dataset.value));
    });
});

function highlightStars(upTo) {
    starBtns.forEach(b => {
        const v = parseInt(b.dataset.value);
        b.classList.toggle('active', v <= upTo);
        b.classList.toggle('hover-active', false);
    });
}

function toggleCommentRequirement(rating) {
    const needsComment = rating > 0 && rating <= 3;
    commentRequired.style.display = needsComment ? 'inline' : 'none';
    commentHint.textContent = needsComment
        ? '{{ __('review.page.comment_required_hint') }}'
        : '{{ __('review.page.comment_hint') }}';
}

// Restore on page load if validation failed
if (ratingInput.value) {
    highlightStars(parseInt(ratingInput.value));
    toggleCommentRequirement(parseInt(ratingInput.value));
}

// ── Mini star ratings ────────────────────────────────────────────────────────
document.querySelectorAll('.mini-stars').forEach(group => {
    const field = group.dataset.field;
    const input = document.getElementById(field + 'Input');
    const btns  = group.querySelectorAll('.mini-star-btn');

    btns.forEach(btn => {
        btn.addEventListener('click', () => {
            const val = parseInt(btn.dataset.value);
            input.value = val;
            btns.forEach(b => b.classList.toggle('active', parseInt(b.dataset.value) <= val));
        });
        btn.addEventListener('mouseenter', () => {
            const v = parseInt(btn.dataset.value);
            btns.forEach(b => b.classList.toggle('active', parseInt(b.dataset.value) <= v));
        });
        btn.addEventListener('mouseleave', () => {
            const cur = parseInt(input.value) || 0;
            btns.forEach(b => b.classList.toggle('active', parseInt(b.dataset.value) <= cur));
        });
    });
});

// ── Photo preview ────────────────────────────────────────────────────────────
function handlePhotos(input) {
    const container = document.getElementById('photoPreviews');
    container.innerHTML = '';
    const files = Array.from(input.files).slice(0, 5);
    files.forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'photo-preview';
            container.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
}

// ── Form validation ───────────────────────────────────────────────────────────
document.getElementById('reviewForm').addEventListener('submit', function(e) {
    if (!ratingInput.value) {
        e.preventDefault();
        alert('{{ __('review.messages.rating_required') }}');
    }
});
</script>
</body>
</html>
