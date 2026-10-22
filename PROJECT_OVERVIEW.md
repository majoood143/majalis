# Majalis — Project Overview

> Hall-booking platform for Oman (currency **OMR**, timezone **Asia/Muscat**, bilingual **Arabic / English**).
> Customers find and book halls (majalis) online; hall owners manage their halls, availability, pricing and earnings; admins run the whole marketplace.

_Last audited: 2026-10-07 on branch `main`._

---

## 1. Filament v4 upgrade status

**Verdict: ✅ The project is on Filament v4.** The upgrade was merged into `main` through PR #2 (`feature/filament-v4-upgrade`).

### Evidence

| Check | Result |
|---|---|
| `composer.json` | `filament/filament: ^4.0` |
| Installed (`composer.lock`) | `filament/*` **v4.11.2** (actions, forms, infolists, schemas, tables, widgets, notifications, query-builder, support) |
| v4 Schema API | `Filament\Schemas\Schema` used in 67 files, `Filament\Schemas\Components\*` in 92 files |
| v4 unified actions | `Filament\Actions\*` used in 146 files |
| v3 method signatures (`form(Form $form)`, `infolist(Infolist $infolist)`) | **0** remaining |
| v3 static properties (`$navigationIcon`, `$view` as plain `string`) | **0** in active code (only in `ActivityLog.php.back`) |
| PHP lint over `app/`, `routes/`, `config/` | No syntax errors |
| `php artisan` boots | ✅ Laravel 12.60.2, PHP 8.4.12 |

### Plugin compatibility (all resolved for v4)

| Plugin | Version | Notes |
|---|---|---|
| bezhansalleh/filament-shield | 4.2.0 | Permissions in `Action:Model` format |
| lara-zeus/spatie-translatable | 1.0.4 | Replaces the abandoned `filament/spatie-laravel-translatable-plugin` |
| pxlrbt/filament-activity-log | 3.1.1 | Replaces `rmsramos/activitylog`, which has no v4 version |
| spatie/laravel-activitylog | 5.0.0 | Traits moved to `Spatie\Activitylog\Models\Concerns\LogsActivity` |
| saade/filament-fullcalendar | 4.0.0-beta7 | **Beta**, so `minimum-stability: beta` is set |
| guava/filament-knowledge-base | ^2.0 | Standard panel + `KnowledgeBasePlugin` |
| backstage/mails | ^4.0 | Namespace changed from `Vormkracht10` |
| croustibat/filament-jobs-monitor | ^4.0 | |
| rupadana/filament-api-service | **4.0.3** | Admin + Owner panel APIs set up (see §8) |
| shuvroroy backup / health, filament-gtag, env-editor, sitemap-generator, map-picker, captcha, whatsapp-conector | — | All load under v4 |

### Cleanup left (not blocking; the app runs)

These are cosmetic or deprecation items. None of them causes a runtime error today.

1. **Unused v3 `use` imports** for classes that no longer exist in v4. They are unused because all call sites use fully qualified v4 classes. They are safe, but they confuse IDEs and static analysis:
   - `use Filament\Forms\Form;` (33 files)
   - `use Filament\Infolists\Infolist;` (13 files)
   - `use Filament\Forms\Get;` / `use Filament\Forms\Set;` (5 files). The v4 class is `Filament\Schemas\Components\Utilities\Get|Set`.
   - `use Filament\Tables\Actions\Action|ActionGroup;` (7 files). The v4 namespace is `Filament\Actions\*`.
   - `use Filament\Forms\Components\Tabs;` (`Admin/Pages/ManageSettings.php`) and `use Filament\Infolists\Components\Tabs\Tab;` (`Admin/Resources/ExtraServiceResource.php`)

   Fix: run `vendor/bin/pint` with the `no_unused_imports` rule, or delete them by hand.
2. **Deprecated but still working in v4:**
   - `BadgeColumn`: 6 usages in `Owner/Resources/EarningsResource.php`, `Owner/Resources/TicketResource.php` and `Owner/Widgets/RecentBookingsWidget.php`. Replace with `TextColumn::make()->badge()`.
   - `Forms\Components\Placeholder`: ~20 files. Replace with `Filament\Infolists\Components\TextEntry` (v4 recommendation).
