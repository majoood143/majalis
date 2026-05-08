<?php

namespace App\Filament\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use App\Filament\Admin\Resources\HallOwnerResource\Pages\ListHallOwners;
use App\Filament\Admin\Resources\HallOwnerResource\Pages\CreateHallOwner;
use App\Filament\Admin\Resources\HallOwnerResource\Pages\ViewHallOwner;
use App\Filament\Admin\Resources\HallOwnerResource\Pages\EditHallOwner;
use App\Filament\Admin\Resources\HallOwnerResource\Pages;
use App\Models\HallOwner;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class HallOwnerResource extends Resource
{
    protected static ?string $model = HallOwner::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-storefront';

    protected static string | \UnitEnum | null $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('hall-owner.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('hall-owner.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('hall-owner.navigation_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make(__('hall-owner.tabs.business_info'))
                    ->tabs([
                        Tab::make(__('hall-owner.tabs.business_info'))
                            ->icon('heroicon-o-building-office')
                            ->schema([
                                Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->label(__('hall-owner.fields.user_id'))
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                TextInput::make('business_name')
                                    ->label(__('hall-owner.fields.business_name'))
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('business_name_ar')
                                    ->label(__('hall-owner.fields.business_name_ar'))
                                    ->maxLength(255),

                                FileUpload::make('logo')
                                    ->label(__('hall-owner.fields.logo'))
                                    ->disk('public')
                                    ->directory('logos/hall-owners')
                                    ->image()
                                    ->imageEditor()
                                    ->maxSize(2048)
                                    ->columnSpanFull(),

                                TextInput::make('commercial_registration')
                                    ->label(__('hall-owner.fields.commercial_registration'))
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),

                                TextInput::make('tax_number')
                                    ->label(__('hall-owner.fields.tax_number'))
                                    ->maxLength(255),
                            ])->columns(2),

                        Tab::make(__('hall-owner.tabs.contact'))
                            ->icon('heroicon-o-phone')
                            ->schema([
                                TextInput::make('business_phone')
                                    ->label(__('hall-owner.fields.business_phone'))
                                    ->numeric()
                                    ->required()
                                    ->maxLength(20),

                                TextInput::make('business_email')
                                    ->label(__('hall-owner.fields.business_email'))
                                    ->email()
                                    ->maxLength(255),

                                Textarea::make('business_address')
                                    ->label(__('hall-owner.fields.business_address'))
                                    ->required()
                                    ->rows(3)
                                    ->columnSpanFull(),

                                Textarea::make('business_address_ar')
                                    ->label(__('hall-owner.fields.business_address_ar'))
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Tab::make(__('hall-owner.tabs.bank_details'))
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                TextInput::make('bank_name')
                                    ->label(__('hall-owner.fields.bank_name'))
                                    ->maxLength(255),

                                TextInput::make('bank_account_name')
                                    ->label(__('hall-owner.fields.bank_account_name'))
                                    ->maxLength(255),

                                TextInput::make('bank_account_number')
                                    ->label(__('hall-owner.fields.bank_account_number'))
                                    ->maxLength(17)
                                    ->rules([
                                        'required',

                                        'regex:/^\d+$/', // ensures only digits
                                    ])
                                    ->numeric(),





                                TextInput::make('iban')
                                    ->label(__('hall-owner.fields.iban'))
                                    ->maxLength(255),

                            ])->columns(2),

                        Tab::make(__('hall-owner.tabs.documents'))
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                FileUpload::make('commercial_registration_document')
                                    ->disk('public')
                                    ->directory('documents/cr')
                                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                                    ->downloadable()
                                    ->columnSpanFull(),

                                FileUpload::make('tax_certificate')
                                    ->disk('public')
                                    ->directory('documents/tax')
                                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                                    ->downloadable()
                                    ->columnSpanFull(),

                                FileUpload::make('identity_document')
                                    ->disk('public')
                                    ->directory('documents/identity')
                                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                                    ->downloadable()
                                    ->columnSpanFull(),
                            ]),

                        Tab::make(__('hall-owner.tabs.verification'))
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Toggle::make('is_verified')
                                    ->label(__('hall-owner.fields.is_verified'))
                                    ->inline(false),

                                Toggle::make('is_active')
                                    ->label(__('hall-owner.fields.is_active'))
                                    ->inline(false),

                                Textarea::make('verification_notes')
                                    ->label(__('hall-owner.fields.verification_notes'))
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Tab::make(__('hall-owner.tabs.commission'))
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Select::make('commission_type')
                                    ->label(__('hall-owner.fields.commission_type'))
                                    ->options([
                                        'percentage' => __('hall-owner.options.percentage'),
                                        'fixed' => __('hall-owner.options.fixed'),
                                    ]),

                                TextInput::make('commission_value')
                                    ->label(__('hall-owner.fields.commission_value'))
                                    ->numeric()
                                    ->step(0.01),

                                Placeholder::make('note')
                                    ->content(__('hall-owner.note'))
                                    ->columnSpanFull(),
                            ])->columns(2),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('hall-owner.columns.owner_name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('business_name')
                    ->label(__('hall-owner.columns.business_name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('commercial_registration')
                    ->label(__('hall-owner.columns.commercial_registration'))
                    ->searchable()
                    ->copyable(),

                TextColumn::make('business_phone')
                    ->label(__('hall-owner.columns.business_phone'))
                    ->searchable()
                    ->copyable(),

                IconColumn::make('is_verified')
                    ->label(__('hall-owner.columns.is_verified'))
                    ->boolean()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label(__('hall-owner.columns.is_active'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('hall-owner.columns.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_verified')
                    ->label(__('hall-owner.filters.verified'))
                    ->boolean()
                    ->native(false),

                TernaryFilter::make('is_active')
                    ->label(__('hall-owner.filters.active'))
                    ->boolean()
                    ->native(false),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),

                    Action::make('verify')
                        ->label(__('hall-owner.actions.verify'))
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->schema([
                            Textarea::make('notes')
                                ->label(__('hall-owner.fields.notes')),
                        ])
                        ->action(function (HallOwner $record, array $data) {
                            $record->verify(Auth::id(), $data['notes'] ?? null);
                        })
                        ->visible(fn(HallOwner $record) => !$record->is_verified),

                    Action::make('reject')
                        ->label(__('hall-owner.actions.reject'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->schema([
                            Textarea::make('notes')
                                ->label(__('hall-owner.fields.rejection_reason'))
                                ->required(),
                        ])
                        ->action(fn(HallOwner $record, array $data) => $record->reject($data['notes']))
                        ->visible(fn(HallOwner $record) => $record->is_verified),

                    DeleteAction::make(),
                // TODO: ActivityLogTimelineTableAction removed (rmsramos v3-only) - replace with v4 equivalent,
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('hall-owner.infolist.business_information'))
                    ->schema([
                        ImageEntry::make('logo')
                            ->label(__('hall-owner.fields.logo'))
                            ->disk('public')
                            ->height(80)
                            ->circular()
                            ->columnSpanFull(),
                        TextEntry::make('user.name')
                            ->label(__('hall-owner.infolist.owner')),
                        TextEntry::make('business_name')
                            ->label(__('hall-owner.infolist.business_name')),

                        TextEntry::make('business_name_ar')
                            ->label(__('hall-owner.infolist.business_name_ar')),
                        TextEntry::make('commercial_registration')
                            ->copyable(),
                        TextEntry::make('tax_number')
                            ->copyable(),
                    ])->columns(3),

                Section::make(__('hall-owner.infolist.contact_information'))
                    ->schema([
                        TextEntry::make('business_phone')
                            ->copyable(),
                        TextEntry::make('business_email')
                            ->copyable(),
                        TextEntry::make('business_address'),
                    ])->columns(2),

                Section::make(__('hall-owner.infolist.bank_details'))
                    ->schema([
                        TextEntry::make('bank_name'),
                        TextEntry::make('bank_account_name'),
                        TextEntry::make('bank_account_number')
                            ->copyable(),
                        TextEntry::make('iban')
                            ->copyable(),
                    ])->columns(2),

                Section::make(__('hall-owner.infolist.verification_status'))
                    ->schema([
                        TextEntry::make('is_verified')
                            ->badge()
                            ->color(fn($state) => $state ? 'success' : 'warning')
                            ->formatStateUsing(fn($state) => $state
                                ? __('hall-owner.infolist.verified')
                                : __('hall-owner.infolist.pending')),
                        TextEntry::make('verified_at')
                            ->dateTime(),
                        TextEntry::make('verifiedBy.name')
                            ->label(__('hall-owner.infolist.verified_by')),
                        TextEntry::make('verification_notes')
                            ->columnSpanFull(),
                    ])->columns(3),

                Section::make(__('hall-owner.infolist.documents'))
                    ->schema([
                        TextEntry::make('commercial_registration_document')
                            ->label(__('hall-owner.infolist.commercial_registration_document'))
                            ->formatStateUsing(function ($state) {
                                if (!$state) {
                                    return '<span class="text-gray-400 italic">' . __('hall-owner.infolist.not_submitted') . '</span>';
                                }
                                $url = asset('storage/' . $state);
                                $ext = strtolower(pathinfo($state, PATHINFO_EXTENSION));
                                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                    return '<a href="' . $url . '" target="_blank"><img src="' . $url . '" class="max-h-40 rounded border" /></a>';
                                }
                                return '<a href="' . $url . '" target="_blank" class="inline-flex items-center gap-1 text-primary-600 hover:underline font-medium"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg> View Document</a>';
                            })
                            ->html(),

                        TextEntry::make('tax_certificate')
                            ->label(__('hall-owner.infolist.tax_certificate'))
                            ->formatStateUsing(function ($state) {
                                if (!$state) {
                                    return '<span class="text-gray-400 italic">' . __('hall-owner.infolist.not_submitted') . '</span>';
                                }
                                $url = asset('storage/' . $state);
                                $ext = strtolower(pathinfo($state, PATHINFO_EXTENSION));
                                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                    return '<a href="' . $url . '" target="_blank"><img src="' . $url . '" class="max-h-40 rounded border" /></a>';
                                }
                                return '<a href="' . $url . '" target="_blank" class="inline-flex items-center gap-1 text-primary-600 hover:underline font-medium"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg> View Document</a>';
                            })
                            ->html(),

                        TextEntry::make('identity_document')
                            ->label(__('hall-owner.infolist.identity_document'))
                            ->formatStateUsing(function ($state) {
                                if (!$state) {
                                    return '<span class="text-gray-400 italic">' . __('hall-owner.infolist.not_submitted') . '</span>';
                                }
                                $url = asset('storage/' . $state);
                                $ext = strtolower(pathinfo($state, PATHINFO_EXTENSION));
                                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                    return '<a href="' . $url . '" target="_blank"><img src="' . $url . '" class="max-h-40 rounded border" /></a>';
                                }
                                return '<a href="' . $url . '" target="_blank" class="inline-flex items-center gap-1 text-primary-600 hover:underline font-medium"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg> View Document</a>';
                            })
                            ->html(),

                        TextEntry::make('additional_documents')
                            ->label(__('hall-owner.infolist.additional_documents'))
                            ->formatStateUsing(function ($state) {
                                if (empty($state)) {
                                    return '<span class="text-gray-400 italic">' . __('hall-owner.infolist.not_submitted') . '</span>';
                                }
                                $files = is_array($state) ? $state : json_decode($state, true) ?? [];
                                if (empty($files)) {
                                    return '<span class="text-gray-400 italic">' . __('hall-owner.infolist.not_submitted') . '</span>';
                                }
                                $html = '<div class="flex flex-wrap gap-3">';
                                foreach ($files as $index => $file) {
                                    $url = asset('storage/' . $file);
                                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                        $html .= '<a href="' . $url . '" target="_blank"><img src="' . $url . '" class="max-h-40 rounded border" /></a>';
                                    } else {
                                        $html .= '<a href="' . $url . '" target="_blank" class="inline-flex items-center gap-1 text-primary-600 hover:underline font-medium"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg> Document ' . ($index + 1) . '</a>';
                                    }
                                }
                                $html .= '</div>';
                                return $html;
                            })
                            ->html()
                            ->columnSpanFull(),
                    ])->columns(3),

                Section::make(__('hall-owner.infolist.statistics'))
                    ->schema([
                        TextEntry::make('total_halls')
                            ->label(__('hall-owner.infolist.total_halls'))
                            ->state(fn($record) => $record->getTotalHalls()),
                        TextEntry::make('active_halls')
                            ->label(__('hall-owner.infolist.active_halls'))
                            ->state(fn($record) => $record->getActiveHalls()),
                        TextEntry::make('total_bookings')
                            ->label(__('hall-owner.infolist.total_bookings'))
                            ->state(fn($record) => $record->getTotalBookings()),
                        TextEntry::make('total_revenue')
                            ->label(__('hall-owner.infolist.total_revenue'))
                            ->money('OMR')
                            ->state(fn($record) => $record->getTotalRevenue()),
                    ])->columns(4),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHallOwners::route('/'),
            'create' => CreateHallOwner::route('/create'),
            'view' => ViewHallOwner::route('/{record}'),
            'edit' => EditHallOwner::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_verified', false)->count();
    }
}
