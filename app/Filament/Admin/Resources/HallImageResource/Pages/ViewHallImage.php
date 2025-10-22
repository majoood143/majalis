<?php

namespace App\Filament\Admin\Resources\HallImageResource\Pages;

use App\Filament\Admin\Resources\HallImageResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ViewHallImage extends ViewRecord
{
    protected static string $resource = HallImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil-square')
                ->color('primary'),

            Actions\Action::make('viewFullImage')
                ->label('View Full Size')
                ->icon('heroicon-o-magnifying-glass-plus')
                ->color('info')
                //->url(fn() => Storage::disk('public')->url($this->record->image_path))
                ->openUrlInNewTab(),

            Actions\Action::make('toggleActive')
                ->label(fn() => $this->record->is_active ? 'Deactivate' : 'Activate')
                ->icon(fn() => $this->record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color(fn() => $this->record->is_active ? 'warning' : 'success')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->is_active = !$this->record->is_active;
                    $this->record->save();

                    Notification::make()
                        ->success()
                        ->title('Status Updated')
                        ->send();

                    Cache::tags(['hall_images', 'hall_' . $this->record->hall_id])->flush();
                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Actions\Action::make('toggleFeatured')
                ->label(fn() => $this->record->is_featured ? 'Unmark Featured' : 'Mark Featured')
                ->icon('heroicon-o-star')
                ->color(fn() => $this->record->is_featured ? 'gray' : 'warning')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->is_featured = !$this->record->is_featured;
                    $this->record->save();

                    Notification::make()
                        ->success()
                        ->title('Featured Status Updated')
                        ->send();

                    Cache::tags(['hall_images', 'hall_' . $this->record->hall_id])->flush();
                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Actions\Action::make('viewHall')
                ->label('View Hall')
                ->icon('heroicon-o-building-storefront')
                ->color('info')
                ->url(fn() => route('filament.admin.resources.halls.view', [
                    'record' => $this->record->hall_id
                ])),

            Actions\Action::make('downloadImage')
                ->label('Download')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    // return Storage::disk('public')->download(
                    //     $this->record->image_path,
                    //     $this->record->hall->name . '_' . $this->record->type . '_' . $this->record->id . '.jpg'
                    // );
                }),

            Actions\Action::make('duplicate')
                ->label('Duplicate')
                ->icon('heroicon-o-document-duplicate')
                ->color('gray')
                ->requiresConfirmation()
                ->action(function () {
                    $newImage = $this->record->replicate();

                    if ($newImage->title) {
                        $title = $newImage->getTranslations('title');
                        foreach ($title as $locale => $value) {
                            if ($value) {
                                $title[$locale] = $value . ' (Copy)';
                            }
                        }
                        $newImage->setTranslations('title', $title);
                    }

                    $newImage->is_featured = false;
                    $newImage->save();

                    Notification::make()
                        ->success()
                        ->title('Image Duplicated')
                        ->actions([
                            \Filament\Notifications\Actions\Action::make('view')
                                ->label('View Duplicate')
                                ->url(HallImageResource::getUrl('view', ['record' => $newImage->id])),
                        ])
                        ->send();
                }),

            Actions\DeleteAction::make()
                ->before(function () {
                    if ($this->record->image_path) {
                        Storage::disk('public')->delete($this->record->image_path);
                    }
                    if ($this->record->thumbnail_path) {
                        Storage::disk('public')->delete($this->record->thumbnail_path);
                    }
                })
                ->successRedirectUrl(route('filament.admin.resources.hall-images.index')),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Image Preview')
                    ->schema([
                        Infolists\Components\ImageEntry::make('image_path')
                            ->label('')
                            ->disk('public')
                            ->height(400)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Infolists\Components\Section::make('Image Information')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('hall.name')
                                    ->label('Hall')
                                    ->badge()
                                    ->color('success')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->icon('heroicon-o-building-storefront'),

                                Infolists\Components\TextEntry::make('type')
                                    ->label('Image Type')
                                    ->formatStateUsing(fn($record) => $record->type_label ?? ucfirst($record->type))
                                    ->badge()
                                    ->color(fn($state) => match ($state) {
                                        'gallery' => 'info',
                                        'featured' => 'warning',
                                        'floor_plan' => 'purple',
                                        '360_view' => 'success',
                                        default => 'gray',
                                    })
                                    ->icon(fn($state) => match ($state) {
                                        'gallery' => 'heroicon-o-photo',
                                        'featured' => 'heroicon-o-star',
                                        'floor_plan' => 'heroicon-o-map',
                                        '360_view' => 'heroicon-o-globe-alt',
                                        default => 'heroicon-o-photo',
                                    }),

                                Infolists\Components\TextEntry::make('order')
                                    ->label('Display Order')
                                    ->badge()
                                    ->color('gray')
                                    ->icon('heroicon-o-bars-3'),
                            ]),

                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\IconEntry::make('is_active')
                                    ->label('Active Status')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger')
                                    ->size(Infolists\Components\IconEntry\IconEntrySize::Large),

                                Infolists\Components\IconEntry::make('is_featured')
                                    ->label('Featured')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-star')
                                    ->falseIcon('heroicon-o-minus-circle')
                                    ->trueColor('warning')
                                    ->falseColor('gray')
                                    ->size(Infolists\Components\IconEntry\IconEntrySize::Large),
                            ]),
                    ])
                    ->icon('heroicon-o-information-circle')
                    ->collapsible(),

                Infolists\Components\Section::make('Image Details')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('title.en')
                                    ->label('Title (English)')
                                    ->placeholder('No title')
                                    ->icon('heroicon-o-language'),

                                Infolists\Components\TextEntry::make('title.ar')
                                    ->label('Title (Arabic)')
                                    ->placeholder('لا يوجد عنوان')
                                    ->icon('heroicon-o-language'),
                            ]),

                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('caption.en')
                                    ->label('Caption (English)')
                                    ->placeholder('No caption')
                                    ->columnSpanFull(),

                                Infolists\Components\TextEntry::make('caption.ar')
                                    ->label('Caption (Arabic)')
                                    ->placeholder('لا يوجد تعليق')
                                    ->columnSpanFull(),
                            ]),

                        Infolists\Components\TextEntry::make('alt_text')
                            ->label('Alt Text (SEO)')
                            ->placeholder('No alt text')
                            ->icon('heroicon-o-magnifying-glass')
                            ->columnSpanFull(),
                    ])
                    ->icon('heroicon-o-document-text')
                    ->collapsible(),

                Infolists\Components\Section::make('Technical Details')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('formatted_size')
                                    ->label('File Size')
                                    ->state(fn($record) => $this->formatBytes($record->file_size ?? 0))
                                    ->badge()
                                    ->color(fn($record) => ($record->file_size ?? 0) > 2097152 ? 'warning' : 'success')
                                    ->icon('heroicon-o-server'),

                                Infolists\Components\TextEntry::make('dimensions')
                                    ->label('Dimensions')
                                    ->placeholder('Unknown')
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-arrows-pointing-out'),

                                Infolists\Components\TextEntry::make('aspect_ratio')
                                    ->label('Aspect Ratio')
                                    ->state(function ($record) {
                                        if ($record->dimensions) {
                                            $dims = explode('x', $record->dimensions);
                                            if (count($dims) === 2) {
                                                $ratio = round($dims[0] / $dims[1], 2);
                                                return $ratio . ':1';
                                            }
                                        }
                                        return 'Unknown';
                                    })
                                    ->badge()
                                    ->color('purple')
                                    ->icon('heroicon-o-chart-bar'),

                                Infolists\Components\TextEntry::make('file_format')
                                    ->label('Format')
                                    ->state(fn($record) => strtoupper(pathinfo($record->image_path, PATHINFO_EXTENSION)))
                                    ->badge()
                                    ->color('gray')
                                    ->icon('heroicon-o-document'),
                            ]),

                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('image_path')
                                    ->label('File Path')
                                    ->copyable()
                                    ->icon('heroicon-o-folder'),

                                Infolists\Components\TextEntry::make('thumbnail_status')
                                    ->label('Thumbnail')
                                    ->state(fn($record) => $record->thumbnail_path ? 'Available' : 'Not Generated')
                                    ->badge()
                                    ->color(fn($record) => $record->thumbnail_path ? 'success' : 'warning')
                                    ->icon(fn($record) => $record->thumbnail_path ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),
                            ]),
                    ])
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsible(),

                Infolists\Components\Section::make('Image Quality Analysis')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('optimization_status')
                                    ->label('Optimization')
                                    ->state(function ($record) {
                                        $size = $record->file_size ?? 0;
                                        if ($size < 524288) return 'Optimized'; // < 512KB
                                        if ($size < 2097152) return 'Good'; // < 2MB
                                        return 'Needs Optimization';
                                    })
                                    ->badge()
                                    ->color(function ($record) {
                                        $size = $record->file_size ?? 0;
                                        if ($size < 524288) return 'success';
                                        if ($size < 2097152) return 'info';
                                        return 'warning';
                                    })
                                    ->icon('heroicon-o-sparkles'),

                                Infolists\Components\TextEntry::make('resolution_quality')
                                    ->label('Resolution')
                                    ->state(function ($record) {
                                        if ($record->dimensions) {
                                            $dims = explode('x', $record->dimensions);
                                            $pixels = $dims[0] * $dims[1];
                                            if ($pixels >= 2073600) return 'High Quality'; // >= 1920x1080
                                            if ($pixels >= 786432) return 'Standard'; // >= 1024x768
                                            return 'Low Quality';
                                        }
                                        return 'Unknown';
                                    })
                                    ->badge()
                                    ->color(function ($record) {
                                        if ($record->dimensions) {
                                            $dims = explode('x', $record->dimensions);
                                            $pixels = $dims[0] * $dims[1];
                                            if ($pixels >= 2073600) return 'success';
                                            if ($pixels >= 786432) return 'info';
                                            return 'warning';
                                        }
                                        return 'gray';
                                    })
                                    ->icon('heroicon-o-photo'),

                                Infolists\Components\TextEntry::make('web_ready')
                                    ->label('Web Ready')
                                    ->state(function ($record) {
                                        $size = $record->file_size ?? 0;
                                        $hasAlt = !empty($record->alt_text);

                                        if ($size < 2097152 && $hasAlt) return 'Yes';
                                        return 'Needs Attention';
                                    })
                                    ->badge()
                                    ->color(function ($record) {
                                        $size = $record->file_size ?? 0;
                                        $hasAlt = !empty($record->alt_text);

                                        if ($size < 2097152 && $hasAlt) return 'success';
                                        return 'warning';
                                    })
                                    ->icon('heroicon-o-globe-alt'),
                            ]),
                    ])
                    ->icon('heroicon-o-chart-bar-square')
                    ->collapsible(),

                Infolists\Components\Section::make('Thumbnail Preview')
                    ->schema([
                        Infolists\Components\ImageEntry::make('thumbnail_path')
                            ->label('Thumbnail')
                            ->disk('public')
                            ->height(150)
                            ->columnSpanFull(),
                    ])
                    ->icon('heroicon-o-photo')
                    ->visible(fn($record) => $record->thumbnail_path !== null)
                    ->collapsible()
                    ->collapsed(),

                Infolists\Components\Section::make('System Information')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('id')
                                    ->label('Image ID')
                                    ->badge()
                                    ->color('gray')
                                    ->copyable()
                                    ->icon('heroicon-o-hashtag'),

                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Uploaded At')
                                    ->dateTime('d M Y, h:i A')
                                    ->icon('heroicon-o-calendar'),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime('d M Y, h:i A')
                                    ->since()
                                    ->icon('heroicon-o-clock'),
                            ]),
                    ])
                    ->icon('heroicon-o-server')
                    ->collapsed(),

                Infolists\Components\Section::make('Activity History')
                    ->schema([
                        Infolists\Components\ViewEntry::make('activity_log')
                            ->label('')
                            ->view('filament.infolists.components.activity-log', [
                                'activities' => fn($record) => activity()
                                    ->forSubject($record)
                                    ->latest()
                                    ->limit(10)
                                    ->get()
                            ]),
                    ])
                    ->icon('heroicon-o-clock')
                    ->collapsed()
                    ->visible(fn() => class_exists(\Spatie\Activitylog\Models\Activity::class)),
            ]);
    }

    public function getTitle(): string
    {
        return 'View Image: ' . ($this->record->title ?: 'Untitled');
    }

    public function getSubheading(): ?string
    {
        $hall = $this->record->hall->name ?? 'Unknown Hall';
        $type = $this->record->type_label ?? ucfirst($this->record->type);
        $size = $this->formatBytes($this->record->file_size ?? 0);

        return "{$hall} • {$type} • {$size}";
    }

    protected function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public function getBreadcrumb(): string
    {
        return $this->record->title ?: 'Image #' . $this->record->id;
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
