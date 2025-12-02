<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

/**
 * Page Seeder
 *
 * Seeds initial static pages for the Majalis platform
 * Creates About Us, Contact Us, Terms & Conditions, and Privacy Policy pages
 */
class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            // About Us Page
            [
                'slug' => 'about-us',
                'title_en' => 'About Us',
                'title_ar' => 'من نحن',
                'content_en' => '<h2>Welcome to Majalis</h2><p>Majalis is Oman\'s premier hall booking platform, connecting event organizers with the perfect venues for their special occasions.</p><h3>Our Mission</h3><p>We strive to make hall booking simple, transparent, and efficient for both venue owners and customers across Oman.</p><h3>Why Choose Majalis?</h3><ul><li>Wide selection of verified halls</li><li>Secure online booking and payment</li><li>24/7 customer support</li><li>Competitive pricing</li></ul>',
                'content_ar' => '<h2>مرحباً بكم في مجالس</h2><p>مجالس هي منصة حجز القاعات الرائدة في عمان، تربط منظمي الفعاليات بالأماكن المثالية لمناسباتهم الخاصة.</p><h3>مهمتنا</h3><p>نسعى لجعل حجز القاعات بسيطاً وشفافاً وفعالاً لكل من أصحاب القاعات والعملاء في جميع أنحاء عمان.</p><h3>لماذا تختار مجالس؟</h3><ul><li>مجموعة واسعة من القاعات الموثقة</li><li>حجز ودفع آمن عبر الإنترنت</li><li>دعم العملاء على مدار الساعة</li><li>أسعار تنافسية</li></ul>',
                'meta_title_en' => 'About Majalis - Leading Hall Booking Platform in Oman',
                'meta_title_ar' => 'عن مجالس - منصة حجز القاعات الرائدة في عمان',
                'meta_description_en' => 'Learn about Majalis, Oman\'s trusted platform for booking event halls and venues.',
                'meta_description_ar' => 'تعرف على مجالس، المنصة الموثوقة في عمان لحجز قاعات ومواقع الفعاليات.',
                'is_active' => true,
                'order' => 1,
                'show_in_footer' => true,
                'show_in_header' => true,
            ],

            // Contact Us Page
            [
                'slug' => 'contact-us',
                'title_en' => 'Contact Us',
                'title_ar' => 'اتصل بنا',
                'content_en' => '<h2>Get in Touch</h2><p>Have questions about bookings, claims, or need assistance? We\'re here to help!</p><h3>Customer Support</h3><p>Email: support@majalis.om<br>Phone: +968 1234 5678<br>Hours: Sunday - Thursday, 8:00 AM - 5:00 PM</p><h3>Claims Department</h3><p>For booking disputes, refund requests, or compensation claims:<br>Email: claims@majalis.om<br>Phone: +968 1234 5679</p><h3>Office Location</h3><p>Majalis Hall Booking Platform<br>Muscat, Oman</p>',
                'content_ar' => '<h2>تواصل معنا</h2><p>هل لديك أسئلة حول الحجوزات أو المطالبات أو تحتاج إلى مساعدة؟ نحن هنا للمساعدة!</p><h3>دعم العملاء</h3><p>البريد الإلكتروني: support@majalis.om<br>الهاتف: ٩٦٨ ١٢٣٤ ٥٦٧٨+<br>ساعات العمل: الأحد - الخميس، ٨:٠٠ صباحاً - ٥:٠٠ مساءً</p><h3>قسم المطالبات</h3><p>لخلافات الحجز، أو طلبات الاسترداد، أو مطالبات التعويض:<br>البريد الإلكتروني: claims@majalis.om<br>الهاتف: ٩٦٨ ١٢٣٤ ٥٦٧٩+</p><h3>موقع المكتب</h3><p>منصة مجالس لحجز القاعات<br>مسقط، عمان</p>',
                'meta_title_en' => 'Contact Majalis - Customer Support & Claims',
                'meta_title_ar' => 'اتصل بمجالس - دعم العملاء والمطالبات',
                'meta_description_en' => 'Contact Majalis for booking support, claims, and customer assistance.',
                'meta_description_ar' => 'اتصل بمجالس للحصول على دعم الحجز والمطالبات ومساعدة العملاء.',
                'is_active' => true,
                'order' => 2,
                'show_in_footer' => true,
                'show_in_header' => false,
            ],

            // Terms and Conditions
            [
                'slug' => 'terms-and-conditions',
                'title_en' => 'Terms and Conditions',
                'title_ar' => 'الشروط والأحكام',
                'content_en' => '<h2>Terms and Conditions</h2><p><strong>Last Updated:</strong> January 2025</p><h3>1. Acceptance of Terms</h3><p>By accessing and using Majalis, you agree to be bound by these Terms and Conditions.</p><h3>2. Booking Terms</h3><ul><li>All bookings are subject to availability</li><li>Payment must be completed to confirm booking</li><li>Cancellation policy applies as per hall owner settings</li></ul><h3>3. User Responsibilities</h3><ul><li>Provide accurate booking information</li><li>Comply with hall rules and regulations</li><li>Report any issues within 24 hours</li></ul><h3>4. Payment Terms</h3><ul><li>Payments processed securely via Thawani</li><li>Platform commission deducted from hall owner earnings</li><li>Refunds processed within 14 business days</li></ul><h3>5. Liability</h3><p>Majalis acts as an intermediary and is not responsible for disputes between customers and hall owners.</p><h3>6. Modifications</h3><p>We reserve the right to modify these terms with notice to users.</p>',
                'content_ar' => '<h2>الشروط والأحكام</h2><p><strong>آخر تحديث:</strong> يناير ٢٠٢٥</p><h3>١. قبول الشروط</h3><p>بالدخول واستخدام مجالس، فإنك توافق على الالتزام بهذه الشروط والأحكام.</p><h3>٢. شروط الحجز</h3><ul><li>جميع الحجوزات تخضع للتوافر</li><li>يجب إتمام الدفع لتأكيد الحجز</li><li>تطبق سياسة الإلغاء وفقاً لإعدادات مالك القاعة</li></ul><h3>٣. مسؤوليات المستخدم</h3><ul><li>تقديم معلومات حجز دقيقة</li><li>الالتزام بقواعد وأنظمة القاعة</li><li>الإبلاغ عن أي مشاكل خلال ٢٤ ساعة</li></ul><h3>٤. شروط الدفع</h3><ul><li>تتم معالجة المدفوعات بشكل آمن عبر ثواني</li><li>يتم خصم عمولة المنصة من أرباح مالك القاعة</li><li>تتم معالجة المبالغ المستردة خلال ١٤ يوم عمل</li></ul><h3>٥. المسؤولية</h3><p>تعمل مجالس كوسيط وليست مسؤولة عن النزاعات بين العملاء وأصحاب القاعات.</p><h3>٦. التعديلات</h3><p>نحتفظ بالحق في تعديل هذه الشروط مع إشعار المستخدمين.</p>',
                'meta_title_en' => 'Terms and Conditions - Majalis',
                'meta_title_ar' => 'الشروط والأحكام - مجالس',
                'meta_description_en' => 'Read Majalis terms and conditions for using our hall booking platform.',
                'meta_description_ar' => 'اقرأ شروط وأحكام مجالس لاستخدام منصة حجز القاعات.',
                'is_active' => true,
                'order' => 3,
                'show_in_footer' => true,
                'show_in_header' => false,
            ],

            // Privacy Policy
            [
                'slug' => 'privacy-policy',
                'title_en' => 'Privacy Policy',
                'title_ar' => 'سياسة الخصوصية',
                'content_en' => '<h2>Privacy Policy</h2><p><strong>Last Updated:</strong> January 2025</p><h3>1. Information We Collect</h3><p>We collect information you provide when:</p><ul><li>Creating an account</li><li>Making a booking</li><li>Contacting customer support</li><li>Using our services</li></ul><h3>2. How We Use Your Information</h3><ul><li>Process bookings and payments</li><li>Communicate about your reservations</li><li>Improve our services</li><li>Comply with legal obligations</li></ul><h3>3. Data Security</h3><p>We implement industry-standard security measures to protect your personal information.</p><h3>4. Information Sharing</h3><p>We share your information only with:</p><ul><li>Hall owners for confirmed bookings</li><li>Payment processors (Thawani)</li><li>Legal authorities when required</li></ul><h3>5. Your Rights</h3><ul><li>Access your personal data</li><li>Request data correction</li><li>Delete your account</li><li>Opt-out of marketing communications</li></ul><h3>6. Cookies</h3><p>We use cookies to enhance user experience and analyze site usage.</p><h3>7. Contact</h3><p>For privacy concerns, email privacy@majalis.om</p>',
                'content_ar' => '<h2>سياسة الخصوصية</h2><p><strong>آخر تحديث:</strong> يناير ٢٠٢٥</p><h3>١. المعلومات التي نجمعها</h3><p>نقوم بجمع المعلومات التي تقدمها عند:</p><ul><li>إنشاء حساب</li><li>إجراء حجز</li><li>الاتصال بدعم العملاء</li><li>استخدام خدماتنا</li></ul><h3>٢. كيف نستخدم معلوماتك</h3><ul><li>معالجة الحجوزات والمدفوعات</li><li>التواصل بشأن حجوزاتك</li><li>تحسين خدماتنا</li><li>الامتثال للالتزامات القانونية</li></ul><h3>٣. أمن البيانات</h3><p>نطبق تدابير أمنية معيارية في الصناعة لحماية معلوماتك الشخصية.</p><h3>٤. مشاركة المعلومات</h3><p>نشارك معلوماتك فقط مع:</p><ul><li>أصحاب القاعات للحجوزات المؤكدة</li><li>معالجات الدفع (ثواني)</li><li>السلطات القانونية عند الحاجة</li></ul><h3>٥. حقوقك</h3><ul><li>الوصول إلى بياناتك الشخصية</li><li>طلب تصحيح البيانات</li><li>حذف حسابك</li><li>إلغاء الاشتراك في الاتصالات التسويقية</li></ul><h3>٦. ملفات تعريف الارتباط</h3><p>نستخدم ملفات تعريف الارتباط لتحسين تجربة المستخدم وتحليل استخدام الموقع.</p><h3>٧. اتصل بنا</h3><p>لمخاوف الخصوصية، راسلنا على privacy@majalis.om</p>',
                'meta_title_en' => 'Privacy Policy - Majalis',
                'meta_title_ar' => 'سياسة الخصوصية - مجالس',
                'meta_description_en' => 'Learn how Majalis protects your privacy and handles your personal data.',
                'meta_description_ar' => 'تعرف على كيفية حماية مجالس لخصوصيتك والتعامل مع بياناتك الشخصية.',
                'is_active' => true,
                'order' => 4,
                'show_in_footer' => true,
                'show_in_header' => false,
            ],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }
    }
}
