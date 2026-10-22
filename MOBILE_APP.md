# Majalis Mobile App: Project Brief & Plan

> Everything a developer (or an AI assistant) needs to build the Majalis mobile app on top of this Laravel backend.
> Read with [PROJECT_OVERVIEW.md](PROJECT_OVERVIEW.md), which covers the whole platform and the admin/owner panel APIs in depth.
>
> _Written 2026-10-22. Status: **Phases 1–3 done** (catalogue, auth, booking + payment, favourites — API + React Native app in `../majalis-app`); Phases 4–7 planned. Design: Majalis App Redesign canvas._

---

## 1. What we are building

Majalis is a **hall (majlis) booking marketplace for Oman**. The web platform already exists:

| Who | Today (web) | Mobile target |
|---|---|---|
| **Customer** | Blade site: search halls, book, pay with Thawani, manage bookings, tickets, reviews | **Customer app**: the main deliverable |
| **Hall owner** | Filament panel `/owner` | Owner screens in the same app later (API `/api/owner/*` already exists) |
| **Admin** | Filament panel `/admin` | Stays on the web. API `/api/admin/*` exists if ever needed |

**One app, role-aware.** Customers see the booking experience. A user who is also an active, verified hall owner gets an "Owner" tab (bookings, calendar, earnings) in a later phase.

### Non-negotiables

| | |
|---|---|
| Languages | **Arabic (default, RTL)** + English. Every screen must work in RTL |
| Currency | **OMR, 3 decimals** (e.g. `150.000 OMR`). Thawani works in **baisa** (1 OMR = 1000 baisa), but the server handles that conversion |
| Timezone | `Asia/Muscat`. Dates go over the API as `Y-m-d` strings, timestamps as ISO-8601 |
| Payments | **Thawani** hosted checkout (no native SDK). Opened in an in-app browser, result comes back by deep link |
| Source of truth | The **server** decides price, availability and payment status. The app never calculates a price it charges for |

---

## 2. Mobile stack: React Native (Expo)

**React Native with Expo SDK 58 + TypeScript**, one codebase for iOS and Android. The app lives in a separate repo: **`../majalis-app`** (sibling folder of this backend).

| Concern | Package / approach | Status |
|---|---|---|
| Framework | Expo SDK 58, React Native 0.88, React 19, React Compiler on | ✅ |
| Navigation + deep links | **expo-router** (file-based, `src/app/`), native tabs, scheme `majalis://` | ✅ |
| Server state / caching | **@tanstack/react-query** (query keys include the language) | ✅ |
| HTTP | small `fetch` wrapper `src/api/client.ts` (Bearer token, `Accept-Language`, typed `ApiError` with 422 field errors) | ✅ |
| Types | hand-written TS types for the API contract, `src/api/types.ts` | ✅ |
| i18n / RTL | **i18next + react-i18next**, `expo-localization`; RTL via `I18nManager.forceRTL` + `expo-updates` reload; expo-router `LocaleProvider` for navigation direction | ✅ |
| Token / prefs storage | **expo-secure-store** (never AsyncStorage for the token) | ✅ (language) |
| Images | **expo-image** | ✅ |
| HTML text | `htmlToText()` for now (descriptions are **HTML** from the rich editor); add a renderer if formatting is needed | ✅ |
| Calendar | day strip + slot grid fed by `/halls/{slug}/availability` | ✅ |
| Payment | **expo-web-browser** `openAuthSessionAsync(payment_url, 'majalis://payment/result')` | Phase 3 |
| Push | **expo-notifications** (FCM on Android, APNs on iOS) | Phase 5 |
| Maps | `react-native-maps` (needs a development build) | Phase 4 |
| Forms | `react-hook-form` + `zod` 4, with a small local `zodResolver` (`src/lib/forms.ts`); server 422 errors map onto fields | ✅ |
| Builds / release | **EAS Build / Submit / Update** | Phase 7 |

