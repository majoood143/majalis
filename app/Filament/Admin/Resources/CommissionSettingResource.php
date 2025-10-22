<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CommissionSettingResource\Pages;
use App\Models\CommissionSetting;
use App\Models\Hall;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CommissionSettingResource extends Resource
{
    protected static ?string $model = CommissionSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationGroup = 'Financial';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Commission Scope')
                    ->description('Select either a specific hall, owner, or leave both empty for global settings')
                    ->schema([
                        Forms\Components\Select::make('hall_id')
                            ->label('Hall (Optional)')
                            ->options(Hall::all()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->helperText('Leave empty for owner-level or global commission'),

                        Forms\Components\Select::make('owner_id')
                            ->label('Owner (Optional)')
                            ->options(User::where('role', 'hall_owner')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->helperText('Leave empty for global commission'),

                        Forms\Components\Placeholder::make('scope_note')
                            ->content('Priority: Hall-specific > Owner-specific > Global')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Commission Details')
                    ->schema([
                        Forms\Components\TextInput::make('name.en')
                            ->label('Name (English)')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('name.ar')
                            ->label('Name (Arabic)')
                            ->maxLength(255),

                        Forms\Components\Select::make('commission_type')
                            ->options([
                                'percentage' => 'Percentage',
                                'fixed' => 'Fixed Amount',
                            ])
                            ->required()
                            ->reactive(),

                        Forms\Components\TextInput::make('commission_value')
                            ->numeric()
                            ->required()
                            ->step(0.01)
                            ->suffix(fn($get) => $get('commission_type') === 'percentage' ? '%' : 'OMR'),

                        Forms\Components\Textarea::make('description.en')
                            ->label('Description (English)')
                            ->rows(3),

                        Forms\Components\Textarea::make('description.ar')
                            ->label('Description (Arabic)')
                            ->rows(3),
                    ])->columns(2),

                Forms\Components\Section::make('Validity Period')
                    ->schema([
                        Forms\Components\DatePicker::make('effective_from')
                            ->native(false)
                            ->helperText('Leave empty for immediate effect'),

                        Forms\Components\DatePicker::make('effective_to')
                            ->native(false)
                            ->helperText('Leave empty for indefinite period'),

                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->inline(false),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('scope')
                    ->label('Scope')
                    ->badge()
                    ->color(fn($record): string => match (true) {
                        $record->isHallSpecific() => 'success',
                        $record->isOwnerSpecific() => 'warning',
                        default => 'primary',
                    })
                    ->formatStateUsing(function ($record) {
                        if ($record->isHallSpecific()) {
                            return 'Hall: ' . $record->hall->name;
                        } elseif ($record->isOwnerSpecific()) {
                            return 'Owner: ' . $record->owner->name;
                        }
                        return 'Global';
                    }),

                Tables\Columns\TextColumn::make('commission_type')
                    ->badge()
                    ->formatStateUsing(fn($state) => ucfirst($state)),

                Tables\Columns\TextColumn::make('commission_value')
                    ->label('Value')
                    ->formatStateUsing(function ($record) {
                        return $record->commission_type->value === 'percentage'
                            ? $record->commission_value . '%'
                            : number_format($record->commission_value, 3) . ' OMR';
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('effective_from')
                    ->date()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('effective_to')
                    ->date()
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Indefinite'),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('commission_type')
                    ->options([
                        'percentage' => 'Percentage',
                        'fixed' => 'Fixed Amount',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->native(false),

                Tables\Filters\Filter::make('scope')
                    ->form([
                        Forms\Components\Select::make('scope_type')
                            ->options([
                                'global' => 'Global',
                                'owner' => 'Owner-Specific',
                                'hall' => 'Hall-Specific',
                            ]),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['scope_type'] === 'global', fn($q) => $q->whereNull('hall_id')->whereNull('owner_id'))
                            ->when($data['scope_type'] === 'owner', fn($q) => $q->whereNotNull('owner_id')->whereNull('hall_id'))
                            ->when($data['scope_type'] === 'hall', fn($q) => $q->whereNotNull('hall_id'));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListCommissionSettings::route('/'),
            'create' => Pages\CreateCommissionSetting::route('/create'),
            'edit' => Pages\EditCommissionSetting::route('/{record}/edit'),
        ];
    }
}
