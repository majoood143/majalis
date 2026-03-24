<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('review.page.link_invalid_title') }} — {{ config('app.name') }}</title>
    <style>
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: #f5f5f5; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .card { background: #fff; border-radius: 16px; box-shadow: 0 2px 16px rgba(0,0,0,.08); padding: 48px 32px; text-align: center; max-width: 400px; }
        .icon { font-size: 56px; margin-bottom: 16px; }
        h1 { font-size: 20px; color: #111827; margin-bottom: 10px; }
        p  { font-size: 14px; color: #6b7280; line-height: 1.6; }
    </style>
</head>
<body>
<div class="card">
    <div class="icon">⏳</div>
    <h1>{{ __('review.page.link_invalid_title') }}</h1>
    <p>{{ $error }}</p>
</div>
</body>
</html>