> RTL switching does **not** work in Expo Go (it resets RTL). Test Arabic layout in a development build: `npx expo run:ios` or `eas build --profile development`.

### App structure (`majalis-app/`)

```
src/
  app/                     expo-router screens (every file = route)
    _layout.tsx            providers: React Query, LocaleProvider, theme, filters; root Stack
    (tabs)/_layout.tsx     native tabs: Halls, Account
    (tabs)/index.tsx       hall search: region chips, results, infinite scroll, pull-to-refresh, suggested dates
    (tabs)/account.tsx     language switch, sign-in placeholder
    hall/[slug].tsx        hall details: gallery, slot prices, availability, services, policies, contact, reviews
    filters.tsx            modal: date, slot, city, hall type, guests, sort
  api/                     client.ts, types.ts, catalog.ts (React Query hooks)
  features/halls/          filters-context.tsx, hall-card.tsx
  components/              Button, Chip, Section, State views, Themed text/view
  i18n/                    index.ts, language.ts, locales/{ar,en}.json
  lib/format.ts            OMR (3 decimals), dates, HTML → text
  constants/theme.ts       brand colours (sand #B9916D, same as the website)
  config/env.ts            EXPO_PUBLIC_API_URL
```

This repo stays the backend; the app never talks to the database or Filament directly, only to `/api/v1` (and later `/api/owner`).

---

## 3. Backend facts the app depends on

### Domain (simplified)

```
Region ─< City ─< Hall >── owner (User with HallOwner profile)
Hall ─< ExtraService, HallImage, HallAvailability (per date+slot: open/closed + custom_price), Review
Hall >─< HallType, EventType ; Hall.features = JSON array of HallFeature ids
User ─< Booking ─< BookingDay (1–3 days), BookingExtraService, Payment, Review
```

### Time slots (use the `TimeSlot` enum; the API exposes it at `GET /api/v1/config`)

| key | label | time |
|---|---|---|
| `morning` | صباحي | 08:00–12:00 |
| `afternoon` | ظهري | 12:00–17:00 |
| `evening` | مسائي | 17:00–23:00 |
| `full_day` | يوم كامل | 08:00–23:00 |

Rules: `full_day` blocks every other slot that day and vice versa. A booking can cover **1–3 (date, slot) pairs** (multi-day). Booking window: **≥ 24 h ahead, ≤ 90 days ahead** (`config/majalis.php`).

### Pricing (what the server does, so the app can explain it)

```
day price      = HallAvailability.custom_price  ?? hall.pricing_override[slot] ?? hall.price_per_slot
hall_price     = Σ day prices
subtotal       = hall_price + Σ selected extra services
platform_fee   = ServiceFeeSetting (hall > owner > global; % or fixed). Customer pays this
total_amount   = subtotal + platform_fee − promo discount
advance        = if hall.allows_advance_payment → fixed or % of total (≥ minimum); balance paid later
```
Commission (`CommissionSetting`) is **owner-side only**. It must never appear in a customer API response.

### Statuses

- `BookingStatus`: `pending → confirmed → completed`, or `cancelled` / `refunded`
- `booking.payment_status`: `pending | partial | paid | failed | refunded | partially_refunded`. `partial` = advance paid, balance due. It is set by `PaymentService` but **missing from the `PaymentStatus` enum**; add it before the API casts to the enum
- `Payment` rows: `pending → processing → paid | failed`. One booking can have several attempts

### Services to reuse (never re-implement in controllers)

| Service | Use for |
|---|---|
| `AvailabilityService` | search with dates, open slots per date, slot prices, nearby-date suggestions |
| `BookingService` | `checkAvailabilityForDays()`, `createBooking()` (pricing, fees, BookingDay rows, availability sync) |
| `PromoCodeService` | `validate()`, `applyToBooking()`, `recordUsage()` |
| `PaymentService` | `initiatePayment()`, `handlePaymentSuccess()` (with session↔payment binding check), refunds |
| `Booking` model | `calculateAdvancePayment()`, `cancel()` (computes refund from cancellation policy), `canReceiveReview()` |
| `InvoiceService` / `BookingPdfService` | invoice PDF download |