3. **TODO** in `app/Providers/Filament/AdminPanelProvider.php:101` about the old activity-log plugin. Resources now read `Spatie\Activitylog\Models\Activity` directly, so this comment can be removed.
4. **Stale file:** `app/Filament/Owner/Pages/ActivityLog.php.back`.
5. **Shield permissions:** the DB still has **280 old-format** permissions (`create_booking`) next to **546 new-format** ones (`Create:Booking`). Delete the old ones after re-assigning roles.
6. **PHP 8.4 deprecations** (implicit nullable parameters): `Review::reject()`, `HallOwner::verify()`, `HallOwner::reject()`. Change `string $x = null` to `?string $x = null`.
7. `OwnerPanelProvider` calls `discoverWidgets()` twice for the same path.
8. Branch `feature/filament-v4-upgrade` has 2 extra commits that only comment out panel colours. It can be deleted or merged.

---

## 2. Tech stack

| Layer | Technology |
|---|---|
| Language | PHP 8.4 (requires `^8.2`) |
| Framework | Laravel 12 |
| Admin UI | Filament 4.11 (Livewire 3.8, Alpine) |
| Frontend (customer site) | Blade + Tailwind CSS 4 + Vite 7 + Alpine.js |
| Database | MySQL |
| Cache / queue | Redis (predis), database queue tables |
| Auth | Session (web) + Laravel Breeze (customer); Sanctum tokens for the API |
| Roles / permissions | spatie/laravel-permission 6 + Filament Shield 4 |
| Translations | spatie/laravel-translatable (JSON columns), `lang/ar.json`, `lang/en.json` |
| PDF | mPDF (Arabic font Tajawal) + DomPDF |
| Payments | **Thawani** (Oman) via `ThawaniService` |
| Messaging | Email (logged with backstage/mails), **WhatsApp** via Evolution API (`wallacemartinss/filament-whatsapp-conector`), SMS driver config |
| API docs | dedoc/scramble, served at `/docs/api` |
| Maps | Leaflet by default, Google Maps when `GOOGLE_MAPS_API_KEY` is set |
| Monitoring | Spatie Health, Spatie Backup, Jobs Monitor, Activity Log |

---

## 3. Filament panels

| Panel | ID / path | Who | Location |
|---|---|---|---|
| **Admin** (default) | `admin` → `/admin` | Platform admins | `app/Filament/Admin`, `app/Filament/Resources`, `app/Filament/Pages` |
| **Owner** | `owner` → `/owner` | Hall owners | `app/Filament/Owner` |
| **Knowledge Base** | `knowledge-base` → `/kb` | Help docs | `docs/en` |

Both main panels have a custom login (with captcha), password reset, profile, database notifications and their own Vite theme (`resources/css/filament/{admin,owner}/theme.css`). Admin runs in SPA mode.

### Admin resources (23)
Booking, City, CommissionSetting, EventType, ExtraService, GuestSession, HallAvailability, HallCategory, HallFeature, HallImage, HallOwner, Hall, HallType, Notification, Page, Payment, Payout, PromoCode, Region, Review, ServiceFeeSetting, Ticket, User. Shield adds Role and User under `app/Filament/Resources`.

Admin pages: Dashboard, Reports, ManageSettings, EnvEditor, Maintenance, WhatsAppTester.

Admin plugins: Shield, Backup, Health, Mails, Jobs Monitor, Gtag, Env Editor, Evolution (WhatsApp), Sitemap Generator, **ApiService**, Spatie Translatable. The Owner panel also registers ApiService (`App\Filament\Api\ApiServicePlugin`).

### Owner resources (12)
Availability, Booking, Earnings, Expense, Feature, Gallery, Hall, Owner, Payout, Pricing, PromoCode, Ticket.

Owner pages: Dashboard, Reports, FinancialReportsPage, AvailabilityCalendarPage (FullCalendar).

---

## 4. Domain model

### Core entities (`app/Models`, 35 models)

```
User ─┬─< Booking >── Hall >── HallOwner ── User(owner)
      │      ├─< BookingDay          (multi-day bookings)
      │      ├─< BookingExtraService >── ExtraService
      │      ├─< Payment
      │      ├─< BookingNotification
      │      ├─  PromoCode (via PromoCodeUsage)
      │      ├─<> EventType
      │      └─< Review
      └─< Ticket ─< TicketMessage

Hall ─┬─< HallImage, HallFeature, HallAvailability, SeasonalPricing, ExtraService
      ├── City >── Region
      ├── HallCategory, HallType
      └─<> EventType

HallOwner ─< OwnerPayout, Expense >── ExpenseCategory
Settings: Setting, CommissionSetting, ServiceFeeSetting, Page
Guest flow: GuestSession (OTP-verified guest bookings)
```

