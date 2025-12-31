<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\GalleryResource\Pages;
use App\Models\Hall;
use App\Models\HallImage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * GalleryResource for Owner Panel
 *
 * Comprehensive gallery management for hall owners.
 * Upload, organize, and manage images for halls.
 *
 * Features:
 * - Upload single/multiple images
 * - Set featured images
 * - Reorder images with drag-and-drop
 * - Edit image metadata (title, caption, alt)
 * - View image details and dimensions
 * - Bulk operations
 *
 * @package App\Filament\Owner\Resources
 */
class GalleryResource extends Resource
{
    /**
     * The model the resource corresponds to.
     */
    protected static ?string $model = HallImage::class;

    /**
     * The navigation icon.
     */
    protected static ?string $navigationIcon = 'heroicon-o-photo';

    /**
     * The navigation group.
     */
    protected static ?string $navigationGroup = 'Hall Management';

    /**
     * The navigation sort order.
     */
    protected static ?int $navigationSort = 6;

    /**
     * Get the navigation label.
     */
    public static function getNavigationLabel(): string
    {
        return __('owner.gallery.navigation');
    }

    /**
     * Get the model label.
     */
    public static function getModelLabel(): string
    {
        return __('owner.gallery.singular');
    }

    /**
     * Get the plural model label.
     */
    public static function getPluralModelLabel(): string
    {
        return __('owner.gallery.plural');
    }

    /**
     * Get the navigation badge (total images).
     */
    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        $count = HallImage::whereHas('hall', function (Builder $query) use ($user) {
            $query->where('owner_id', $user->id);
        })->count();