---

## 4. API contract: customer app (`/api/v1`)

Conventions:
- Base URL: `https://<host>/api/v1`. Always send `Accept: application/json` and `Accept-Language: ar|en` (or `?lang=`)
- Auth: `Authorization: Bearer <sanctum token>`
- Single record → `{ "data": {...} }`. List → `{ "data": [...], "links": {...}, "meta": {...} }` (Laravel paginator)
- Errors: `401`, `403`, `404`, `422 { message, errors: { field: [..] } }`, `429` (rate limit)
- Money: JSON numbers in OMR (`150`, `414.72`); format to 3 decimals in the app. Translatable fields come back already in the requested language
- Responses are built by explicit `App\Http\Resources\Api\V1\*` classes. Add a field there deliberately; nothing leaks automatically

### ✅ Phase 1: public catalogue (implemented)

| Method | Endpoint | Notes |
|---|---|---|
| GET | `/config` | currency, timezone, locales, `time_slots[]` (key, label, start, end), `max_booking_days`, `advance_days`, `min_advance_hours` |
| GET | `/regions` | active, ordered |
| GET | `/cities?region_id=` | |
| GET | `/hall-types`, `/hall-features`, `/event-types` | filter + form options |
| GET | `/halls` | **browse**: `region_id, city_id, hall_type_id, min_guests, max_price, features[], sort=rating\|price_low\|price_high\|capacity\|latest, per_page≤50` |
| GET | `/halls?date=Y-m-d[&time_slot=]` | **date search**: each hall gets `is_available`; `meta.available_count`, `meta.suggested_dates[]` when nothing is free |
| GET | `/halls/{slug}` | full details: description (HTML), contacts, `slot_prices`, cancellation policy, `advance_payment`, `images[]`, `gallery_urls[]`, `features[]`, `extra_services[]`, last 5 approved `reviews[]` |
| GET | `/halls/{slug}/availability?from=&to=` | per day → 4 slots `{key, available, price}`. Max 31 days per call, clamped to the 90-day window; slots inside the 24 h lead time come back `available: false`. Throttled 30/min |

Code: [routes/api.php](routes/api.php), [app/Http/Controllers/Api/V1/](app/Http/Controllers/Api/V1/), [app/Http/Resources/Api/V1/](app/Http/Resources/Api/V1/).

> Availability semantics: a slot is free only if the owner **opened** it in `hall_availabilities` **and** no active booking/BookingDay holds it. A date with no availability rows = hall closed. This matches the website's search.

### ✅ Phase 2: customer auth & profile (implemented 2026-10-22)

| Method | Endpoint | Notes |
|---|---|---|
| POST | `/auth/register` | `name, email, phone?, password, password_confirmation, device_name` → 201 `{token, token_expires_at, user}`. Fires `Registered` like the website: verification email + `LinkGuestBookingsOnRegistration` |
| POST | `/auth/login` | `email, password, device_name` → `{token, token_expires_at, user}`. One token **per device** (same `device_name` replaces the old token). Inactive account → 403. Any role can sign in; `user.can_use_owner_mode` tells the app whether to offer owner screens |
| POST | `/auth/logout` | revokes the **current** token only |
| POST | `/auth/forgot-password` | sends the website's reset email. Always the same answer (no email enumeration) |
| GET | `/me` | `UserResource`: id, name, email, `email_verified`, phone, role, `language_preference`, `can_use_owner_mode` |
| PATCH | `/me` | partial: `name, email, phone, language_preference`. A new email resets verification and re-sends the email |
| PUT | `/me/password` | `current_password, password, password_confirmation`. **Signs out every other device** |
| DELETE | `/me` | `password`. 409 while the user has upcoming pending/confirmed bookings (same rule as the website). Soft-deletes the user, revokes all tokens and device tokens (**App Store requirement**) |
| POST | `/devices` | `token, platform (ios\|android), device_name?` → upsert; a token moves to whoever signed in last |
| DELETE | `/devices/{token}` | unregister before sign-out |

