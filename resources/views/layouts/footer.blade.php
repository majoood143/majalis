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
                {{-- <img src="{{ asset('images/logo.webp') }}" alt="Majalis Logo" class="w-8 h-8 rounded-xl">
                <h3 class="mb-4 text-lg font-semibold">
                    {{ app()->getLocale() === 'ar' ? 'مجالس' : 'Majalis' }}
                </h3> --}}
                <p class="text-gray-400">
                    {{ app()->getLocale() === 'ar' ? 'منصة حجز القاعات الموثوقة في عمان' : 'Your trusted hall booking platform in Oman' }}
                </p>
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
                </ul>
            </div>
            <div>
                <h3 class="mb-4 text-lg font-semibold">
                    {{ app()->getLocale() === 'ar' ? 'اتصل بنا' : 'Contact' }}
                </h3>
                <p class="text-gray-400">
                    {{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email' }}: support@majalis.om<br>
                    {{ app()->getLocale() === 'ar' ? 'الهاتف' : 'Phone' }}: +968 1234 5678
                </p>
            </div>
        </div>
        <div class="pt-8 mt-8 text-center text-gray-400 border-t border-gray-700">
            <p>&copy; {{ date('Y') }} Majalis.
                {{ app()->getLocale() === 'ar' ? 'جميع الحقوق محفوظة' : 'All rights reserved' }}.</p>
        </div>
    </div>
</footer>
