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

class HallImageResource extends Resource
{
    protected static ?string $model = HallImage::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Hall Management';

    protected static ?int $navigationSort = 3;

    protected static ?string $label = 'Hall Image';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Image Information')
                    ->schema([
                        Forms\Components\Select::make('hall_id')
                            ->label('Hall')
                            ->options(Hall::all()->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('type')
                            ->options([
                                'gallery' => 'Gallery Image',
                                'featured' => 'Featured Image',
                                'floor_plan' => 'Floor Plan',
                                '360_view' => '360° View',
                            ])
                            ->default('gallery')
                            ->required(),

                        Forms\Components\FileUpload::make('image_path')
                            ->label('Image')
                            ->image()
                            ->directory('halls/images')
                            ->required()
                            ->columnSpanFull()
                            ->imageEditor(),

                        Forms\Components\FileUpload::make('thumbnail_path')
                            ->label('Thumbnail (Optional)')
                            ->image()
                            ->directory('halls/thumbnails')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Image Details')
                    ->schema([
                        Forms\Components\TextInput::make('title.en')
                            ->label('Title (English)')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('title.ar')
                            ->label('Title (Arabic)')
                            ->maxLength(255),

                        Forms\Components\Textarea::make('caption.en')
                            ->label('Caption (English)')
                            ->rows(2),

                        Forms\Components\Textarea::make('caption.ar')
                            ->label('Caption (Arabic)')
                            ->rows(2),

                        Forms\Components\TextInput::make('alt_text')
                            ->label('Alt Text (for SEO)')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\TextInput::make('order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->inline(false),

                        Forms\Components\Toggle::make('is_featured')
                            ->inline(false),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Image')
                    ->size(80),

                Tables\Columns\TextColumn::make('hall.name')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->hall->name),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn($record) => $record->type_label)
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(30)
                    ->formatStateUsing(fn($record) => $record->title ?: '-'),

                Tables\Columns\TextColumn::make('formatted_size')
                    ->label('Size')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('dimensions')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('order')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('hall_id')
                    ->relationship('hall', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'gallery' => 'Gallery Image',
                        'featured' => 'Featured Image',
                        'floor_plan' => 'Floor Plan',
                        '360_view' => '360° View',
                    ]),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->native(false),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->native(false),
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