Details:
- Signed-in routes use `auth:sanctum` + `api.active` (`EnsureApiUserIsActive`): a deactivated user's token is revoked and the app gets 403.
- Rate limits are **named** limiters (`AppServiceProvider::configureApiRateLimits`): `api-v1` 120/min, `api-v1-auth` 6/min per email+IP and 20/min per IP, `api-v1-password-reset` 3/min, `api-v1-sensitive` 6/min (password change, deletion), `api-v1-availability` 30/min. Anonymous `throttle:N,1` limits share one counter per user, so don't nest them.
- Token lifetime: `SANCTUM_EXPIRATION` (30 days). On any 401 the app clears the session and shows signed-out state.
- Arabic messages: added `resources/lang/ar/validation.php`, `passwords.php` and auth keys (they were missing, so Arabic users saw English errors on the website too).
- Table `device_tokens` (`App\Models\DeviceToken`, `User::deviceTokens()`).
- Open: soft-deleted users keep their email, so the same email can't register again. Decide whether to anonymise the email on deletion.

### ✅ Phase 3: booking & payment (implemented 2026-10-22)

All signed-in (`auth:sanctum` + `api.active`).

| Method | Endpoint | Notes |
|---|---|---|
| POST | `/bookings/quote` | `{hall_id, days:[{date,time_slot}] (1–3, unique dates), extra_services[], promo_code?, payment_type: full\|advance}` → per-day price + `available`, services, `hall_price`, `subtotal`, `service_fee`, `discount`, `total`, `promo {code, valid, message}`, `payment` (chosen plan) and `payment_options` (advance + full). Writes nothing |
| POST | `/bookings` | quote body + `number_of_guests` (hall capacity), `event_type_id?` (from `/event-types`), `customer_name?`, `customer_phone?` (required only when the account has no phone), `customer_notes?` → 201 booking. 409 `{unavailable_days}` when a slot is gone; 422 for an invalid promo |
| GET | `/bookings?status=upcoming\|past\|cancelled` | paginated, own bookings only |
| GET | `/bookings/{booking_number}` | detail: days, services, event types, pricing, `payment {type, advance_amount, balance_due, balance_paid, amount_due_now}`, `can_pay`, `can_cancel`, `can_review`, `refund_amount` |
| POST | `/bookings/{booking_number}/pay` | → `{payment_url, amount, return_url_prefix}`. 409 unless pending & unpaid & not past. Retry = call again |
| POST | `/bookings/{booking_number}/cancel` | `Booking::cancel()`: refund = amount paid online − hall cancellation fee (an admin processes the refund) |
| GET | `/bookings/{booking_number}/invoice` | PDF (`BookingPdfService`) |
| GET (public) | `/payments/thawani/{payment_reference}/{success\|cancel}` | Thawani's success/cancel URL for app payments. Verifies with `PaymentService::handlePaymentSuccess()` (session↔payment binding), then 302 → `majalis://payment/result?booking=…&status=paid\|cancelled\|failed` |
| GET | `/favorites`; PUT/DELETE `/halls/{slug}/favorite` | "Saved" tab (table `favorite_halls`). Hall lists/details carry `is_favorite` when a token is sent |
| GET | `/halls?q=` | text search on name (ar/en) and address, both search modes |

**Money rules (same as the guest checkout):** advance = hall advance on the *discounted* subtotal + the full service fee; commission and owner payout are computed on the advance only, because **the balance is paid to the hall directly** and marked received by the owner/admin in the panels. So the app shows "balance due to the hall", never a "pay balance" button (a change from the design canvas).

**App flow:** Hall → Choose dates (calendar from `/availability`, up to 3 days, slot per day) → Review & pay (live `/quote` on every change) → `POST /bookings` → `POST /pay` → `WebBrowser.openAuthSessionAsync(payment_url, "majalis://payment/result")` → booking detail re-read from the API. A booking created on a failed/closed payment is reused on retry, never duplicated.

