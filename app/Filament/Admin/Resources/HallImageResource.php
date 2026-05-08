<?php

namespace App\Filament\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Admin\Resources\HallImageResource\Pages\ListHallImages;
use App\Filament\Admin\Resources\HallImageResource\Pages\CreateHallImage;
use App\Filament\Admin\Resources\HallImageResource\Pages\EditHallImage;
use App\Filament\Admin\Resources\HallImageResource\Pages;
use App\Models\HallImage;
use App\Models\Hall;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class HallImageResource extends Resource
{
    protected static ?string $model = HallImage::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-photo';

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
            Section::make(__('hall-image.image_information'))
                ->schema([
                    Select::make('hall_id')
                        ->label(__('hall-image.hall'))
                        ->options(Hall::all()->pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->preload(),

                    Select::make('type')
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
                    FileUpload::make('image_path')
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
                    FileUpload::make('thumbnail_path')
                        ->label(__('hall-image.thumbnail_path'))
                        ->image()
                        ->disk('public')                    // ← REQUIRED for loading existing images
                        ->directory('halls/thumbnails')
                        ->visibility('public')              // ← Ensures files are publicly accessible
                        ->columnSpanFull()
                        ->imagePreviewHeight('150'),        // ← Smaller preview for thumbnails
                ])->columns(2),

                Section::make(__('hall-image.image_details'))
                    ->schema([
                        TextInput::make('title.en')
                            ->label(__('hall-image.title_en'))
                            ->maxLength(255),

                        TextInput::make('title.ar')
                            ->label(__('hall-image.title_ar'))
                            ->maxLength(255),

                        Textarea::make('caption.en')
                            ->label(__('hall-image.caption_en'))
                            ->rows(2),

                        Textarea::make('caption.ar')
                            ->label(__('hall-image.caption_ar'))
                            ->rows(2),

                        TextInput::make('alt_text')
                            ->label(__('hall-image.alt_text'))
                            ->maxLength(255),
                    ])->columns(2),

                Section::make(__('hall-image.settings'))
                    ->schema([
                        TextInput::make('order')
                            ->label(__('hall-image.order'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Toggle::make('is_active')
                            ->label(__('hall-image.is_active'))
                            ->default(true)
                            ->inline(false),

                        Toggle::make('is_featured')
                            ->label(__('hall-image.is_featured'))
                            ->inline(false),
                    ])->columns(3),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label(__('hall-image.image'))
                    ->size(80),

                TextColumn::make('hall.name')
                    ->label(__('hall-image.hall_name'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->hall->name),

                TextColumn::make('type')
                    ->label(__('hall-image.type'))
                    ->badge()
                    ->formatStateUsing(function ($record) {
                        $typeKey = $record->type;
                        $translations = __('hall-image.types');
                        return $translations[$typeKey] ?? $typeKey;
                    })
                    ->sortable(),

                TextColumn::make('title')
                    ->label(__('hall-image.title'))
                    ->searchable()
                    ->limit(30)
                    ->formatStateUsing(fn($record) => $record->title ?: __('hall-image.no_title')),

                TextColumn::make('formatted_size')
                    ->label(__('hall-image.size'))
                    ->toggleable(),

                TextColumn::make('dimensions')
                    ->label(__('hall-image.dimensions'))
                    ->toggleable(),

                IconColumn::make('is_featured')
                    ->label(__('hall-image.featured'))
                    ->boolean()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label(__('hall-image.active'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('order')
                    ->label(__('hall-image.order'))
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('hall_id')
                    ->label(__('hall-image.filters.hall'))
                    ->relationship('hall', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('type')
                    ->label(__('hall-image.filters.type'))
                    ->options([
                        'gallery' => __('hall-image.types.gallery'),
                        'featured' => __('hall-image.types.featured'),
                        'floor_plan' => __('hall-image.types.floor_plan'),
                        '360_view' => __('hall-image.types.360_view'),
                    ]),

                TernaryFilter::make('is_featured')
                    ->label(__('hall-image.filters.featured'))
                    ->boolean()
                    ->native(false),

                TernaryFilter::make('is_active')
                    ->label(__('hall-image.filters.active'))
                    ->boolean()
                    ->native(false),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->label(__('hall-image.edit')),
                    DeleteAction::make()
                        ->label(__('hall-image.delete')),
                // TODO: ActivityLogTimelineTableAction removed (rmsramos v3-only) - replace with v4 equivalent,
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListHallImages::route('/'),
            'create' => CreateHallImage::route('/create'),
            'edit' => EditHallImage::route('/{record}/edit'),
        ];
    }
}
