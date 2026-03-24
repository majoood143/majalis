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
                <div class="flex mt-4 space-x-4">
                    <a href="https://twitter.com/majalis_om" target="_blank" rel="noopener noreferrer"
                       class="text-gray-400 transition hover:text-white" aria-label="X (Twitter)">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </a>

                    <a href="https://instagram.com/majalis_om" target="_blank" rel="noopener noreferrer"
                       class="text-gray-400 transition hover:text-white" aria-label="Instagram">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zM5.838 12a6.162 6.162 0 1112.324 0 6.162 6.162 0 01-12.324 0zM12 16a4 4 0 110-8 4 4 0 010 8zm4.965-10.405a1.44 1.44 0 112.881.001 1.44 1.44 0 01-2.881-.001z"/>
                        </svg>
                    </a>

                    <a href="https://facebook.com/majalis_om" target="_blank" rel="noopener noreferrer"
                       class="text-gray-400 transition hover:text-white" aria-label="Facebook">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.879v-6.99h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.99C18.343 21.128 22 16.991 22 12z"/>
                        </svg>
                    </a>
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
                        <div class="p-2 transition bg-gray-700 rounded-lg hover:bg-gray-600">
                            <svg class="w-10 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.445 8.511c-1.5 0-2.775.75-3.6 1.95l-1.425 2.325-1.35-2.25c-.525-.825-1.425-1.35-2.4-1.35-1.65 0-3 1.35-3 3v3.75c0 .45.375.825.825.825h1.575c.45 0 .825-.375.825-.825v-3.75c0-.6.525-1.125 1.125-1.125.6 0 1.125.525 1.125 1.125v3.75c0 .45.375.825.825.825h1.575c.45 0 .825-.375.825-.825v-3.75c0-.6.525-1.125 1.125-1.125.6 0 1.125.525 1.125 1.125v3.75c0 .45.375.825.825.825h1.575c.45 0 .825-.375.825-.825v-3.75c0-2.175-1.65-3.975-3.825-3.975z" fill="currentColor"/>
                            </svg>
                        </div>

                        <!-- Mastercard -->
                        <div class="p-2 transition bg-gray-700 rounded-lg hover:bg-gray-600">
                            <svg class="w-10 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="8" cy="12" r="4.5" fill="currentColor" fill-opacity="0.7"/>
                                <circle cx="16" cy="12" r="4.5" fill="currentColor" fill-opacity="0.7"/>
                                <path d="M12 8.5c1.5 1.5 1.5 4.5 0 6" stroke="currentColor" stroke-width="1.5"/>
                            </svg>
                        </div>

                        <!-- PayPal -->
                        <div class="p-2 transition bg-gray-700 rounded-lg hover:bg-gray-600">
                            <svg class="w-10 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16.5 7.5c0-2.25-1.5-4.5-4.5-4.5H7.5c-.75 0-1.5.75-1.5 1.5L4.5 18c0 .75.75 1.5 1.5 1.5h3l.75-4.5h3c3 0 4.5-1.5 4.5-4.5 0-1.5-.75-3-2.25-3z" fill="currentColor"/>
                                <path d="M15 7.5c.75 0 1.5.75 1.5 1.5 0 2.25-1.5 3.75-3.75 3.75h-2.25l.75-4.5h3.75z" fill="currentColor" fill-opacity="0.7"/>
                            </svg>
                        </div>

                        <!-- Apple Pay -->
                        <div class="p-2 transition bg-gray-700 rounded-lg hover:bg-gray-600">
                            <svg class="w-10 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.5 7.5c.5 0 1 .25 1.5.75.5-.5 1-.75 1.5-.75 1 0 1.5.75 1.5 1.5 0 .25-.25 1-1 1.5-.5.5-1 .75-2 .75s-1.5-.25-2-.75c-.5-.5-1-1.25-1-1.5 0-.75.5-1.5 1.5-1.5zM6 18c.5 0 1-.5 1.5-1.5.5 1 1 1.5 1.5 1.5.5 0 1-.5 1.5-1.5.5 1 1 1.5 1.5 1.5.5 0 1-.5 1.5-1.5.5 1 1 1.5 1.5 1.5.5 0 1-.5 1.5-1.5.5 1 1 1.5 1.5 1.5.5 0 1-.5 1.5-1.5" stroke="currentColor" stroke-width="0.5"/>
                            </svg>
                        </div>

                        <!-- Google Pay -->
                        <div class="p-2 transition bg-gray-700 rounded-lg hover:bg-gray-600">
                            <svg class="w-10 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 12c0-1.5.75-3 2.25-3.75L9 7.5C7.5 6.75 6 6.75 4.5 7.5 2.25 9 1.5 12 3 14.25c.75 1.5 2.25 2.25 3.75 2.25 1.5 0 3-.75 3.75-2.25.75-1.5.75-3 0-4.5" stroke="currentColor" stroke-width="1.5"/>
                                <circle cx="15" cy="12" r="2.5" fill="currentColor"/>
                                <circle cx="21" cy="12" r="2.5" fill="currentColor"/>
                            </svg>
                        </div>

                        <!-- Cash on Delivery / COD -->
                        <div class="p-2 transition bg-gray-700 rounded-lg hover:bg-gray-600">
                            <svg class="w-10 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="4" y="8" width="16" height="10" rx="1" stroke="currentColor" stroke-width="1.5"/>
                                <circle cx="12" cy="13" r="2" fill="currentColor"/>
                                <path d="M8 6L16 6" stroke="currentColor" stroke-width="1.5"/>
                            </svg>
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