**Known gaps:** if the customer pays but closes the browser before Thawani redirects, the booking stays "awaiting payment" until a webhook or retry verifies it — add a Thawani webhook (or a scheduled `getSessionStatus` sweep). `APP_URL` must be reachable from the phone for the return route (LAN IP on a device; public HTTPS in production).

### ⏳ Phase 4: support, notifications, content

| Endpoint | Notes |
|---|---|
| `GET/POST /tickets`, `GET /tickets/{id}`, `POST /tickets/{id}/reply` (multipart attachments), `POST /tickets/{id}/close\|reopen\|rate` | reuse `Customer\TicketController` logic |
| `GET /notifications`, `POST /notifications/{id}/read` | database notifications |
| `GET /pages/{slug}` | about / terms / privacy (required links in both stores) |
| Push | new FCM notification channel; send on booking confirmed/cancelled, payment received, reminder (`SendBookingReminder`), review request |

### Phase 6: owner mode (API already exists)

`/api/owner/*` (bookings, earnings, availability, halls, payouts, expenses, tickets) is documented in PROJECT_OVERVIEW.md §8. Missing for the app: **confirm/cancel booking actions** for owners, and push for new bookings.

---

## 5. Things found during analysis (fix while building the API)

| # | Issue | Impact on the app | Where |
|---|---|---|---|
| 1 | `BookingService::checkAvailability()` ignores `hall_availabilities` (only checks existing bookings), while search/calendar require an "open" row | App could book a slot the owner closed | ✅ fixed 2026-10-22 — `BookingService::isDayBookable()` checks the window, owner-opened slots and bookings; `AvailabilityService` now also counts `booking_days` (extra days of multi-day bookings were shown as free). |
| 2 | `BookingService::calculatePricing()` uses only `price_per_slot`: no per-slot override, no custom date price, no multi-day | Quote ≠ charged price | ✅ fixed — `BookingService::priceBreakdown()` is shared by `quote()` and `createBooking()`; extra services must belong to the hall. |
| 3 | Logged-in web booking has **no promo-code** step (only the guest flow applies promos) | Decide: support promos in the app (recommended) | ✅ API supports promos; promo usage is now recorded in `PaymentService::handlePaymentSuccess()` for every flow. (Website signed-in checkout still has no promo field.) |
| 4 | Web customer cancel sets `status=cancelled` directly: skips `Booking::cancel()` (refund amount) and ignores `cancellation_hours` | App must use `Booking::cancel()` + policy check | ✅ API uses `Booking::cancel()`, which now also refunds advance-only (`partial`) payments. (Website cancel button unchanged.) |
| 5 | Three different slot-time definitions: `config/majalis.php` (08–14, 16–22), `TimeSlot` enum, `AvailabilityService::TIME_SLOTS` | Confusing labels | API uses the `TimeSlot` enum. Align the others |
| 6 | `generateBookingNumber()` reads the last number then +1, so two concurrent bookings can collide | Rare 500 on create | ✅ fixed — `withTrashed()` + `lockForUpdate()` in `BookingService::generateBookingNumber()`. |
| 7 | Payment success URL is a web-session route | Blocks app payments | ✅ fixed — `initiatePayment()` takes return URLs; checkout host now follows `THAWANI_BASE_URL` (was hard-coded to UAT). |
| 8 | Hall/service descriptions are HTML | Plain text via `htmlToText()` for now | |
| 9 | `php artisan test` can't migrate on SQLite (migration order) | No automated API tests yet | PROJECT_OVERVIEW.md §7. Fix first in Phase 2 |
| 10 | Large amount of **uncommitted work on `main`** (panel APIs, Sanctum, hardening, this v1 API) | Risk of loss | Commit/branch before Phase 2 |

---

## 6. Roadmap

