<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\HallImageResource\Pages;
use App\Models\HallImage;
use App\Models\Hall;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\ActionGroup;

class HallImageResource extends Resource
{
    protected static ?string $model = HallImage::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    //protected static ?string $navigationGroup = 'Hall Management';

    public static function getNavigationGroup(): ?string
    {
        return __('admin.hall_navigation_group');
    }

    protected static ?int $navigationSort = 3;

    protected static ?string $label = 'Hall Image';

    public static function getModelLabel(): string
    {
        return __('hall-image.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('hall-image.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('hall-image.navigation_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\Section::make(__('hall-image.image_information'))
                ->schema([
                    Forms\Components\Select::make('hall_id')
                        ->label(__('hall-image.hall'))
                        ->options(Hall::all()->pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->preload(),

                    Forms\Components\Select::make('type')
                        ->label(__('hall-image.type'))
                        ->options([
                            'gallery' => __('hall-image.types.gallery'),
                            'featured' => __('hall-image.types.featured'),
                            'floor_plan' => __('hall-image.types.floor_plan'),
                            '360_view' => __('hall-image.types.360_view'),
                        ])
                        ->default('gallery')
                        ->required(),

                    // FIX: Added ->disk('public') to load existing images correctly
                    Forms\Components\FileUpload::make('image_path')
                        ->label(__('hall-image.image_path'))
                        ->image()
                        ->disk('public')                    // ← REQUIRED for loading existing images
                        ->directory('halls/images')
                        ->visibility('public')              // ← Ensures files are publicly accessible
                        ->required()
                        ->columnSpanFull()
                        ->imageEditor()
                        ->imagePreviewHeight('250')         // ← Better preview in edit form
                        ->loadingIndicatorPosition('left')
                        ->removeUploadedFileButtonPosition('right')
                        ->uploadProgressIndicatorPosition('left'),

                    // FIX: Added ->disk('public') to load existing thumbnails correctly
                    Forms\Components\FileUpload::make('thumbnail_path')
                        ->label(__('hall-image.thumbnail_path'))
                        ->image()
                        ->disk('public')                    // ← REQUIRED for loading existing images
                        ->directory('halls/thumbnails')
                        ->visibility('public')              // ← Ensures files are publicly accessible
                        ->columnSpanFull()
                        ->imagePreviewHeight('150'),        // ← Smaller preview for thumbnails
                ])->columns(2),

                Forms\Components\Section::make(__('hall-image.image_details'))
                    ->schema([
                        Forms\Components\TextInput::make('title.en')
                            ->label(__('hall-image.title_en'))
                            ->maxLength(255),

                        Forms\Components\TextInput::make('title.ar')
                            ->label(__('hall-image.title_ar'))
                            ->maxLength(255),

                        Forms\Components\Textarea::make('caption.en')
                            ->label(__('hall-image.caption_en'))
                            ->rows(2),

                        Forms\Components\Textarea::make('caption.ar')
                            ->label(__('hall-image.caption_ar'))
                            ->rows(2),

                        Forms\Components\TextInput::make('alt_text')
                            ->label(__('hall-image.alt_text'))
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make(__('hall-image.settings'))
                    ->schema([
                        Forms\Components\TextInput::make('order')
                            ->label(__('hall-image.order'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Forms\Components\Toggle::make('is_active')
                            ->label(__('hall-image.is_active'))
                            ->default(true)
                            ->inline(false),

                        Forms\Components\Toggle::make('is_featured')
                            ->label(__('hall-image.is_featured'))
                            ->inline(false),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label(__('hall-image.image'))
                    ->size(80),

                Tables\Columns\TextColumn::make('hall.name')
                    ->label(__('hall-image.hall_name'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->hall->name),

                Tables\Columns\TextColumn::make('type')
                    ->label(__('hall-image.type'))
                    ->badge()
                    ->formatStateUsing(function ($record) {
                        $typeKey = $record->type;
                        $translations = __('hall-image.types');
                        return $translations[$typeKey] ?? $typeKey;
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label(__('hall-image.title'))
                    ->searchable()
                    ->limit(30)
                    ->formatStateUsing(fn($record) => $record->title ?: __('hall-image.no_title')),

                Tables\Columns\TextColumn::make('formatted_size')
                    ->label(__('hall-image.size'))
                    ->toggleable(),

                Tables\Columns\TextColumn::make('dimensions')
                    ->label(__('hall-image.dimensions'))
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label(__('hall-image.featured'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('hall-image.active'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('order')
                    ->label(__('hall-image.order'))
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('hall_id')
                    ->label(__('hall-image.filters.hall'))
                    ->relationship('hall', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('type')
                    ->label(__('hall-image.filters.type'))
                    ->options([
                        'gallery' => __('hall-image.types.gallery'),
                        'featured' => __('hall-image.types.featured'),
                        'floor_plan' => __('hall-image.types.floor_plan'),
                        '360_view' => __('hall-image.types.360_view'),
                    ]),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label(__('hall-image.filters.featured'))
                    ->boolean()
                    ->native(false),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('hall-image.filters.active'))
                    ->boolean()
                    ->native(false),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label(__('hall-image.edit')),
                    Tables\Actions\DeleteAction::make()
                        ->label(__('hall-image.delete')),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order')
            ->reorderable('order');
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
            'index' => Pages\ListHallImages::route('/'),
            'create' => Pages\CreateHallImage::route('/create'),
            'edit' => Pages\EditHallImage::route('/{record}/edit'),
        ];
    }
}
