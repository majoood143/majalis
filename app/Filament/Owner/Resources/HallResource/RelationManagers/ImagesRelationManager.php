<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\HallResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * ImagesRelationManager for Owner Panel
 *
 * Allows hall owners to manage gallery images for their halls.
 */
class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('owner.relation.images');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image_path')
                    ->label(__('owner.images.image'))
                    ->image()
                    ->imageEditor()
                    ->directory('halls/gallery')
                    ->visibility('public')
                    ->maxSize(5120)
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Select::make('type')
                    ->label(__('owner.images.type'))
                    ->options([
                        'featured' => __('owner.images.types.featured'),
                        'gallery' => __('owner.images.types.gallery'),
                        'floor_plan' => __('owner.images.types.floor_plan'),
                        'panorama' => __('owner.images.types.panorama'),
                        'exterior' => __('owner.images.types.exterior'),
                        'interior' => __('owner.images.types.interior'),
                    ])
                    ->default('gallery')
                    ->required(),

                Forms\Components\TextInput::make('alt_text.en')
                    ->label(__('owner.images.alt_en'))
                    ->maxLength(255)
                    ->helperText(__('owner.images.alt_help')),

                Forms\Components\TextInput::make('alt_text.ar')
                    ->label(__('owner.images.alt_ar'))
                    ->maxLength(255)
                    ->extraInputAttributes(['dir' => 'rtl']),

                Forms\Components\Textarea::make('caption.en')
                    ->label(__('owner.images.caption_en'))
                    ->rows(2),

                Forms\Components\Textarea::make('caption.ar')
                    ->label(__('owner.images.caption_ar'))
                    ->rows(2)
                    ->extraInputAttributes(['dir' => 'rtl']),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label(__('owner.images.preview'))
                    ->size(80)
                    ->square(),

                Tables\Columns\TextColumn::make('type')
                    ->label(__('owner.images.type'))
                    ->formatStateUsing(fn (string $state): string => __("owner.images.types.{$state}"))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'featured' => 'warning',
                        'floor_plan' => 'info',
                        'panorama' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('alt_text')
                    ->label(__('owner.images.alt'))
                    ->formatStateUsing(fn ($record) => $record->getTranslation('alt_text', app()->getLocale()) ?: '-')
                    ->limit(30),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label(__('owner.images.order'))
                    ->sortable()
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('owner.images.type'))
                    ->options([
                        'featured' => __('owner.images.types.featured'),
                        'gallery' => __('owner.images.types.gallery'),
                        'floor_plan' => __('owner.images.types.floor_plan'),
                        'panorama' => __('owner.images.types.panorama'),
                        'exterior' => __('owner.images.types.exterior'),
                        'interior' => __('owner.images.types.interior'),
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('set_featured')
                    ->label(__('owner.images.set_featured'))
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->visible(fn ($record): bool => $record->type !== 'featured')
                    ->requiresConfirmation()
                    ->action(function ($record): void {
                        // Remove featured from other images
                        $this->ownerRecord->images()
                            ->where('type', 'featured')
                            ->update(['type' => 'gallery']);

                        // Set this one as featured
                        $record->update(['type' => 'featured']);

                        // Update hall featured image
                        $this->ownerRecord->update(['featured_image' => $record->image_path]);
                    }),
                Tables\Actions\DeleteAction::make()
                    ->before(function ($record): void {
                        $record->deleteFile();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records): void {
                            $records->each->deleteFile();
                        }),
                ]),
            ]);
    }
}
