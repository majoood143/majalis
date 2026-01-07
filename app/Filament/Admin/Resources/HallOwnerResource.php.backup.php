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

class HallOwnerResource extends Resource
{
    protected static ?string $model = HallOwner::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Hall Owner Information')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Business Info')
                            ->icon('heroicon-o-building-office')
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\TextInput::make('business_name')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('business_name_ar')
                                    ->label('Business Name (Arabic)')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('commercial_registration')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('tax_number')
                                    ->maxLength(255),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Contact')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Forms\Components\TextInput::make('business_phone')
                                    ->tel()
                                    ->required()
                                    ->maxLength(20),

                                Forms\Components\TextInput::make('business_email')
                                    ->email()
                                    ->maxLength(255),

                                Forms\Components\Textarea::make('business_address')
                                    ->required()
                                    ->rows(3)
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('business_address_ar')
                                    ->label('Business Address (Arabic)')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Bank Details')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Forms\Components\TextInput::make('bank_name')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('bank_account_name')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('bank_account_number')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('iban')
                                    ->maxLength(255),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Documents')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\FileUpload::make('commercial_registration_document')
                                    ->directory('documents/cr')
                                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                                    ->columnSpanFull(),

                                Forms\Components\FileUpload::make('tax_certificate')
                                    ->directory('documents/tax')
                                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                                    ->columnSpanFull(),

                                Forms\Components\FileUpload::make('identity_document')
                                    ->directory('documents/identity')
                                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Verification')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Forms\Components\Toggle::make('is_verified')
                                    ->inline(false),

                                Forms\Components\Toggle::make('is_active')
                                    ->inline(false),

                                Forms\Components\Textarea::make('verification_notes')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Commission')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Forms\Components\Select::make('commission_type')
                                    ->options([
                                        'percentage' => 'Percentage',
                                        'fixed' => 'Fixed Amount',
                                    ]),

                                Forms\Components\TextInput::make('commission_value')
                                    ->numeric()
                                    ->step(0.01),

                                Forms\Components\Placeholder::make('note')
                                    ->content('Leave empty to use global commission settings')
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
                    ->label('Owner Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('business_name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('commercial_registration')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('business_phone')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\IconColumn::make('is_verified')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Verified')
                    ->boolean()
                    ->native(false),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->native(false),
            ])
            ->actions([
                ActionGroup::make([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('verify')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label('Verification Notes'),
                    ])
                    ->action(function (HallOwner $record, array $data) {
                        $record->verify(Auth::id(), $data['notes'] ?? null);
                    })
                    ->visible(fn(HallOwner $record) => !$record->is_verified),

                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label('Rejection Reason')
                            ->required(),
                    ])
                    ->action(fn(HallOwner $record, array $data) => $record->reject($data['notes']))
                    ->visible(fn(HallOwner $record) => $record->is_verified),

                Tables\Actions\DeleteAction::make(),
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
                Infolists\Components\Section::make('Business Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Owner'),
                        Infolists\Components\TextEntry::make('business_name'),
                        Infolists\Components\TextEntry::make('business_name_ar')
                            ->label('Business Name (Arabic)'),
                        Infolists\Components\TextEntry::make('commercial_registration')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('tax_number')
                            ->copyable(),
                    ])->columns(3),

                Infolists\Components\Section::make('Contact Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('business_phone')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('business_email')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('business_address'),
                    ])->columns(2),

                Infolists\Components\Section::make('Bank Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('bank_name'),
                        Infolists\Components\TextEntry::make('bank_account_name'),
                        Infolists\Components\TextEntry::make('bank_account_number')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('iban')
                            ->copyable(),
                    ])->columns(2),

                Infolists\Components\Section::make('Verification Status')
                    ->schema([
                        Infolists\Components\TextEntry::make('is_verified')
                            ->badge()
                            ->color(fn($state) => $state ? 'success' : 'warning')
                            ->formatStateUsing(fn($state) => $state ? 'Verified' : 'Pending'),
                        Infolists\Components\TextEntry::make('verified_at')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('verifiedBy.name')
                            ->label('Verified By'),
                        Infolists\Components\TextEntry::make('verification_notes')
                            ->columnSpanFull(),
                    ])->columns(3),

                Infolists\Components\Section::make('Statistics')
                    ->schema([
                        Infolists\Components\TextEntry::make('total_halls')
                            ->label('Total Halls')
                            ->state(fn($record) => $record->getTotalHalls()),
                        Infolists\Components\TextEntry::make('active_halls')
                            ->label('Active Halls')
                            ->state(fn($record) => $record->getActiveHalls()),
                        Infolists\Components\TextEntry::make('total_bookings')
                            ->label('Total Bookings')
                            ->state(fn($record) => $record->getTotalBookings()),
                        Infolists\Components\TextEntry::make('total_revenue')
                            ->label('Total Revenue')
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
