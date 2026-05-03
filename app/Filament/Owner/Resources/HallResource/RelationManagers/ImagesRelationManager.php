<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\HallResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
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

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('image_path')
                    ->label(__('owner.images.image'))
                    ->image()
                    ->imageEditor()
                    ->directory('halls/gallery')
                    ->visibility('public')
                    ->maxSize(5120)
                    ->required()
                    ->columnSpanFull(),

                Select::make('type')
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

                TextInput::make('alt_text.en')
                    ->label(__('owner.images.alt_en'))
                    ->maxLength(255)
                    ->helperText(__('owner.images.alt_help')),

                TextInput::make('alt_text.ar')
                    ->label(__('owner.images.alt_ar'))
                    ->maxLength(255)
                    ->extraInputAttributes(['dir' => 'rtl']),

                Textarea::make('caption.en')
                    ->label(__('owner.images.caption_en'))
                    ->rows(2),

                Textarea::make('caption.ar')
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
                ImageColumn::make('image_path')
                    ->label(__('owner.images.preview'))
                    ->size(80)
                    ->square(),

                TextColumn::make('type')
                    ->label(__('owner.images.type'))
                    ->formatStateUsing(fn (string $state): string => __("owner.images.types.{$state}"))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'featured' => 'warning',
                        'floor_plan' => 'info',
                        'panorama' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('alt_text')
                    ->label(__('owner.images.alt'))
                    ->formatStateUsing(fn ($record) => $record->getTranslation('alt_text', app()->getLocale()) ?: '-')
                    ->limit(30),

                TextColumn::make('sort_order')
                    ->label(__('owner.images.order'))
                    ->sortable()
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('type')
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
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('set_featured')
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
                DeleteAction::make()
                    ->before(function ($record): void {
                        $record->deleteFile();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function ($records): void {
                            $records->each->deleteFile();
                        }),
                ]),
            ]);
    }
}