**Translatable models** (JSON columns, `ar`/`en`): City, EventType, CommissionSetting, Expense, ExpenseCategory, ExtraService, Hall, HallFeature, HallCategory, HallImage, HallType, Region, SeasonalPricing, ServiceFeeSetting.

### Enums (`app/Enums`)

| Enum | Values |
|---|---|
| `UserRole` | admin, hall_owner, customer |
| `BookingStatus` | pending, confirmed, cancelled, completed, refunded |
| `PaymentStatus` | pending, paid, failed, refunded, partially_refunded |
| `PayoutStatus` | pending, processing, completed, failed, cancelled, on_hold |
| `TimeSlot` | morning, afternoon, evening, full_day |
| Others | CommissionType, Expense* (Type/Status/PaymentMethod/PaymentStatus), Notification* (Event/Status/Type), RecurringFrequency |

### Business rules (from `config/majalis.php` and services)
- Slots: morning 08:00–14:00, evening 16:00–22:00 (plus afternoon / full day in `TimeSlot`).
- Bookings can be made up to 90 days ahead, at least 24 hours in advance.
- Platform commission (percentage or fixed) + service fee, with an audit trail on bookings.
- Advance (partial) payment is supported per hall.
- Multi-day bookings (`booking_days` table).
- Promo codes with usage tracking.
- Guest booking without an account (OTP verification, token-based access, optional "create account" later).

---

## 5. Application layers

| Folder | Contents |
|---|---|
| `app/Services` | **BookingService**, AvailabilityService, PaymentService, **ThawaniService**, CommissionService, PromoCodeService, InvoiceService, ReportService, NotificationService, WhatsappNotificationService, PDF services, ImageOptimizationService |
| `app/Http/Controllers/Customer` | HallController, BookingController, GuestBookingController, TicketController, GuestTicketController, ProfileController |
| `app/Http/Controllers` | PaymentController (Thawani callbacks), PromoCodeController, ReviewController, PageController, CustomerController |
| `app/Http/Middleware` | SetUserLanguage, EnsureUserIsOwner, EnsureFilamentSessionIsValid, RemoveSensitiveHeaders |
| `app/Jobs` | SendBookingNotification, SendBookingReminder(s), SendReviewRequest, GenerateInvoice, SendBookingInvoiceEmail, AutoCompleteBookings |
| `app/Events` / `Listeners` | Booking events → notifications |
| `app/Policies` | 34 policies (Shield-generated + custom) |
| `app/Console/Commands` | SyncBookingsToAvailability, booking-email diagnostics, Tajawal font loaders |
| `app/Livewire` | HallOwnerRegistration, LanguageSwitcher, MaintenanceCommands |

### Routes
- `routes/web.php`: public site (halls, booking, payment callbacks, pages, reviews, dashboard/profile). Note the catch-all `{slug}` route at the end.
- `routes/customer.php`, `routes/customer-tickets.php`, `routes/guest-booking.php`, `routes/auth.php`
- Existing JSON endpoints (used by the website):
  - `GET  api/halls/check-availability`
  - `GET  api/halls/suggest-dates`
  - `POST api/promo-code/validate`
  - `POST api/webhooks/evolution` (WhatsApp webhook)
  - `POST api/auth/login`, `POST api/auth/logout` (from rupadana), `GET api/user` (`routes/api.php`)
  - `api/admin/*`, `api/owner/*`: panel APIs, see §8
- Scheduler (`routes/console.php`): `evolution:cleanup` daily.

---

## 6. Local setup

```bash
composer install
cp .env.example .env && php artisan key:generate
# configure DB_*, REDIS_*, MAIL_*, THAWANI_API_KEY, GOOGLE_MAPS_API_KEY, WhatsApp/Evolution keys
php artisan migrate --seed
npm install && npm run build
composer dev     # serve + queue:listen + pail + vite
```

Panels: `/admin`, `/owner`, `/kb`. API docs: `/docs/api`.

---

## 7. Testing

Only the Breeze defaults exist (`tests/Feature/Auth`, `ProfileTest`, `ExampleTest`). There are **no domain tests** for booking, pricing, availability or payment.

⚠️ `php artisan test` currently fails (24 of 25 tests, also before the API work): `phpunit.xml` uses in-memory SQLite, but the migrations don't run there (`no such table: regions`). Either fix the migrations for SQLite or point the test env at a MySQL test database.

