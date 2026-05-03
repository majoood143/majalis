<?php

namespace App\Filament\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Admin\Resources\GuestSessionResource\Pages\ListGuestSessions;
use App\Filament\Admin\Resources\GuestSessionResource\Pages\ViewGuestSession;
use App\Filament\Admin\Resources\GuestSessionResource\Pages;
use App\Models\GuestSession;
use Filament\Forms;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Illuminate\Database\Eloquent\Builder;

class GuestSessionResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = GuestSession::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?int $navigationSort = 10;

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin.security_navigation_group');
    }

    public static function getModelLabel(): string
    {
        return __('guest-session.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('guest-session.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('guest-session.navigation_label');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('guest-session.guest_information'))
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('guest-session.name')),

                        TextEntry::make('email')
                            ->label(__('guest-session.email'))
                            ->copyable(),

                        TextEntry::make('phone')
                            ->label(__('guest-session.phone'))
                            ->placeholder('-'),

                        TextEntry::make('session_token')
                            ->label(__('guest-session.session_token'))
                            ->copyable()
                            ->fontFamily('mono')
                            ->columnSpanFull(),
                    ])->columns(3),

                Section::make(__('guest-session.session_status'))
                    ->schema([
                        TextEntry::make('status')
                            ->label(__('guest-session.status'))
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'pending'   => 'warning',
                                'verified'  => 'info',
                                'booking'   => 'primary',
                                'payment'   => 'primary',
                                'completed' => 'success',
                                'expired'   => 'gray',
                                'cancelled' => 'danger',
                                default     => 'gray',
                            })
                            ->formatStateUsing(fn(GuestSession $record): string => $record->status_label),

                        IconEntry::make('is_verified')
                            ->label(__('guest-session.is_verified'))
                            ->boolean(),

                        TextEntry::make('otp_attempts')
                            ->label(__('guest-session.otp_attempts')),

                        TextEntry::make('verified_at')
                            ->label(__('guest-session.verified_at'))
                            ->dateTime()
                            ->placeholder('-'),

                        TextEntry::make('expires_at')
                            ->label(__('guest-session.expires_at'))
                            ->dateTime(),

                        TextEntry::make('otp_expires_at')
                            ->label(__('guest-session.otp_expires_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ])->columns(3),

                Section::make(__('guest-session.booking_information'))
                    ->schema([
                        TextEntry::make('hall.name')
                            ->label(__('guest-session.hall'))
                            ->placeholder('-'),

                        TextEntry::make('booking.booking_number')
                            ->label(__('guest-session.booking'))
                            ->placeholder('-'),

                        TextEntry::make('booking_data')
                            ->label(__('guest-session.booking_data'))
                            ->formatStateUsing(fn($state) => $state ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '-')
                            ->fontFamily('mono')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make(__('guest-session.security_information'))
                    ->schema([
                        TextEntry::make('ip_address')
                            ->label(__('guest-session.ip_address'))
                            ->placeholder('-')
                            ->copyable(),

                        TextEntry::make('user_agent')
                            ->label(__('guest-session.user_agent'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make(__('guest-session.timestamps'))
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('guest-session.created_at'))
                            ->dateTime(),

                        TextEntry::make('updated_at')
                            ->label(__('guest-session.updated_at'))
                            ->dateTime(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('name')
                    ->label(__('guest-session.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label(__('guest-session.email'))
                    ->searchable()
                    ->copyable(),

                TextColumn::make('phone')
                    ->label(__('guest-session.phone'))
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('status')
                    ->label(__('guest-session.status'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending'   => 'warning',
                        'verified'  => 'info',
                        'booking'   => 'primary',
                        'payment'   => 'primary',
                        'completed' => 'success',
                        'expired'   => 'gray',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn(GuestSession $record): string => $record->status_label)
                    ->sortable(),

                IconColumn::make('is_verified')
                    ->label(__('guest-session.is_verified'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('otp_attempts')
                    ->label(__('guest-session.otp_attempts'))
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('hall.name')
                    ->label(__('guest-session.hall'))
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('ip_address')
                    ->label(__('guest-session.ip_address'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('expires_at')
                    ->label(__('guest-session.expires_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label(__('guest-session.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('guest-session.status'))
                    ->options([
                        'pending'   => __('guest-session.status_pending'),
                        'verified'  => __('guest-session.status_verified'),
                        'booking'   => __('guest-session.status_booking'),
                        'payment'   => __('guest-session.status_payment'),
                        'completed' => __('guest-session.status_completed'),
                        'expired'   => __('guest-session.status_expired'),
                        'cancelled' => __('guest-session.status_cancelled'),
                    ])
                    ->multiple(),

                TernaryFilter::make('is_verified')
                    ->label(__('guest-session.is_verified'))
                    ->boolean()
                    ->trueLabel(__('guest-session.verified_only'))
                    ->falseLabel(__('guest-session.unverified_only'))
                    ->native(false),

                Filter::make('expired')
                    ->label(__('guest-session.filter_expired'))
                    ->query(fn(Builder $query) => $query->where('expires_at', '<=', now())),

                Filter::make('active')
                    ->label(__('guest-session.filter_active'))
                    ->query(fn(Builder $query) => $query->where('expires_at', '>', now())
                        ->whereNotIn('status', ['expired', 'cancelled', 'completed'])),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    DeleteAction::make()
                        ->label(__('guest-session.hard_delete'))
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading(__('guest-session.hard_delete_heading'))
                        ->modalDescription(__('guest-session.hard_delete_description'))
                        ->modalSubmitActionLabel(__('guest-session.hard_delete_confirm')),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('guest-session.hard_delete_bulk'))
                        ->requiresConfirmation()
                        ->modalHeading(__('guest-session.hard_delete_bulk_heading'))
                        ->modalDescription(__('guest-session.hard_delete_bulk_description'))
                        ->modalSubmitActionLabel(__('guest-session.hard_delete_confirm')),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGuestSessions::route('/'),
            'view'  => ViewGuestSession::route('/{record}'),
        ];
    }
}
