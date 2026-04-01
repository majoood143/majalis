<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Tabs;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Actions\Action;

class ManageSettings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.admin.pages.manage-settings';

    public array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('settings.navigation_label');
    }

    public function getTitle(): string
    {
        return __('settings.title');
    }

    public function getSubheading(): ?string
    {
        return __('settings.subheading');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('settings.navigation_group');
    }

    public function mount(): void
    {
        $groups = ['general', 'contact', 'social', 'finance', 'gtag', 'seo'];

        $loaded = [];
        foreach ($groups as $group) {
            $loaded[$group] = Setting::getGroup($group);
        }

        $this->form->fill($loaded);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('settings_tabs')
                    ->tabs([
                        // ─── GENERAL ────────────────────────────────────────
                        Tabs\Tab::make(__('settings.tabs.general'))
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Forms\Components\Section::make(__('settings.sections.site_identity'))
                                    ->description(__('settings.sections.site_identity_desc'))
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('general.site_name')
                                            ->label(__('settings.fields.site_name'))
                                            ->placeholder('Majalis')
                                            ->maxLength(100),

                                        Forms\Components\TextInput::make('general.site_tagline')
                                            ->label(__('settings.fields.site_tagline'))
                                            ->placeholder(__('settings.placeholders.site_tagline'))
                                            ->maxLength(255),

                                        Forms\Components\Textarea::make('general.site_description')
                                            ->label(__('settings.fields.site_description'))
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ]),

                                Forms\Components\Section::make(__('settings.sections.regional'))
                                    ->description(__('settings.sections.regional_desc'))
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\Select::make('general.timezone')
                                            ->label(__('settings.fields.timezone'))
                                            ->options(collect(timezone_identifiers_list())->mapWithKeys(fn ($tz) => [$tz => $tz])->toArray())
                                            ->default('Asia/Muscat')
                                            ->searchable(),

                                        Forms\Components\Select::make('general.default_locale')
                                            ->label(__('settings.fields.default_locale'))
                                            ->options([
                                                'en' => 'English',
                                                'ar' => 'العربية',
                                            ])
                                            ->default('en'),
                                    ]),
                            ]),

                        // ─── CONTACT ────────────────────────────────────────
                        Tabs\Tab::make(__('settings.tabs.contact'))
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Forms\Components\Section::make(__('settings.sections.email_contact'))
                                    ->description(__('settings.sections.email_contact_desc'))
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('contact.email')
                                            ->label(__('settings.fields.email'))
                                            ->email()
                                            ->placeholder('info@majalis.com')
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('contact.support_email')
                                            ->label(__('settings.fields.support_email'))
                                            ->email()
                                            ->placeholder('support@majalis.com')
                                            ->maxLength(255),
                                    ]),

                                Forms\Components\Section::make(__('settings.sections.phone_contact'))
                                    ->description(__('settings.sections.phone_contact_desc'))
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('contact.phone')
                                            ->label(__('settings.fields.phone'))
                                            ->tel()
                                            ->placeholder('+968 24 000 000')
                                            ->maxLength(20),

                                        Forms\Components\TextInput::make('contact.mobile')
                                            ->label(__('settings.fields.mobile'))
                                            ->tel()
                                            ->placeholder('+968 9000 0000')
                                            ->maxLength(20),

                                        Forms\Components\TextInput::make('contact.whatsapp')
                                            ->label(__('settings.fields.whatsapp'))
                                            ->tel()
                                            ->placeholder('+968 9000 0000')
                                            ->helperText(__('settings.helpers.whatsapp'))
                                            ->maxLength(20),

                                        Forms\Components\TextInput::make('contact.fax')
                                            ->label(__('settings.fields.fax'))
                                            ->tel()
                                            ->placeholder('+968 24 000 001')
                                            ->maxLength(20),
                                    ]),

                                Forms\Components\Section::make(__('settings.sections.address'))
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\Textarea::make('contact.address')
                                            ->label(__('settings.fields.address'))
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        Forms\Components\TextInput::make('contact.google_maps_url')
                                            ->label(__('settings.fields.google_maps_url'))
                                            ->url()
                                            ->placeholder('https://maps.google.com/...')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // ─── SOCIAL MEDIA ───────────────────────────────────
                        Tabs\Tab::make(__('settings.tabs.social'))
                            ->icon('heroicon-o-share')
                            ->schema([
                                Forms\Components\Section::make(__('settings.sections.social_media'))
                                    ->description(__('settings.sections.social_media_desc'))
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('social.facebook_url')
                                            ->label('Facebook')
                                            ->url()
                                            ->placeholder('https://facebook.com/majalis')
                                            ->prefixIcon('heroicon-o-globe-alt'),

                                        Forms\Components\TextInput::make('social.instagram_url')
                                            ->label('Instagram')
                                            ->url()
                                            ->placeholder('https://instagram.com/majalis')
                                            ->prefixIcon('heroicon-o-globe-alt'),

                                        Forms\Components\TextInput::make('social.twitter_url')
                                            ->label('X (Twitter)')
                                            ->url()
                                            ->placeholder('https://x.com/majalis')
                                            ->prefixIcon('heroicon-o-globe-alt'),

                                        Forms\Components\TextInput::make('social.snapchat_url')
                                            ->label('Snapchat')
                                            ->url()
                                            ->placeholder('https://snapchat.com/add/majalis')
                                            ->prefixIcon('heroicon-o-globe-alt'),

                                        Forms\Components\TextInput::make('social.youtube_url')
                                            ->label('YouTube')
                                            ->url()
                                            ->placeholder('https://youtube.com/@majalis')
                                            ->prefixIcon('heroicon-o-globe-alt'),

                                        Forms\Components\TextInput::make('social.tiktok_url')
                                            ->label('TikTok')
                                            ->url()
                                            ->placeholder('https://tiktok.com/@majalis')
                                            ->prefixIcon('heroicon-o-globe-alt'),

                                        Forms\Components\TextInput::make('social.linkedin_url')
                                            ->label('LinkedIn')
                                            ->url()
                                            ->placeholder('https://linkedin.com/company/majalis')
                                            ->prefixIcon('heroicon-o-globe-alt'),
                                    ]),
                            ]),

                        // ─── FINANCE ────────────────────────────────────────
                        Tabs\Tab::make(__('settings.tabs.finance'))
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Forms\Components\Section::make(__('settings.sections.tax'))
                                    ->description(__('settings.sections.tax_desc'))
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('finance.vat_rate')
                                            ->label(__('settings.fields.vat_rate'))
                                            ->numeric()
                                            ->suffix('%')
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->default(5)
                                            ->helperText(__('settings.helpers.vat_rate')),

                                        Forms\Components\TextInput::make('finance.currency')
                                            ->label(__('settings.fields.currency'))
                                            ->default('OMR')
                                            ->maxLength(10)
                                            ->helperText(__('settings.helpers.currency')),
                                    ]),

                                Forms\Components\Section::make(__('settings.sections.bank'))
                                    ->description(__('settings.sections.bank_desc'))
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('finance.bank_name')
                                            ->label(__('settings.fields.bank_name'))
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('finance.bank_account_name')
                                            ->label(__('settings.fields.bank_account_name'))
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('finance.bank_iban')
                                            ->label(__('settings.fields.bank_iban'))
                                            ->placeholder('OM00 0000 0000 0000 0000 0000')
                                            ->maxLength(50),

                                        Forms\Components\TextInput::make('finance.bank_swift')
                                            ->label(__('settings.fields.bank_swift'))
                                            ->placeholder('BANKOMAN')
                                            ->maxLength(20),
                                    ]),
                            ]),

                        // ─── SEO / OPEN GRAPH ───────────────────────────────
                        Tabs\Tab::make(__('settings.tabs.seo'))
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Forms\Components\Section::make(__('settings.sections.favicon'))
                                    ->description(__('settings.sections.favicon_desc'))
                                    ->schema([
                                        Forms\Components\FileUpload::make('seo.favicon')
                                            ->label(__('settings.fields.favicon'))
                                            ->image()
                                            ->directory('seo')
                                            ->acceptedFileTypes(['image/x-icon', 'image/png', 'image/svg+xml'])
                                            ->helperText(__('settings.helpers.favicon')),
                                    ]),

                                Forms\Components\Section::make(__('settings.sections.open_graph'))
                                    ->description(__('settings.sections.open_graph_desc'))
                                    ->schema([
                                        // shared fields
                                        Forms\Components\Grid::make(2)->schema([
                                            Forms\Components\Select::make('seo.og_type')
                                                ->label(__('settings.fields.og_type'))
                                                ->options([
                                                    'website' => 'website',
                                                    'article' => 'article',
                                                ])
                                                ->default('website'),

                                            Forms\Components\FileUpload::make('seo.og_image')
                                                ->label(__('settings.fields.og_image'))
                                                ->image()
                                                ->directory('seo')
                                                ->helperText(__('settings.helpers.og_image')),
                                        ]),

                                        // per-language title & description
                                        Tabs::make('og_langs')
                                            ->tabs([
                                                Tabs\Tab::make('English')->schema([
                                                    Forms\Components\TextInput::make('seo.og_title_en')
                                                        ->label(__('settings.fields.og_title') . ' (EN)')
                                                        ->placeholder('Majalis – Book the Perfect Hall')
                                                        ->maxLength(95),

                                                    Forms\Components\Textarea::make('seo.og_description_en')
                                                        ->label(__('settings.fields.og_description') . ' (EN)')
                                                        ->rows(3)
                                                        ->maxLength(200),
                                                ]),

                                                Tabs\Tab::make('العربية')->schema([
                                                    Forms\Components\TextInput::make('seo.og_title_ar')
                                                        ->label(__('settings.fields.og_title') . ' (AR)')
                                                        ->placeholder('مجالس – احجز القاعة المثالية')
                                                        ->maxLength(95)
                                                        ->extraInputAttributes(['dir' => 'rtl']),

                                                    Forms\Components\Textarea::make('seo.og_description_ar')
                                                        ->label(__('settings.fields.og_description') . ' (AR)')
                                                        ->rows(3)
                                                        ->maxLength(200)
                                                        ->extraInputAttributes(['dir' => 'rtl']),
                                                ]),
                                            ]),
                                    ]),

                                Forms\Components\Section::make(__('settings.sections.twitter_card'))
                                    ->description(__('settings.sections.twitter_card_desc'))
                                    ->schema([
                                        // shared fields
                                        Forms\Components\Grid::make(2)->schema([
                                            Forms\Components\Select::make('seo.twitter_card')
                                                ->label(__('settings.fields.twitter_card'))
                                                ->options([
                                                    'summary'             => 'Summary',
                                                    'summary_large_image' => 'Summary with Large Image',
                                                ])
                                                ->default('summary_large_image'),

                                            Forms\Components\TextInput::make('seo.twitter_site')
                                                ->label(__('settings.fields.twitter_site'))
                                                ->placeholder('@majalis')
                                                ->maxLength(50)
                                                ->helperText(__('settings.helpers.twitter_site')),

                                            Forms\Components\FileUpload::make('seo.twitter_image')
                                                ->label(__('settings.fields.twitter_image'))
                                                ->image()
                                                ->directory('seo')
                                                ->helperText(__('settings.helpers.twitter_image'))
                                                ->columnSpanFull(),
                                        ]),

                                        // per-language title & description
                                        Tabs::make('tw_langs')
                                            ->tabs([
                                                Tabs\Tab::make('English')->schema([
                                                    Forms\Components\TextInput::make('seo.twitter_title_en')
                                                        ->label(__('settings.fields.twitter_title') . ' (EN)')
                                                        ->placeholder('Majalis – Book the Perfect Hall')
                                                        ->maxLength(70),

                                                    Forms\Components\Textarea::make('seo.twitter_description_en')
                                                        ->label(__('settings.fields.twitter_description') . ' (EN)')
                                                        ->rows(3)
                                                        ->maxLength(200),
                                                ]),

                                                Tabs\Tab::make('العربية')->schema([
                                                    Forms\Components\TextInput::make('seo.twitter_title_ar')
                                                        ->label(__('settings.fields.twitter_title') . ' (AR)')
                                                        ->placeholder('مجالس – احجز القاعة المثالية')
                                                        ->maxLength(70)
                                                        ->extraInputAttributes(['dir' => 'rtl']),

                                                    Forms\Components\Textarea::make('seo.twitter_description_ar')
                                                        ->label(__('settings.fields.twitter_description') . ' (AR)')
                                                        ->rows(3)
                                                        ->maxLength(200)
                                                        ->extraInputAttributes(['dir' => 'rtl']),
                                                ]),
                                            ]),
                                    ]),
                            ]),

                        // ─── GOOGLE ANALYTICS ───────────────────────────────
                        Tabs\Tab::make(__('settings.tabs.gtag'))
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Forms\Components\Section::make(__('settings.sections.gtag'))
                                    ->description(__('settings.sections.gtag_desc'))
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('gtag.gtag_id')
                                            ->label(__('settings.fields.gtag_id'))
                                            ->placeholder('G-XXXXXXXXXX')
                                            ->maxLength(50)
                                            ->columnSpanFull(),

                                        Forms\Components\Toggle::make('gtag.enabled')
                                            ->label(__('settings.fields.gtag_enabled'))
                                            ->default(true),

                                        Forms\Components\Toggle::make('gtag.anonymize_ip')
                                            ->label(__('settings.fields.anonymize_ip'))
                                            ->default(false)
                                            ->helperText(__('settings.helpers.anonymize_ip')),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $group => $settings) {
            if (! is_array($settings)) {
                continue;
            }
            foreach ($settings as $name => $value) {
                Setting::set($group, $name, $value);
            }
        }

        Notification::make()
            ->title(__('settings.saved'))
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('settings.save_button'))
                ->submit('save')
                ->icon('heroicon-o-check')
                ->color('primary'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label(__('settings.save_button'))
                ->action('save')
                ->icon('heroicon-o-check')
                ->color('primary'),
        ];
    }
}
