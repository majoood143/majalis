<?php

namespace App\Filament\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Admin\Resources\UserResource\Pages\ListUsers;
use App\Filament\Admin\Resources\UserResource\Pages\CreateUser;
use App\Filament\Admin\Resources\UserResource\Pages\EditUser;
use App\Filament\Admin\Resources\UserResource\Pages\ViewUser;
use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Actions\ActionGroup as ActionsActionGroup;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static string | \UnitEnum | null $navigationGroup = 'User Management';

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('user.form.sections.user_information'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('user.form.name'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label(__('user.form.email'))
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('password')
                            ->label(__('user.form.password'))
                            ->password()
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $context): bool => $context === 'create')
                            ->maxLength(255)
                            ->helperText(__('user.form.password_helper')),

                        Select::make('role')
                            ->label(__('user.form.role'))
                            ->options([
                                'admin' => __('user.roles.admin'),
                                'hall_owner' => __('user.roles.hall_owner'),
                                'customer' => __('user.roles.customer'),
                            ])
                            ->required(),
                    ])->columns(2),

                Section::make(__('user.form.sections.contact_information'))
                    ->schema([
                        TextInput::make('phone')
                            ->label(__('user.form.phone'))
                            ->tel()
                            ->maxLength(20),

                        TextInput::make('phone_country_code')
                            ->label(__('user.form.phone_country_code'))
                            ->default('+968')
                            ->maxLength(5),

                        Toggle::make('is_active')
                            ->label(__('user.form.is_active'))
                            ->default(true)
                            ->inline(false),
                    ])->columns(3),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('user.table.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label(__('user.table.email'))
                    ->searchable()
                    ->copyable()
                    ->sortable(),

                TextColumn::make('role')
                    ->label(__('user.table.role'))
                    ->badge()
                    ->sortable(),
                    //->formatStateUsing(fn($state) => __('user.roles.' . $state)),

                TextColumn::make('phone')
                    ->label(__('user.table.phone'))
                    ->searchable(),

                IconColumn::make('email_verified_at')
                    ->label(__('user.table.verified'))
                    ->boolean()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label(__('user.table.active'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('user.table.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label(__('user.filters.role'))
                    ->options([
                        'admin' => __('user.roles.admin'),
                        'hall_owner' => __('user.roles.hall_owner'),
                        'customer' => __('user.roles.customer'),
                    ]),

                TernaryFilter::make('is_active')
                    ->label(__('user.filters.active'))
                    ->boolean()
                    ->trueLabel(__('user.filters.active_true'))
                    ->falseLabel(__('user.filters.active_false'))
                    ->native(false),

                TernaryFilter::make('email_verified_at')
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
            ->recordActions([
                ActionsActionGroup::make([
                    EditAction::make()
                        ->label(__('user.actions.edit')),
                    DeleteAction::make()
                        ->label(__('user.actions.delete')),
                // TODO: ActivityLogTimelineTableAction removed (rmsramos v3-only) - replace with v4 equivalent,

                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
            'view' => ViewUser::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }
}
