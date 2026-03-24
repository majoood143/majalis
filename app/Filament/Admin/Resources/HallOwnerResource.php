<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\HallOwnerResource\Pages;
use App\Models\HallOwner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Filament\Tables\Actions\ActionGroup;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;

class HallOwnerResource extends Resource
{
    protected static ?string $model = HallOwner::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'User Management';

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make(__('hall-owner.tabs.business_info'))
                    ->tabs([
                        Forms\Components\Tabs\Tab::make(__('hall-owner.tabs.business_info'))
                            ->icon('heroicon-o-building-office')
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->label(__('hall-owner.fields.user_id'))
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\TextInput::make('business_name')
                                    ->label(__('hall-owner.fields.business_name'))
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('business_name_ar')
                                    ->label(__('hall-owner.fields.business_name_ar'))
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('commercial_registration')
                                    ->label(__('hall-owner.fields.commercial_registration'))
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('tax_number')
                                    ->label(__('hall-owner.fields.tax_number'))
                                    ->maxLength(255),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make(__('hall-owner.tabs.contact'))
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Forms\Components\TextInput::make('business_phone')
                                    ->label(__('hall-owner.fields.business_phone'))
                                    ->numeric()
                                    ->required()
                                    ->maxLength(20),

                                Forms\Components\TextInput::make('business_email')
                                    ->label(__('hall-owner.fields.business_email'))
                                    ->email()
                                    ->maxLength(255),

                                Forms\Components\Textarea::make('business_address')
                                    ->label(__('hall-owner.fields.business_address'))
                                    ->required()
                                    ->rows(3)
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('business_address_ar')
                                    ->label(__('hall-owner.fields.business_address_ar'))
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make(__('hall-owner.tabs.bank_details'))
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Forms\Components\TextInput::make('bank_name')
                                    ->label(__('hall-owner.fields.bank_name'))
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('bank_account_name')
                                    ->label(__('hall-owner.fields.bank_account_name'))
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('bank_account_number')
                                    ->label(__('hall-owner.fields.bank_account_number'))
                                    ->maxLength(17)
                                    ->rules([
                                        'required',

                                        'regex:/^\d+$/', // ensures only digits
                                    ])
                                    ->numeric(),





                                Forms\Components\TextInput::make('iban')
                                    ->label(__('hall-owner.fields.iban'))
                                    ->maxLength(255),

                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make(__('hall-owner.tabs.documents'))
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\FileUpload::make('commercial_registration_document')
                                    ->disk('public')
                                    ->directory('documents/cr')
                                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                                    ->downloadable()
                                    ->columnSpanFull(),

                                Forms\Components\FileUpload::make('tax_certificate')
                                    ->disk('public')
                                    ->directory('documents/tax')
                                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                                    ->downloadable()
                                    ->columnSpanFull(),

                                Forms\Components\FileUpload::make('identity_document')
                                    ->disk('public')
                                    ->directory('documents/identity')
                                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                                    ->downloadable()
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make(__('hall-owner.tabs.verification'))
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Forms\Components\Toggle::make('is_verified')
                                    ->label(__('hall-owner.fields.is_verified'))
                                    ->inline(false),

                                Forms\Components\Toggle::make('is_active')
                                    ->label(__('hall-owner.fields.is_active'))
                                    ->inline(false),

                                Forms\Components\Textarea::make('verification_notes')
                                    ->label(__('hall-owner.fields.verification_notes'))
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make(__('hall-owner.tabs.commission'))
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Forms\Components\Select::make('commission_type')
                                    ->label(__('hall-owner.fields.commission_type'))
                                    ->options([
                                        'percentage' => __('hall-owner.options.percentage'),
                                        'fixed' => __('hall-owner.options.fixed'),
                                    ]),

                                Forms\Components\TextInput::make('commission_value')
                                    ->label(__('hall-owner.fields.commission_value'))
                                    ->numeric()
                                    ->step(0.01),

                                Forms\Components\Placeholder::make('note')
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
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('hall-owner.columns.owner_name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('business_name')
                    ->label(__('hall-owner.columns.business_name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('commercial_registration')
                    ->label(__('hall-owner.columns.commercial_registration'))
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('business_phone')
                    ->label(__('hall-owner.columns.business_phone'))
                    ->searchable()
                    ->copyable(),

                Tables\Columns\IconColumn::make('is_verified')
                    ->label(__('hall-owner.columns.is_verified'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('hall-owner.columns.is_active'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('hall-owner.columns.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label(__('hall-owner.filters.verified'))
                    ->boolean()
                    ->native(false),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('hall-owner.filters.active'))
                    ->boolean()
                    ->native(false),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),

                    Tables\Actions\Action::make('verify')
                        ->label(__('hall-owner.actions.verify'))
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Textarea::make('notes')
                                ->label(__('hall-owner.fields.notes')),
                        ])
                        ->action(function (HallOwner $record, array $data) {
                            $record->verify(Auth::id(), $data['notes'] ?? null);
                        })
                        ->visible(fn(HallOwner $record) => !$record->is_verified),

                    Tables\Actions\Action::make('reject')
                        ->label(__('hall-owner.actions.reject'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Textarea::make('notes')
                                ->label(__('hall-owner.fields.rejection_reason'))
                                ->required(),
                        ])
                        ->action(fn(HallOwner $record, array $data) => $record->reject($data['notes']))
                        ->visible(fn(HallOwner $record) => $record->is_verified),

                    Tables\Actions\DeleteAction::make(),
                ActivityLogTimelineTableAction::make('Activities'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('hall-owner.infolist.business_information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label(__('hall-owner.infolist.owner')),
                        Infolists\Components\TextEntry::make('business_name')
                            ->label(__('hall-owner.infolist.business_name')),

                        Infolists\Components\TextEntry::make('business_name_ar')
                            ->label(__('hall-owner.infolist.business_name_ar')),
                        Infolists\Components\TextEntry::make('commercial_registration')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('tax_number')
                            ->copyable(),
                    ])->columns(3),

                Infolists\Components\Section::make(__('hall-owner.infolist.contact_information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('business_phone')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('business_email')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('business_address'),
                    ])->columns(2),

                Infolists\Components\Section::make(__('hall-owner.infolist.bank_details'))
                    ->schema([
                        Infolists\Components\TextEntry::make('bank_name'),
                        Infolists\Components\TextEntry::make('bank_account_name'),
                        Infolists\Components\TextEntry::make('bank_account_number')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('iban')
                            ->copyable(),
                    ])->columns(2),

                Infolists\Components\Section::make(__('hall-owner.infolist.verification_status'))
                    ->schema([
                        Infolists\Components\TextEntry::make('is_verified')
                            ->badge()
                            ->color(fn($state) => $state ? 'success' : 'warning')
                            ->formatStateUsing(fn($state) => $state
                                ? __('hall-owner.infolist.verified')
                                : __('hall-owner.infolist.pending')),
                        Infolists\Components\TextEntry::make('verified_at')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('verifiedBy.name')
                            ->label(__('hall-owner.infolist.verified_by')),
                        Infolists\Components\TextEntry::make('verification_notes')
                            ->columnSpanFull(),
                    ])->columns(3),

                Infolists\Components\Section::make(__('hall-owner.infolist.statistics'))
                    ->schema([
                        Infolists\Components\TextEntry::make('total_halls')
                            ->label(__('hall-owner.infolist.total_halls'))
                            ->state(fn($record) => $record->getTotalHalls()),
                        Infolists\Components\TextEntry::make('active_halls')
                            ->label(__('hall-owner.infolist.active_halls'))
                            ->state(fn($record) => $record->getActiveHalls()),
                        Infolists\Components\TextEntry::make('total_bookings')
                            ->label(__('hall-owner.infolist.total_bookings'))
                            ->state(fn($record) => $record->getTotalBookings()),
                        Infolists\Components\TextEntry::make('total_revenue')
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
            'index' => Pages\ListHallOwners::route('/'),
            'create' => Pages\CreateHallOwner::route('/create'),
            'view' => Pages\ViewHallOwner::route('/{record}'),
            'edit' => Pages\EditHallOwner::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_verified', false)->count();
    }
}
