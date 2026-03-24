<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('review.page.thankyou_title') }} — {{ config('app.name') }}</title>
    <style>
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: #f5f5f5; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .card { background: #fff; border-radius: 16px; box-shadow: 0 2px 16px rgba(0,0,0,.08); padding: 48px 32px; text-align: center; max-width: 440px; }
        .icon { font-size: 64px; margin-bottom: 16px; }
        h1 { font-size: 22px; color: #111827; margin-bottom: 10px; }
        p  { font-size: 14px; color: #6b7280; line-height: 1.7; margin-bottom: 8px; }
        .badge { display: inline-block; background: #fef3c7; color: #92400e; border-radius: 20px; padding: 4px 12px; font-size: 12px; font-weight: 600; margin-top: 8px; }
        .btn { display: inline-block; margin-top: 24px; padding: 12px 28px; background: linear-gradient(135deg, #7c3aed, #4f46e5); color: #fff; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 14px; }
    </style>
</head>
<body>
<div class="card">
    <div class="icon">🎉</div>
    <h1>{{ __('review.page.thankyou_heading') }}</h1>
    <p>{{ __('review.page.thankyou_body') }}</p>
    @if($isLate)
        <span class="badge">{{ __('review.page.thankyou_late_badge') }}</span>
    @endif
    <br>
    <a href="{{ route('home') }}" class="btn">{{ __('review.page.thankyou_cta') }}</a>
</div>
</body>
</html>