| Phase | Scope | Exit criteria |
|---|---|---|
| **0. Housekeeping** | Commit current work on a branch; fix migration order so feature tests run; production `.env` review (APP_DEBUG off, HTTPS, queue worker) | `php artisan test` green on CI |
| **1. Catalogue API** ✅ | `/api/v1` config, lookups, hall search, details, availability | Done 2026-10-22 |
| **2. Auth API + screens** ✅ | register/login/logout per device, profile, password, delete account, device tokens; app sign-in/register/forgot/profile/password/delete screens | Done 2026-10-22 (feature tests still blocked by the migration order, see §5 #9) |
| **3. Booking + payment API** ✅ | quote, create, list, detail, cancel, pay (Thawani app return), invoice, review; fixes #1–#4, #6–#7 | End-to-end UAT payment on Thawani sandbox from a phone |
| **4. App MVP (React Native)** 🟡 | ✅ 2026-10-22: redesign (Sand & Ink), list + map, filters, hall details, auth, booking + Thawani payment, bookings, saved. Next: push, reviews, tickets, Android build | TestFlight + Play internal testing build |
| **5. Support & push** | tickets, notifications, FCM, reminders, reviews | Push received on both platforms |
| **6. Owner mode** | owner tab on `/api/owner/*` + confirm/cancel actions | |
| **7. Store release** | icons, splash, store listings (AR/EN), privacy policy URL, deep-link domain files (`apple-app-site-association`, `assetlinks.json`) | Live in App Store & Google Play |

---

## 7. Skills needed

**Backend (Laravel)**
- Laravel 12: routing, Form Requests, API Resources, policies, events/queues, scheduler
- Sanctum token auth (per-device tokens, abilities, expiry)
- This project's services (§3) and the Filament v4 panel API layer (`app/Filament/Api`, see PROJECT_OVERVIEW §8)
- Thawani Checkout API (sessions, success/cancel redirects, refunds, UAT vs live keys)
- Firebase Cloud Messaging (HTTP v1 API, service-account auth)
- PHPUnit feature tests against MySQL; API docs with Scramble (`/docs/api`)

**Mobile (React Native / Expo)**
- TypeScript, React 19 hooks, Expo SDK 58 (read the versioned docs; APIs change every SDK), expo-router, TanStack Query
- RTL layouts (`start`/`end` styles, `I18nManager`), Arabic typography, i18next, `Intl` formatting for `ar-OM`
- Deep links / universal links (iOS Associated Domains, Android App Links) and custom scheme `majalis://`
- In-app browser auth/payment flows (`expo-web-browser` `openAuthSessionAsync`)
- Maps, image caching, secure storage, push notifications
- EAS Build/Submit/Update, development builds vs Expo Go, App Store / Play Console release, signing, privacy forms (data collected: name, email, phone, payment via 3rd party)

**Product / QA**
- Arabic UX copy review, Oman payment testing (Thawani UAT cards), accessibility, device matrix (small Android + iPhone)

---

## 8. Working rules for this codebase

- Customer endpoints go in `routes/api.php` under the `v1` group (`api.locale` + throttle). Controllers in `app/Http/Controllers/Api/V1`, responses in `app/Http/Resources/Api/V1` (explicit fields only).
- Business logic stays in `app/Services`. Controllers validate → call service → return resource. Web and app must hit the **same** service method so prices/availability never diverge.
- Never expose: `commission_*`, `owner_payout`, `admin_notes`, `gateway_response`, other users' data, guest tokens.
- Admin/owner APIs: edit existing `Resources/{Plural}/Api/` folders. **Never** run `php artisan make:filament-api-service` (see PROJECT_OVERVIEW §8).
- Version the contract: breaking changes go to `/api/v2`. Additive fields are fine in v1.
- Quick local check without a server:
  ```bash
  php artisan route:list --path=api/v1
  curl -H 'Accept: application/json' -H 'Accept-Language: ar' 'http://localhost:8000/api/v1/halls?per_page=5'
  ```
