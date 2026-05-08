<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Owner\Resources\GalleryResource\Pages\ListGallery;
use App\Filament\Owner\Resources\GalleryResource\Pages\CreateGallery;
use App\Filament\Owner\Resources\GalleryResource\Pages\EditGallery;
use App\Filament\Owner\Resources\GalleryResource\Pages\BulkUpload;
use App\Filament\Owner\Resources\GalleryResource\Pages\ManageGallery;
use App\Filament\Owner\Resources\GalleryResource\Pages;
use App\Models\Hall;
use App\Models\HallImage;
use Filament\Forms;
use Filament\Forms\Form;
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
 * IMPORTANT: Extends OwnerResource for automatic owner scoping.
 *
 * @package App\Filament\Owner\Resources
 */
class GalleryResource extends OwnerResource
{
    /**
     * The model the resource corresponds to.
     */
    protected static ?string $model = HallImage::class;

    /**
     * The navigation icon.
     */
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-photo';

    /**
     * The navigation group.
     */
    //protected static ?string $navigationGroup = 'Hall Management';

    public static function getNavigationGroup(): ?string
    {
        return __('owner.nav_groups.hall_management');
    }

    /**
     * The navigation sort order.
     */
    protected static ?int $navigationSort = 6;

    /**
     * The slug for the resource.
     */
    protected static ?string $slug = 'gallery';

    /**
     * Get the navigation label.
     */
    public static function getNavigationLabel(): string
    {
        return __('owner.gallery.navigation') ?? 'Gallery';
    }

    /**
     * Get the model label.
     */
    public static function getModelLabel(): string
    {
        return __('owner.gallery.singular') ?? 'Image';
    }

