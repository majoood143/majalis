<footer class="py-8 mt-12 text-white bg-gray-800">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
            <div>
                <div class="flex items-center mb-4 space-x-2 ">
                    <img src="{{ asset('images/logo.webp') }}" alt="Majalis Logo" class="w-8 h-8 rounded-xl">
                    <span class="text-xl font-bold text-white">
                         {{ app()->getLocale() === 'ar' ? 'مجالس' : 'Majalis' }}
                    </span>
                </div>
                <p class="text-gray-400">
                    {{ app()->getLocale() === 'ar' ? 'منصة حجز القاعات الموثوقة في عمان' : 'Your trusted hall booking platform in Oman' }}
                </p>

                <!-- Social Media Links -->
                @php
                    $socialTwitter   = \App\Models\Setting::get('social', 'twitter_url');
                    $socialInstagram = \App\Models\Setting::get('social', 'instagram_url');
                    $socialFacebook  = \App\Models\Setting::get('social', 'facebook_url');
                    $socialSnapchat  = \App\Models\Setting::get('social', 'snapchat_url');
                    $socialYoutube   = \App\Models\Setting::get('social', 'youtube_url');
                    $socialTiktok    = \App\Models\Setting::get('social', 'tiktok_url');
                    $socialLinkedin  = \App\Models\Setting::get('social', 'linkedin_url');
                @endphp
                <div class="flex flex-wrap gap-3 mt-4">
                    @if($socialTwitter)
                    <a href="{{ $socialTwitter }}" target="_blank" rel="noopener noreferrer"
                       class="text-gray-400 transition hover:text-white" aria-label="X (Twitter)">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </a>
                    @endif

                    @if($socialInstagram)
                    <a href="{{ $socialInstagram }}" target="_blank" rel="noopener noreferrer"
                       class="text-gray-400 transition hover:text-white" aria-label="Instagram">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zM5.838 12a6.162 6.162 0 1112.324 0 6.162 6.162 0 01-12.324 0zM12 16a4 4 0 110-8 4 4 0 010 8zm4.965-10.405a1.44 1.44 0 112.881.001 1.44 1.44 0 01-2.881-.001z"/>
                        </svg>
                    </a>
                    @endif

                    @if($socialFacebook)
                    <a href="{{ $socialFacebook }}" target="_blank" rel="noopener noreferrer"
                       class="text-gray-400 transition hover:text-white" aria-label="Facebook">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.879v-6.99h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.99C18.343 21.128 22 16.991 22 12z"/>
                        </svg>
                    </a>
                    @endif

                    @if($socialSnapchat)
                    <a href="{{ $socialSnapchat }}" target="_blank" rel="noopener noreferrer"
                       class="text-gray-400 transition hover:text-yellow-400" aria-label="Snapchat">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12.017 2C9.638 2 7.49 3.156 6.23 5.02c-.678.994-.938 2.107-.938 3.312 0 .38.03.756.077 1.122-.32.158-.664.24-1.02.24-.413 0-.79-.1-1.095-.278-.11-.063-.226-.094-.34-.094-.29 0-.554.194-.625.487-.044.18-.01.368.093.514.28.398.773.677 1.374.797.04.008.08.016.12.022-.17.43-.434.8-.79 1.05-.158.11-.24.293-.21.473.03.18.16.326.337.38.9.278 1.688.43 2.356.46.018.04.03.082.03.126 0 .128-.04.266-.12.41-.28.51-.89.938-1.806 1.27-.27.098-.444.356-.41.64.034.284.26.51.547.535.974.086 1.84.376 2.58.863.255.166.543.25.837.25.22 0 .44-.046.646-.14.438-.2.92-.302 1.43-.302.51 0 .992.102 1.43.302.206.094.425.14.645.14.294 0 .582-.084.837-.25.74-.487 1.606-.777 2.58-.863.288-.025.513-.25.547-.535.034-.284-.14-.542-.41-.64-.916-.332-1.527-.76-1.806-1.27-.08-.144-.12-.282-.12-.41 0-.044.012-.086.03-.126.668-.03 1.456-.182 2.356-.46.178-.054.307-.2.337-.38.03-.18-.052-.362-.21-.473-.356-.25-.62-.62-.79-1.05.04-.006.08-.014.12-.022.6-.12 1.094-.4 1.374-.797.103-.146.137-.334.093-.514-.07-.293-.335-.487-.625-.487-.114 0-.23.03-.34.094-.305.178-.682.278-1.095.278-.356 0-.7-.082-1.02-.24.047-.366.077-.742.077-1.122 0-1.205-.26-2.318-.938-3.312C14.527 3.156 12.378 2 12.017 2z"/>
                        </svg>
                    </a>
                    @endif

                    @if($socialYoutube)
                    <a href="{{ $socialYoutube }}" target="_blank" rel="noopener noreferrer"
                       class="text-gray-400 transition hover:text-red-500" aria-label="YouTube">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                    </a>
                    @endif

                    @if($socialTiktok)
                    <a href="{{ $socialTiktok }}" target="_blank" rel="noopener noreferrer"
                       class="text-gray-400 transition hover:text-white" aria-label="TikTok">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.32 6.32 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.18 8.18 0 004.78 1.52V6.79a4.85 4.85 0 01-1.01-.1z"/>
                        </svg>
                    </a>
                    @endif

                    @if($socialLinkedin)
                    <a href="{{ $socialLinkedin }}" target="_blank" rel="noopener noreferrer"
                       class="text-gray-400 transition hover:text-blue-400" aria-label="LinkedIn">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </a>
                    @endif
                </div>
            </div>

            <div>
                <h3 class="mb-4 text-lg font-semibold">
                    {{ app()->getLocale() === 'ar' ? 'روابط سريعة' : 'Quick Links' }}
                </h3>
                <ul class="space-y-2">
                    <li><a href="{{ url('/about-us') }}" class="text-gray-400 transition hover:text-white">
                            {{ app()->getLocale() === 'ar' ? 'من نحن' : 'About Us' }}
                        </a></li>
                    <li><a href="{{ url('/contact-us') }}" class="text-gray-400 transition hover:text-white">
                            {{ app()->getLocale() === 'ar' ? 'اتصل بنا' : 'Contact Us' }}
                        </a></li>
                    <li><a href="{{ url('/terms-and-conditions') }}" class="text-gray-400 transition hover:text-white">
                            {{ app()->getLocale() === 'ar' ? 'الشروط والأحكام' : 'Terms & Conditions' }}
                        </a></li>
                    <li><a href="{{ url('/privacy-policy') }}" class="text-gray-400 transition hover:text-white">
                            {{ app()->getLocale() === 'ar' ? 'سياسة الخصوصية' : 'Privacy Policy' }}
                        </a></li>
                    <li>
                        <a href="{{ route('hall-owner.register') }}" style="color: rgb(185 145 109)" class="font-medium transition hover:text-gray-300">
                            {{ app()->getLocale() === 'ar' ? 'سجّل قاعتك معنا' : 'Register Your Hall' }}
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <h3 class="mb-4 text-lg font-semibold">
                    {{ app()->getLocale() === 'ar' ? 'اتصل بنا' : 'Contact' }}
                </h3>
                <p class="text-gray-400">
                    @php $contactEmail = \App\Models\Setting::get('contact', 'email'); $contactPhone = \App\Models\Setting::get('contact', 'phone'); @endphp
                    @if($contactEmail){{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email' }}: {{ $contactEmail }}<br>@endif
                    @if($contactPhone){{ app()->getLocale() === 'ar' ? 'الهاتف' : 'Phone' }}: {{ $contactPhone }}@endif

                </p>

                <!-- Payment Methods Section -->
                <div class="mt-6">
                    <h4 class="mb-3 text-sm font-semibold text-gray-300">
                        {{ app()->getLocale() === 'ar' ? 'طرق الدفع المقبولة' : 'We Accept' }}
                    </h4>
                    <div class="flex flex-wrap gap-3">
                        <!-- Visa -->
                        <div class="flex items-center justify-center px-2 py-1 transition bg-white rounded-lg hover:bg-gray-100" style="min-width:52px; height:32px;">
                            <img src="{{ asset('images/payment/visa.svg') }}" class="w-auto h-5" alt="Visa">
                        </div>

                        <!-- Mastercard -->
                        <div class="flex items-center justify-center px-2 py-1 transition bg-white rounded-lg hover:bg-gray-100" style="min-width:52px; height:32px;">
                            <img src="{{ asset('images/payment/mastercard.svg') }}" class="w-auto h-5" alt="Mastercard">
                        </div>

                        <!-- Thawani -->
                        <div class="flex items-center justify-center px-2 py-1 transition bg-white rounded-lg hover:bg-gray-100" style="min-width:52px; height:32px;">
                            <img src="{{ asset('images/payment/thawani.svg') }}" class="w-auto h-5" alt="Thawani">
                        </div>

                        <!-- Maal -->
                        <div class="flex items-center justify-center px-2 py-1 transition bg-white rounded-lg hover:bg-gray-100" style="min-width:52px; height:32px;">
                            <img src="{{ asset('images/payment/maal.svg') }}" class="w-auto h-5" alt="Maal">
                        </div>


                    </div>
                </div>
            </div>
        </div>

        <div class="pt-8 mt-8 text-center text-gray-400 border-t border-gray-700">
            <p>&copy; {{ date('Y') }} Majalis.
                {{ app()->getLocale() === 'ar' ? 'جميع الحقوق محفوظة' : 'All rights reserved' }}.</p>
        </div>
    </div>
</footer>

<!-- Go to Top Button (desktop only) -->
<button id="go-to-top"
    onclick="window.scrollTo({ top: 0, behavior: 'smooth' })"
    title="{{ app()->getLocale() === 'ar' ? 'العودة إلى الأعلى' : 'Back to top' }}"
    aria-label="{{ app()->getLocale() === 'ar' ? 'العودة إلى الأعلى' : 'Back to top' }}"
    class="hidden md:flex fixed bottom-6 {{ app()->getLocale() === 'ar' ? 'left-6' : 'right-6' }} z-50 items-center justify-center w-11 h-11 bg-gray-800 text-white rounded-full shadow-lg opacity-0 pointer-events-none transition-all duration-300 hover:bg-gray-700"
    style="transition: opacity 0.3s, transform 0.3s;">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/>
    </svg>
</button>

<script>
    (function () {
        var btn = document.getElementById('go-to-top');
        window.addEventListener('scroll', function () {
            if (window.scrollY > 300) {
                btn.style.opacity = '1';
                btn.style.pointerEvents = 'auto';
            } else {
                btn.style.opacity = '0';
                btn.style.pointerEvents = 'none';
            }
        });
    })();
</script>
