<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\GuestSessionResource\Pages;
use App\Models\GuestSession;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\ActionGroup;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Illuminate\Database\Eloquent\Builder;

class GuestSessionResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = GuestSession::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

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

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('guest-session.guest_information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label(__('guest-session.name')),

                        Infolists\Components\TextEntry::make('email')
                            ->label(__('guest-session.email'))
                            ->copyable(),

                        Infolists\Components\TextEntry::make('phone')
                            ->label(__('guest-session.phone'))
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('session_token')
                            ->label(__('guest-session.session_token'))
                            ->copyable()
                            ->fontFamily('mono')
                            ->columnSpanFull(),
                    ])->columns(3),

                Infolists\Components\Section::make(__('guest-session.session_status'))
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
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

                        Infolists\Components\IconEntry::make('is_verified')
                            ->label(__('guest-session.is_verified'))
                            ->boolean(),

                        Infolists\Components\TextEntry::make('otp_attempts')
                            ->label(__('guest-session.otp_attempts')),

                        Infolists\Components\TextEntry::make('verified_at')
                            ->label(__('guest-session.verified_at'))
                            ->dateTime()
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('expires_at')
                            ->label(__('guest-session.expires_at'))
                            ->dateTime(),

                        Infolists\Components\TextEntry::make('otp_expires_at')
                            ->label(__('guest-session.otp_expires_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ])->columns(3),

                Infolists\Components\Section::make(__('guest-session.booking_information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('hall.name')
                            ->label(__('guest-session.hall'))
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('booking.booking_number')
                            ->label(__('guest-session.booking'))
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('booking_data')
                            ->label(__('guest-session.booking_data'))
                            ->formatStateUsing(fn($state) => $state ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '-')
                            ->fontFamily('mono')
                            ->columnSpanFull(),
                    ])->columns(2),

                Infolists\Components\Section::make(__('guest-session.security_information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('ip_address')
                            ->label(__('guest-session.ip_address'))
                            ->placeholder('-')
                            ->copyable(),

                        Infolists\Components\TextEntry::make('user_agent')
                            ->label(__('guest-session.user_agent'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])->columns(2),

                Infolists\Components\Section::make(__('guest-session.timestamps'))
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label(__('guest-session.created_at'))
                            ->dateTime(),

                        Infolists\Components\TextEntry::make('updated_at')
                            ->label(__('guest-session.updated_at'))
                            ->dateTime(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('guest-session.name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label(__('guest-session.email'))
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label(__('guest-session.phone'))
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
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

                Tables\Columns\IconColumn::make('is_verified')
                    ->label(__('guest-session.is_verified'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('otp_attempts')
                    ->label(__('guest-session.otp_attempts'))
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('hall.name')
                    ->label(__('guest-session.hall'))
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label(__('guest-session.ip_address'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label(__('guest-session.expires_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('guest-session.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
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

                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label(__('guest-session.is_verified'))
                    ->boolean()
                    ->trueLabel(__('guest-session.verified_only'))
                    ->falseLabel(__('guest-session.unverified_only'))
                    ->native(false),

                Tables\Filters\Filter::make('expired')
                    ->label(__('guest-session.filter_expired'))
                    ->query(fn(Builder $query) => $query->where('expires_at', '<=', now())),

                Tables\Filters\Filter::make('active')
                    ->label(__('guest-session.filter_active'))
                    ->query(fn(Builder $query) => $query->where('expires_at', '>', now())
                        ->whereNotIn('status', ['expired', 'cancelled', 'completed'])),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->label(__('guest-session.hard_delete'))
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading(__('guest-session.hard_delete_heading'))
                        ->modalDescription(__('guest-session.hard_delete_description'))
                        ->modalSubmitActionLabel(__('guest-session.hard_delete_confirm')),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
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
            'index' => Pages\ListGuestSessions::route('/'),
            'view'  => Pages\ViewGuestSession::route('/{record}'),
        ];
    }
}