---

## 8. Panel APIs (rupadana/filament-api-service) & mobile readiness

Plugin page: https://filamentphp.com/plugins/rupadana-api-service

**Status: ✅ set up for every app resource in the Admin (23) and Owner (11) panels — 152 endpoints.**
Vendor resources (Shield roles, mails, jobs monitor, WhatsApp, tokens, knowledge base) have no API.

### Authentication

| | |
|---|---|
| Login | `POST /api/auth/login` `{email, password}` → `{token}` (Sanctum, throttled 6/min) |
| Use | `Authorization: Bearer <token>` on every request |
| Current user | `GET /api/user` |
| Logout | `POST /api/auth/logout` (revokes **all** of the user's tokens) |
| Language | `?lang=ar\|en` or `Accept-Language` header (default: app locale) |

Access rules (`App\Http\Middleware\EnsureApiPanelAccess`): the token user must pass `User::canAccessPanel()` for that panel, the same check as the web panels.
- `/api/admin/*`: admin users, **plus** the Shield permission for each action (`ViewAny:Hall`, `Create:Hall`, …).
- `/api/owner/*`: active, verified hall owners. No Shield permission check (the Owner panel doesn't use one). Every query is limited to the owner's own halls/bookings/payouts.
- Customers get 403 on both.

### Conventions

```
GET    /api/{panel}/{slug}          list (paginated)
GET    /api/{panel}/{slug}/{id}     show
POST   /api/{panel}/{slug}          create            → 201
PUT|PATCH /api/{panel}/{slug}/{id}  partial update (only sent fields)
DELETE /api/{panel}/{slug}/{id}     delete (soft delete where the model supports it)
```

List query string:
- `?filter[status]=pending,confirmed` exact match (comma = any)
- `?filter[search]=text`
- `?filter[booking_date_from]=2026-01-01&filter[booking_date_to]=…` date ranges
- `?filter[trashed]=with|only` for soft-deleted models
- `?sort=-created_at`
- `?include=hall,user`
- `?per_page=15` (max 100)

Unknown filters, sorts or includes return 400. Each list handler declares what it allows.

Responses:
- Translatable fields: `name` (in the request language) + `name_translations: {ar, en}`.
- Writes take `name: {en, ar}`; a partial update merges into the existing translations.
- File fields: the path plus an absolute `*_url`.
- Uploads: multipart. On update, send `POST` + `_method=PUT`. Sending an existing path string keeps the current file.
- Errors are always JSON: 401, 403, 404, 422 (`{message, errors}`).

### Endpoints

**Admin** (`/api/admin/…`, Shield permissions):

| Slug | Ops | Notes |
|---|---|---|
| `bookings` | list, show, update, delete | update = status/payment status/customer fields/notes. Status changes call `Booking::confirm()/cancel()/complete()`. No create (bookings go through `BookingService`) |
| `payments` | list, show, update, delete | update = status (blocked: paid→pending, refunded→pending/paid), refund/failure fields. No create (Thawani flow) |
| `payouts` | CRUD | totals calculated from paid bookings in the period. Net amount recalculated on update |
| `halls` | CRUD | unique slug, capacity/lat/lng checks, phone cleanup, `featured_image` + `gallery[]` uploads |
| `hall-owners` | CRUD | logo + document uploads, one owner profile per user, commission ≤ 100% |
| `hall-images` | CRUD | upload → size/mime/dimensions saved. Replacing an image deletes the old file |
| `hall-availabilities` | CRUD | no past dates, one record per hall/date/slot |
| `extra-services` | CRUD | quantity range check; required ⇒ active |
| `commission-settings`, `service-fee-settings` | CRUD | hall+owner ⇒ hall wins, % ≤ 100, date order |
| `promo-codes` | CRUD | `created_by_type=admin`, % ≤ 100 |
| `tickets` | CRUD | `resolved_at`/`closed_at` stamped on status change |
| `users` | CRUD | password hashed, empty password on update = unchanged, can't delete yourself |
| `cities`, `regions`, `event-types`, `hall-categories`, `hall-types`, `hall-features`, `pages`, `reviews` | CRUD | slugs/codes generated when empty |
| `guest-sessions`, `notifications` | list, show, delete | |

**Owner** (`/api/owner/…`, own data only):

| Slug | Ops | Notes |
|---|---|---|
| `halls` | CRUD | `owner_id` forced to the token user, `is_featured` always false |
| `availabilities` | CRUD | **create is bulk**: `date` or `start_date`+`end_date` × `time_slots[]` → returns all records |
| `gallery` | CRUD | images for own halls only |
| `pricings` | CRUD | seasonal pricing for own halls |
| `promo-codes` | CRUD | `hall_id` required (own hall) |
| `expenses` | CRUD | `attachments[]` uploads |
| `tickets` | list, show, create, update | update = status/priority/resolution |
| `payouts` | list, show, create | payout *request*: only `period_start`, `period_end`, `notes`. Totals calculated on the server. One pending request at a time |
| `bookings`, `earnings`, `features` | list, show | earnings = paid confirmed/completed bookings |

### Where the code is

| Path | What |
|---|---|
| `app/Filament/Api/` | Shared base: `ApiService` (routes `/api/{panel}/{slug}`), base handlers, `Transformer`, `ApiServicePlugin` (Owner panel, no Token resource) |
| `app/Filament/{Admin,Owner}/Resources/{Plural}/Api/` | Per resource: `*ApiService`, `Handlers/`, `Requests/` (validation), `Transformers/` |
| `app/Http/Middleware/EnsureApiPanelAccess.php`, `SetApiLocale.php` | Panel access + language |
| `app/Rules/FileOrPath.php` | Upload-or-existing-path rule |
| `config/api-service.php` | `panel_prefix: false` (prefix is added by our base class; the package's `{panel}` prefix makes Admin and Owner routes with the same slug overwrite each other) |

To change a resource's API, edit its `Requests/*Request.php` (fields/validation) or its handler hooks: `beforeCreate`, `afterCreate`, `beforeUpdate`, `updateRecord`, `beforeDelete`, `scopeQuery`. A handler is enabled when it's listed in `*ApiService::handlers()`.

> Do **not** run `php artisan make:filament-api-service` for these resources. The stock generator assumes the v4 folder layout (`Resources/Halls/HallResource.php`) and would generate classes pointing at resources that don't exist here.

API docs (Scramble): `/docs/api`.

### Still to do for the mobile app

> **Customer app plan, API contract and progress: see [MOBILE_APP.md](MOBILE_APP.md).** Phase 1 (public catalogue `/api/v1`) is implemented.

The panel APIs cover the **admin and hall-owner** apps. A **customer** app needs its own `/api/v1` endpoints that reuse `BookingService`, `AvailabilityService`, `PromoCodeService` and `ThawaniService`. These are business flows, not CRUD:

```
POST   /api/v1/auth/register | forgot-password | verify-otp
GET    /api/v1/regions, /cities?region=, /event-types, /hall-categories, /hall-types   (public)
GET    /api/v1/halls?city=&type=&capacity=&date=        GET /api/v1/halls/{slug}         (public)
GET    /api/v1/halls/{id}/availability?from=&to=
POST   /api/v1/bookings/quote | /bookings | /bookings/{id}/cancel | /bookings/{id}/pay | /bookings/{id}/review
GET    /api/v1/bookings | /bookings/{id} | /bookings/{id}/invoice
GET|POST /api/v1/tickets, /tickets/{id}/reply
POST   /api/v1/devices      (push token)
```

Checklist:
- [x] Sanctum tokens, panel APIs, JSON errors, locale, absolute file URLs, rate limiting
- [ ] Token expiry (`config/sanctum.php` → `expiration`); logout currently revokes all devices
- [ ] Owner booking actions (confirm/cancel from the app). The Owner API is read-only for bookings, like the Owner panel
- [ ] Many-to-many fields not writable via API yet: hall ↔ hall types / event types, user ↔ Shield roles
- [ ] Customer `/api/v1` (above), phone/OTP login, Thawani deep-link return (`majalis://payment/...`)
- [ ] Push notifications (FCM channel + `device_tokens` table)
- [ ] CORS only if a browser client calls the API (mobile apps don't need it)
- [ ] Protect `/docs/api` in production
- [ ] Feature tests (see §7)

---

## 9. Useful commands

```bash
php artisan shield:generate --all         # regenerate permissions/policies
php artisan filament:upgrade              # runs on every composer dump-autoload
php artisan bookings:sync-availability    # see SyncBookingsToAvailability
php artisan queue:work                    # notifications, invoices, reminders
php artisan schedule:work                 # scheduled tasks
vendor/bin/pint                           # code style
```