        return $count > 0 ? (string) $count : null;
    }

    /**
     * Get the navigation badge color.
     */
    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    /**
     * Get the Eloquent query scoped to owner's halls.
     */
    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        return parent::getEloquentQuery()
            ->whereHas('hall', function (Builder $query) use ($user) {
                $query->where('owner_id', $user?->id);
            })
            ->with(['hall'])
            ->orderBy('hall_id')
            ->orderBy('order');
    }

    /**
     * Configure the form for creating/editing images.
     */
    public static function form(Form $form): Form
    {
        $user = Auth::user();

        return $form
            ->schema([
                // Image Upload Section
                Forms\Components\Section::make(__('owner.gallery.sections.image'))
                    ->description(__('owner.gallery.sections.image_desc'))
                    ->schema([
                        // Hall Selection
                        Forms\Components\Select::make('hall_id')
                            ->label(__('owner.gallery.fields.hall'))
                            ->relationship(
                                name: 'hall',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->where('owner_id', $user?->id)
                            )
                            ->getOptionLabelFromRecordUsing(fn (Hall $record) => $record->getTranslation('name', app()->getLocale()))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false)
                            ->columnSpanFull(),

                        // Image Upload
                        Forms\Components\FileUpload::make('image_path')
                            ->label(__('owner.gallery.fields.image'))
                            ->image()
                            ->disk('public')
                            ->directory('halls/images')
                            ->visibility('public')
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('1920')
                            ->imageResizeTargetHeight('1080')
                            ->maxSize(5120) // 5MB
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->required()
                            ->columnSpanFull()
                            ->helperText(__('owner.gallery.helpers.image')),

                        // Image Type
                        Forms\Components\Select::make('type')
                            ->label(__('owner.gallery.fields.type'))
                            ->options([
                                'gallery' => __('owner.gallery.types.gallery'),
                                'featured' => __('owner.gallery.types.featured'),
                                'floor_plan' => __('owner.gallery.types.floor_plan'),
                                'exterior' => __('owner.gallery.types.exterior'),
                                'interior' => __('owner.gallery.types.interior'),
                            ])
                            ->default('gallery')
                            ->required()
                            ->native(false),

                        // Featured Toggle
                        Forms\Components\Toggle::make('is_featured')
                            ->label(__('owner.gallery.fields.is_featured'))
                            ->helperText(__('owner.gallery.helpers.is_featured'))
                            ->default(false),

                        // Active Toggle
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('owner.gallery.fields.is_active'))
                            ->helperText(__('owner.gallery.helpers.is_active'))
                            ->default(true),

                        // Order
                        Forms\Components\TextInput::make('order')
                            ->label(__('owner.gallery.fields.order'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                    ])
                    ->columns(2),

                // Metadata Section
                Forms\Components\Section::make(__('owner.gallery.sections.metadata'))
                    ->description(__('owner.gallery.sections.metadata_desc'))
                    ->collapsed()
                    ->schema([
                        // Title (English)
                        Forms\Components\TextInput::make('title.en')
                            ->label(__('owner.gallery.fields.title_en'))
                            ->maxLength(150)
                            ->placeholder(__('owner.gallery.placeholders.title')),

                        // Title (Arabic)
                        Forms\Components\TextInput::make('title.ar')
                            ->label(__('owner.gallery.fields.title_ar'))
                            ->maxLength(150)
                            ->placeholder(__('owner.gallery.placeholders.title_ar')),

                        // Caption (English)
                        Forms\Components\Textarea::make('caption.en')
                            ->label(__('owner.gallery.fields.caption_en'))
                            ->rows(2)
                            ->maxLength(500),

                        // Caption (Arabic)
                        Forms\Components\Textarea::make('caption.ar')
                            ->label(__('owner.gallery.fields.caption_ar'))
                            ->rows(2)
                            ->maxLength(500),

                        // Alt Text (for SEO)
                        Forms\Components\TextInput::make('alt_text')
                            ->label(__('owner.gallery.fields.alt_text'))
                            ->maxLength(255)
                            ->helperText(__('owner.gallery.helpers.alt_text'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    /**
     * Configure the table for listing images.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('hall_id')
            ->defaultGroup('hall.name')
            ->reorderable('order')
            ->columns([
                // Image Preview
                Tables\Columns\ImageColumn::make('image_path')
                    ->label(__('owner.gallery.columns.image'))
                    ->disk('public')
                    ->width(80)
                    ->height(60)
                    ->square()
                    ->extraImgAttributes(['class' => 'rounded-lg object-cover']),

                // Hall Name
                Tables\Columns\TextColumn::make('hall.name')
                    ->label(__('owner.gallery.columns.hall'))
                    ->formatStateUsing(fn ($record) => $record->hall->getTranslation('name', app()->getLocale()))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Title
                Tables\Columns\TextColumn::make('title')
                    ->label(__('owner.gallery.columns.title'))
                    ->formatStateUsing(function ($record) {
                        $title = $record->getTranslation('title', app()->getLocale());
                        return $title ?: '-';
                    })
                    ->limit(30)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function ($q) use ($search) {
                            $q->whereRaw("LOWER(JSON_EXTRACT(title, '$.en')) LIKE ?", ['%' . strtolower($search) . '%'])
                                ->orWhereRaw("LOWER(JSON_EXTRACT(title, '$.ar')) LIKE ?", ['%' . strtolower($search) . '%']);
                        });
                    }),

                // Type
                Tables\Columns\TextColumn::make('type')
                    ->label(__('owner.gallery.columns.type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __("owner.gallery.types.{$state}"))
                    ->color(fn (string $state): string => match ($state) {
                        'featured' => 'warning',
                        'floor_plan' => 'info',
                        'exterior' => 'success',
                        'interior' => 'purple',
                        default => 'gray',
                    }),

                // Featured
                Tables\Columns\IconColumn::make('is_featured')
                    ->label(__('owner.gallery.columns.featured'))
                    ->boolean()
                    ->trueIcon('heroicon-s-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                // Active
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('owner.gallery.columns.active'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                // File Size
                Tables\Columns\TextColumn::make('file_size')
                    ->label(__('owner.gallery.columns.size'))
                    ->formatStateUsing(fn ($state) => $state ? static::formatBytes((int) $state) : '-')
                    ->toggleable(),

                // Dimensions
                Tables\Columns\TextColumn::make('dimensions')
                    ->label(__('owner.gallery.columns.dimensions'))
                    ->state(fn ($record) => $record->width && $record->height
                        ? "{$record->width}×{$record->height}"
                        : '-')
                    ->toggleable(isToggledHiddenByDefault: true),

                // Order
                Tables\Columns\TextColumn::make('order')
                    ->label(__('owner.gallery.columns.order'))
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                // Created
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('owner.gallery.columns.uploaded'))
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Hall Filter
                Tables\Filters\SelectFilter::make('hall_id')
                    ->label(__('owner.gallery.filters.hall'))
                    ->relationship('hall', 'name', fn (Builder $query) => $query->where('owner_id', Auth::id()))
                    ->getOptionLabelFromRecordUsing(fn (Hall $record) => $record->getTranslation('name', app()->getLocale()))
                    ->searchable()
                    ->preload(),

                // Type Filter
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('owner.gallery.filters.type'))
                    ->options([
                        'gallery' => __('owner.gallery.types.gallery'),
                        'featured' => __('owner.gallery.types.featured'),
                        'floor_plan' => __('owner.gallery.types.floor_plan'),
                        'exterior' => __('owner.gallery.types.exterior'),
                        'interior' => __('owner.gallery.types.interior'),
                    ]),

                // Featured Filter
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label(__('owner.gallery.filters.featured'))
                    ->trueLabel(__('owner.gallery.filters.featured_only'))
                    ->falseLabel(__('owner.gallery.filters.not_featured')),

                // Active Filter
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('owner.gallery.filters.status'))
                    ->trueLabel(__('owner.gallery.filters.active_only'))
                    ->falseLabel(__('owner.gallery.filters.inactive_only')),
            ])
            ->actions([
                // View
                Tables\Actions\ViewAction::make()
                    ->modalWidth('4xl'),

                // Toggle Featured
                Tables\Actions\Action::make('toggle_featured')
                    ->label(fn ($record): string => $record->is_featured
                        ? __('owner.gallery.actions.unmark_featured')
                        : __('owner.gallery.actions.mark_featured'))
                    ->icon('heroicon-o-star')
                    ->color(fn ($record): string => $record->is_featured ? 'gray' : 'warning')
                    ->action(function ($record): void {
                        $record->update(['is_featured' => !$record->is_featured]);

                        Notification::make()
                            ->success()
                            ->title($record->is_featured
                                ? __('owner.gallery.notifications.marked_featured')
                                : __('owner.gallery.notifications.unmarked_featured'))
                            ->send();
                    }),

                // Edit
                Tables\Actions\EditAction::make(),

                // Delete
                Tables\Actions\DeleteAction::make()
                    ->before(function ($record): void {
                        // Delete file from storage
                        if ($record->image_path && Storage::disk('public')->exists($record->image_path)) {
                            Storage::disk('public')->delete($record->image_path);
                        }
                        if ($record->thumbnail_path && Storage::disk('public')->exists($record->thumbnail_path)) {
                            Storage::disk('public')->delete($record->thumbnail_path);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Bulk Activate
                    Tables\Actions\BulkAction::make('activate')
                        ->label(__('owner.gallery.bulk.activate'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Collection $records): void {
                            $records->each(fn ($record) => $record->update(['is_active' => true]));

                            Notification::make()
                                ->success()
                                ->title(__('owner.gallery.notifications.bulk_activated', ['count' => $records->count()]))
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    // Bulk Deactivate
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label(__('owner.gallery.bulk.deactivate'))
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->action(function (Collection $records): void {
                            $records->each(fn ($record) => $record->update(['is_active' => false]));

                            Notification::make()
                                ->success()
                                ->title(__('owner.gallery.notifications.bulk_deactivated', ['count' => $records->count()]))
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    // Bulk Change Type
                    Tables\Actions\BulkAction::make('change_type')
                        ->label(__('owner.gallery.bulk.change_type'))
                        ->icon('heroicon-o-tag')
                        ->color('info')
                        ->form([
                            Forms\Components\Select::make('type')
                                ->label(__('owner.gallery.fields.type'))
                                ->options([
                                    'gallery' => __('owner.gallery.types.gallery'),
                                    'featured' => __('owner.gallery.types.featured'),
                                    'floor_plan' => __('owner.gallery.types.floor_plan'),
                                    'exterior' => __('owner.gallery.types.exterior'),
                                    'interior' => __('owner.gallery.types.interior'),
                                ])
                                ->required()
                                ->native(false),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each(fn ($record) => $record->update(['type' => $data['type']]));

                            Notification::make()
                                ->success()
                                ->title(__('owner.gallery.notifications.bulk_type_changed', ['count' => $records->count()]))
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    // Bulk Delete
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (Collection $records): void {
                            foreach ($records as $record) {
                                if ($record->image_path && Storage::disk('public')->exists($record->image_path)) {
                                    Storage::disk('public')->delete($record->image_path);
                                }
                                if ($record->thumbnail_path && Storage::disk('public')->exists($record->thumbnail_path)) {
                                    Storage::disk('public')->delete($record->thumbnail_path);
                                }
                            }
                        }),
                ]),
            ])
            ->emptyStateHeading(__('owner.gallery.empty.heading'))
            ->emptyStateDescription(__('owner.gallery.empty.description'))
            ->emptyStateIcon('heroicon-o-photo')
            ->emptyStateActions([
                Tables\Actions\Action::make('upload')
                    ->label(__('owner.gallery.empty.action'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->url(fn () => static::getUrl('create')),
            ]);
    }

    /**
     * Configure the infolist for viewing image details.
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Image Preview
                Infolists\Components\Section::make(__('owner.gallery.sections.preview'))
                    ->schema([
                        Infolists\Components\ImageEntry::make('image_path')
                            ->label('')
                            ->disk('public')
                            ->height(400)
                            ->columnSpanFull()
                            ->extraImgAttributes(['class' => 'rounded-xl']),
                    ]),

                // Image Info
                Infolists\Components\Section::make(__('owner.gallery.sections.info'))
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('hall.name')
                                    ->label(__('owner.gallery.fields.hall'))
                                    ->formatStateUsing(fn ($record) => $record->hall->getTranslation('name', app()->getLocale()))
                                    ->badge()
                                    ->color('success'),

                                Infolists\Components\TextEntry::make('type')
                                    ->label(__('owner.gallery.fields.type'))
                                    ->formatStateUsing(fn (string $state): string => __("owner.gallery.types.{$state}"))
                                    ->badge(),

                                Infolists\Components\IconEntry::make('is_featured')
                                    ->label(__('owner.gallery.fields.is_featured'))
                                    ->boolean()
                                    ->trueIcon('heroicon-s-star')
                                    ->falseIcon('heroicon-o-star')
                                    ->trueColor('warning'),

                                Infolists\Components\TextEntry::make('file_size')
                                    ->label(__('owner.gallery.fields.file_size'))
                                    ->formatStateUsing(fn ($state) => $state ? static::formatBytes((int) $state) : '-'),

                                Infolists\Components\TextEntry::make('dimensions')
                                    ->label(__('owner.gallery.fields.dimensions'))
                                    ->state(fn ($record) => $record->width && $record->height
                                        ? "{$record->width}×{$record->height} px"
                                        : '-'),

                                Infolists\Components\TextEntry::make('mime_type')
                                    ->label(__('owner.gallery.fields.format'))
                                    ->default('-'),

                                Infolists\Components\TextEntry::make('order')
                                    ->label(__('owner.gallery.fields.order'))
                                    ->badge()
                                    ->color('gray'),

                                Infolists\Components\IconEntry::make('is_active')
                                    ->label(__('owner.gallery.fields.is_active'))
                                    ->boolean(),

                                Infolists\Components\TextEntry::make('created_at')
                                    ->label(__('owner.gallery.fields.uploaded_at'))
                                    ->dateTime('d M Y, H:i'),
                            ]),
                    ]),

                // Metadata
                Infolists\Components\Section::make(__('owner.gallery.sections.metadata'))
                    ->schema([
                        Infolists\Components\TextEntry::make('title')
                            ->label(__('owner.gallery.fields.title'))
                            ->formatStateUsing(function ($record) {
                                $en = $record->getTranslation('title', 'en');
                                $ar = $record->getTranslation('title', 'ar');
                                if (!$en && !$ar) return '-';
                                return ($en ?: '-') . ' / ' . ($ar ?: '-');
                            }),

                        Infolists\Components\TextEntry::make('caption')
                            ->label(__('owner.gallery.fields.caption'))
                            ->formatStateUsing(fn ($record) => $record->getTranslation('caption', app()->getLocale()) ?: '-'),

                        Infolists\Components\TextEntry::make('alt_text')
                            ->label(__('owner.gallery.fields.alt_text'))
                            ->default('-'),
                    ])
                    ->collapsed(),
            ]);
    }

    /**
     * Format bytes to human readable format.
     */
    public static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Get the pages for the resource.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGallery::route('/'),
            'create' => Pages\CreateGallery::route('/create'),
            'edit' => Pages\EditGallery::route('/{record}/edit'),
            'upload' => Pages\BulkUpload::route('/upload'),
            'manage' => Pages\ManageGallery::route('/manage'),
        ];
    }
}