    /**
     * Get the plural model label.
     */
    public static function getPluralModelLabel(): string
    {
        return __('owner.gallery.plural') ?? 'Gallery Images';
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
     * Apply owner scope to images query.
     * Only shows images belonging to halls owned by the current user.
     *
     * This overrides the parent OwnerResource method for HallImage specific scoping.
     */
    protected static function applyOwnerScope(Builder $query, $user): Builder
    {
        return $query->whereHas('hall', function (Builder $q) use ($user) {
            $q->where('owner_id', $user->id);
        });
    }

    /**
     * Get the Eloquent query scoped to owner's halls.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['hall'])
            ->orderBy('hall_id')
            ->orderBy('order');
    }

    /**
     * Configure the form for creating/editing images.
     */
    public static function form(Schema $schema): Schema
    {
        $user = Auth::user();

        return $schema
            ->components([
                // Image Upload Section
                Section::make(__('owner.gallery.sections.image') ?? 'Image Upload')
                    ->description(__('owner.gallery.sections.image_desc') ?? 'Upload and configure the image')
                    ->schema([
                        // Hall Selection
                        Select::make('hall_id')
                            ->label(__('owner.gallery.fields.hall') ?? 'Hall')
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
                        FileUpload::make('image_path')
                            ->label(__('owner.gallery.fields.image') ?? 'Image')
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
                            ->helperText(__('owner.gallery.helpers.image') ?? 'Max 5MB. Formats: JPEG, PNG, WebP'),

                        // Image Type
                        Select::make('type')
                            ->label(__('owner.gallery.fields.type') ?? 'Image Type')
                            ->options([
                                'gallery' => __('owner.gallery.types.gallery') ?? 'Gallery',
                                'featured' => __('owner.gallery.types.featured') ?? 'Featured',
                                'floor_plan' => __('owner.gallery.types.floor_plan') ?? 'Floor Plan',
                                'exterior' => __('owner.gallery.types.exterior') ?? 'Exterior',
                                'interior' => __('owner.gallery.types.interior') ?? 'Interior',
                            ])
                            ->default('gallery')
                            ->required()
                            ->native(false),

                        // Featured Toggle
                        Toggle::make('is_featured')
                            ->label(__('owner.gallery.fields.is_featured') ?? 'Featured Image')
                            ->helperText(__('owner.gallery.helpers.is_featured') ?? 'Show in featured sections')
                            ->default(false),

                        // Active Toggle
                        Toggle::make('is_active')
                            ->label(__('owner.gallery.fields.is_active') ?? 'Active')
                            ->helperText(__('owner.gallery.helpers.is_active') ?? 'Only active images are shown')
                            ->default(true),

                        // Order
                        TextInput::make('order')
                            ->label(__('owner.gallery.fields.order') ?? 'Display Order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                    ])
                    ->columns(2),

                // Metadata Section
                Section::make(__('owner.gallery.sections.metadata') ?? 'Image Metadata')
                    ->description(__('owner.gallery.sections.metadata_desc') ?? 'Optional title, caption and SEO information')
                    ->collapsed()
                    ->schema([
                        // Title (English)
                        TextInput::make('title.en')
                            ->label(__('owner.gallery.fields.title_en') ?? 'Title (English)')
                            ->maxLength(150)
                            ->placeholder('e.g., Main Hall Entrance'),

                        // Title (Arabic)
                        TextInput::make('title.ar')
                            ->label(__('owner.gallery.fields.title_ar') ?? 'Title (Arabic)')
                            ->maxLength(150)
                            ->placeholder('مثال: مدخل القاعة الرئيسية'),

                        // Caption (English)
                        Textarea::make('caption.en')
                            ->label(__('owner.gallery.fields.caption_en') ?? 'Caption (English)')
                            ->rows(2)
                            ->maxLength(500),

                        // Caption (Arabic)
                        Textarea::make('caption.ar')
                            ->label(__('owner.gallery.fields.caption_ar') ?? 'Caption (Arabic)')
                            ->rows(2)
                            ->maxLength(500),

                        // Alt Text (for SEO)
                        TextInput::make('alt_text')
                            ->label(__('owner.gallery.fields.alt_text') ?? 'Alt Text')
                            ->maxLength(255)
                            ->helperText(__('owner.gallery.helpers.alt_text') ?? 'Describe the image for accessibility')
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
                ImageColumn::make('image_path')
                    ->label(__('owner.gallery.columns.image') ?? 'Image')
                    ->disk('public')
                    ->width(80)
                    ->height(60)
                    ->square()
                    ->extraImgAttributes(['class' => 'rounded-lg object-cover']),

                // Hall Name
                TextColumn::make('hall.name')
                    ->label(__('owner.gallery.columns.hall') ?? 'Hall')
                    ->formatStateUsing(fn ($record) => $record->hall->getTranslation('name', app()->getLocale()))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Type
                TextColumn::make('type')
                    ->label(__('owner.gallery.columns.type') ?? 'Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __('owner.gallery.types.' . $state, [], app()->getLocale()) ?: $state)
                    ->color(fn (string $state): string => match ($state) {
                        'featured' => 'warning',
                        'floor_plan' => 'info',
                        'exterior' => 'success',
                        'interior' => 'purple',
                        default => 'gray',
                    }),

                // Featured
                IconColumn::make('is_featured')
                    ->label(__('owner.gallery.columns.featured') ?? 'Featured')
                    ->boolean()
                    ->trueIcon('heroicon-s-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                // Active
                IconColumn::make('is_active')
                    ->label(__('owner.gallery.columns.active') ?? 'Active')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                // File Size
                TextColumn::make('file_size')
                    ->label(__('owner.gallery.columns.size') ?? 'Size')
                    ->formatStateUsing(fn ($state) => $state ? static::formatBytes((int) $state) : '-')
                    ->toggleable(),

                // Order
                TextColumn::make('order')
                    ->label(__('owner.gallery.columns.order') ?? 'Order')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                // Created
                TextColumn::make('created_at')
                    ->label(__('owner.gallery.columns.uploaded') ?? 'Uploaded')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Hall Filter
                SelectFilter::make('hall_id')
                    ->label(__('owner.gallery.filters.hall') ?? 'Filter by Hall')
                    ->relationship('hall', 'name', fn (Builder $query) => $query->where('owner_id', Auth::id()))
                    ->getOptionLabelFromRecordUsing(fn (Hall $record) => $record->getTranslation('name', app()->getLocale()))
                    ->searchable()
                    ->preload(),

                // Type Filter
                SelectFilter::make('type')
                    ->label(__('owner.gallery.filters.type') ?? 'Image Type')
                    ->options([
                        'gallery' => 'Gallery',
                        'featured' => 'Featured',
                        'floor_plan' => 'Floor Plan',
                        'exterior' => 'Exterior',
                        'interior' => 'Interior',
                    ]),

                // Featured Filter
                TernaryFilter::make('is_featured')
                    ->label(__('owner.gallery.filters.featured') ?? 'Featured'),

                // Active Filter
                TernaryFilter::make('is_active')
                    ->label(__('owner.gallery.filters.status') ?? 'Status'),
            ])
            ->recordActions([
                // View
                ViewAction::make()
                    ->modalWidth('4xl'),

                // Toggle Featured
                Action::make('toggle_featured')
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
                EditAction::make(),

                // Delete
                DeleteAction::make()
                    ->before(function ($record): void {
                        if ($record->image_path && Storage::disk('public')->exists($record->image_path)) {
                            Storage::disk('public')->delete($record->image_path);
                        }
                        if ($record->thumbnail_path && Storage::disk('public')->exists($record->thumbnail_path)) {
                            Storage::disk('public')->delete($record->thumbnail_path);
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Bulk Activate
                    BulkAction::make('activate')
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
                    BulkAction::make('deactivate')
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

                    // Bulk Delete
                    DeleteBulkAction::make()
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
            ->emptyStateHeading(__('owner.gallery.empty.heading') ?? 'No Images Yet')
            ->emptyStateDescription(__('owner.gallery.empty.description') ?? 'Upload images to showcase your hall')
            ->emptyStateIcon('heroicon-o-photo')
            ->emptyStateActions([
                Action::make('upload')
                    ->label(__('owner.gallery.empty.action') ?? 'Upload First Image')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->url(fn () => static::getUrl('create')),
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
            'index' => ListGallery::route('/'),
            'create' => CreateGallery::route('/create'),
            'edit' => EditGallery::route('/{record}/edit'),
            'upload' => BulkUpload::route('/bulk-upload'),
            'manage' => ManageGallery::route('/manage'),
        ];
    }
}
