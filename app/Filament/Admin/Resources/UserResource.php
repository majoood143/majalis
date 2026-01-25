<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Actions\ActionGroup as ActionsActionGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Filament\Tables\Actions\ActionGroup;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return __('user.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('user.resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('user.resource.navigation_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('user.form.sections.user_information'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('user.form.name'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label(__('user.form.email'))
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('password')
                            ->label(__('user.form.password'))
                            ->password()
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $context): bool => $context === 'create')
                            ->maxLength(255)
                            ->helperText(__('user.form.password_helper')),

                        Forms\Components\Select::make('role')
                            ->label(__('user.form.role'))
                            ->options([
                                'admin' => __('user.roles.admin'),
                                'hall_owner' => __('user.roles.hall_owner'),
                                'customer' => __('user.roles.customer'),
                            ])
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make(__('user.form.sections.contact_information'))
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->label(__('user.form.phone'))
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('phone_country_code')
                            ->label(__('user.form.phone_country_code'))
                            ->default('+968')
                            ->maxLength(5),

                        Forms\Components\Toggle::make('is_active')
                            ->label(__('user.form.is_active'))
                            ->default(true)
                            ->inline(false),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('user.table.name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label(__('user.table.email'))
                    ->searchable()
                    ->copyable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('role')
                    ->label(__('user.table.role'))
                    ->badge()
                    ->sortable(),
                    //->formatStateUsing(fn($state) => __('user.roles.' . $state)),

                Tables\Columns\TextColumn::make('phone')
                    ->label(__('user.table.phone'))
                    ->searchable(),

                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label(__('user.table.verified'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('user.table.active'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('user.table.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label(__('user.filters.role'))
                    ->options([
                        'admin' => __('user.roles.admin'),
                        'hall_owner' => __('user.roles.hall_owner'),
                        'customer' => __('user.roles.customer'),
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('user.filters.active'))
                    ->boolean()
                    ->trueLabel(__('user.filters.active_true'))
                    ->falseLabel(__('user.filters.active_false'))
                    ->native(false),

                Tables\Filters\TernaryFilter::make('email_verified_at')
                    ->label(__('user.filters.email_verified'))
                    ->boolean()
                    ->trueLabel(__('user.filters.verified_true'))
                    ->falseLabel(__('user.filters.verified_false'))
                    ->queries(
                        true: fn($query) => $query->whereNotNull('email_verified_at'),
                        false: fn($query) => $query->whereNull('email_verified_at'),
                    )
                    ->native(false),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label(__('user.actions.edit')),
                    Tables\Actions\DeleteAction::make()
                        ->label(__('user.actions.delete')),
                ActivityLogTimelineTableAction::make('Activities'),

                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('user.actions.delete_bulk')),
                ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }
}
