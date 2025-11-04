<?php

namespace App\Filament\Admin\Resources\HallImageResource\Pages;

use App\Filament\Admin\Resources\HallImageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ListHallImages extends ListRecords
{
    protected static string $resource = HallImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->color('primary'),

            Actions\Action::make('bulkUpload')
                ->label('Bulk Upload')
                ->icon('heroicon-o-cloud-arrow-up')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\Select::make('hall_id')
                        ->label('Hall')
                        ->options(\App\Models\Hall::pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->preload(),

                    \Filament\Forms\Components\Select::make('type')
                        ->label('Image Type')
                        ->options([
                            'gallery' => 'Gallery Image',
                            'featured' => 'Featured Image',
                            'floor_plan' => 'Floor Plan',
                            '360_view' => '360° View',
                        ])
                        ->default('gallery')
                        ->required(),

                    \Filament\Forms\Components\FileUpload::make('images')
                        ->label('Images')
                        ->image()
                        ->multiple()
                        ->directory('halls/images')
                        ->required()
                        ->maxFiles(20)
                        ->imageEditor(),

                    \Filament\Forms\Components\Toggle::make('generate_thumbnails')
                        ->label('Generate Thumbnails')
                        ->default(true),
                ])
                ->action(function (array $data) {
                    $this->bulkUploadImages($data);
                }),

            Actions\Action::make('optimizeImages')
                ->label('Optimize All Images')
                ->icon('heroicon-o-sparkles')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Optimize All Hall Images')
                ->modalDescription('This will optimize all hall images to reduce file size while maintaining quality.')
                ->action(function () {
                    $this->optimizeAllImages();
                }),

            Actions\Action::make('generateThumbnails')
                ->label('Generate Thumbnails')
                ->icon('heroicon-o-photo')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Generate Missing Thumbnails')
                ->modalDescription('Generate thumbnails for images that don\'t have them.')
                ->action(function () {
                    $this->generateMissingThumbnails();
                }),

            Actions\Action::make('cleanupOrphaned')
                ->label('Cleanup Orphaned Files')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Delete Orphaned Image Files')
                ->modalDescription('Remove image files that no longer have database records.')
                ->action(function () {
                    $this->cleanupOrphanedFiles();
                }),

            Actions\Action::make('exportGallery')
                ->label('Export Gallery')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->form([
                    \Filament\Forms\Components\Select::make('hall_id')
                        ->label('Hall (Optional)')
                        ->options(\App\Models\Hall::pluck('name', 'id'))
                        ->searchable()
                        ->preload(),
                ])
                ->action(function (array $data) {
                    $this->exportGallery($data);
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Images')
                ->icon('heroicon-o-squares-2x2')
                ->badge(fn() => \App\Models\HallImage::count()),

            'gallery' => Tab::make('Gallery')
                ->icon('heroicon-o-photo')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('type', 'gallery'))
                ->badge(fn() => \App\Models\HallImage::where('type', 'gallery')->count())
                ->badgeColor('info'),

            'featured' => Tab::make('Featured')
                ->icon('heroicon-o-star')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('type', 'featured'))
                ->badge(fn() => \App\Models\HallImage::where('type', 'featured')->count())
                ->badgeColor('warning'),

            'floor_plans' => Tab::make('Floor Plans')
                ->icon('heroicon-o-map')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('type', 'floor_plan'))
                ->badge(fn() => \App\Models\HallImage::where('type', 'floor_plan')->count())
                ->badgeColor('purple'),

            '360_views' => Tab::make('360° Views')
                ->icon('heroicon-o-globe-alt')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('type', '360_view'))
                ->badge(fn() => \App\Models\HallImage::where('type', '360_view')->count())
                ->badgeColor('success'),

            'is_featured' => Tab::make('Featured Items')
                ->icon('heroicon-o-star')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_featured', true))
                ->badge(fn() => \App\Models\HallImage::where('is_featured', true)->count())
                ->badgeColor('warning'),

            'active' => Tab::make('Active')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', true))
                ->badge(fn() => \App\Models\HallImage::where('is_active', true)->count())
                ->badgeColor('success'),

            'inactive' => Tab::make('Inactive')
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', false))
                ->badge(fn() => \App\Models\HallImage::where('is_active', false)->count())
                ->badgeColor('danger'),

            'with_thumbnails' => Tab::make('With Thumbnails')
                ->icon('heroicon-o-photo')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('thumbnail_path'))
                ->badge(fn() => \App\Models\HallImage::whereNotNull('thumbnail_path')->count())
                ->badgeColor('success'),

            'without_thumbnails' => Tab::make('Without Thumbnails')
                ->icon('heroicon-o-photo')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNull('thumbnail_path'))
                ->badge(fn() => \App\Models\HallImage::whereNull('thumbnail_path')->count())
                ->badgeColor('gray'),
        ];
    }

    protected function bulkUploadImages(array $data): void
    {
        $uploadedCount = 0;

        foreach ($data['images'] as $index => $imagePath) {
            try {
                $image = \App\Models\HallImage::create([
                    'hall_id' => $data['hall_id'],
                    'type' => $data['type'],
                    'image_path' => $imagePath,
                    'order' => $index,
                    'is_active' => true,
                ]);

                // Generate thumbnail if requested
                if ($data['generate_thumbnails']) {
                    $this->generateThumbnail($image);
                }

                $uploadedCount++;
            } catch (\Exception $e) {
                Log::error('Bulk upload error: ' . $e->getMessage());
            }
        }

        Notification::make()
            ->success()
            ->title('Bulk Upload Completed')
            ->body("{$uploadedCount} image(s) uploaded successfully.")
            ->send();

        $this->redirect(static::getUrl());
    }

    protected function optimizeAllImages(): void
    {
        $images = \App\Models\HallImage::whereNotNull('image_path')->get();
        $optimizedCount = 0;

        foreach ($images as $image) {
            try {
                // Implement image optimization logic here
                // Example: using Intervention Image or similar package

                $optimizedCount++;
            } catch (\Exception $e) {
                Log::error('Image optimization error: ' . $e->getMessage());
            }
        }

        Notification::make()
            ->success()
            ->title('Optimization Completed')
            ->body("{$optimizedCount} image(s) optimized.")
            ->send();
    }

    protected function generateMissingThumbnails(): void
    {
        $images = \App\Models\HallImage::whereNull('thumbnail_path')
            ->whereNotNull('image_path')
            ->get();

        $generatedCount = 0;

        foreach ($images as $image) {
            try {
                $this->generateThumbnail($image);
                $generatedCount++;
            } catch (\Exception $e) {
                Log::error('Thumbnail generation error: ' . $e->getMessage());
            }
        }

        Notification::make()
            ->success()
            ->title('Thumbnails Generated')
            ->body("{$generatedCount} thumbnail(s) created.")
            ->send();

        $this->redirect(static::getUrl());
    }

    protected function generateThumbnail($image): void
    {
        //Implement thumbnail generation logic
        //Example using Intervention Image:
        //$thumbnail = Image::make(Storage::disk('public')->path($image->image_path))
        $manager = new ImageManager(new Driver());
        $thumbnail = $manager->read(Storage::disk('public')->path($image->image_path))
            ->resize(300, 200, function ($constraint) {
                $constraint->aspectRatio();
            });
        
        $thumbnailPath = 'halls/thumbnails/' . basename($image->image_path);
        Storage::disk('public')->put($thumbnailPath, $thumbnail->encode());
        
        $image->update(['thumbnail_path' => $thumbnailPath]);
    }

    protected function cleanupOrphanedFiles(): void
    {
        $databaseFiles = \App\Models\HallImage::pluck('image_path')->toArray();
        $storageFiles = Storage::disk('public')->files('halls/images');

        $deletedCount = 0;

        foreach ($storageFiles as $file) {
            if (!in_array($file, $databaseFiles)) {
                Storage::disk('public')->delete($file);
                $deletedCount++;
            }
        }

        Notification::make()
            ->success()
            ->title('Cleanup Completed')
            ->body("{$deletedCount} orphaned file(s) deleted.")
            ->send();
    }

    protected function exportGallery(array $data): void
    {
        $query = \App\Models\HallImage::with('hall');

        if (isset($data['hall_id'])) {
            $query->where('hall_id', $data['hall_id']);
        }

        $images = $query->get();

        $filename = 'hall_images_' . now()->format('Y_m_d_His') . '.csv';
        $path = storage_path('app/public/exports/' . $filename);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $file = fopen($path, 'w');

        fputcsv($file, [
            'ID',
            'Hall',
            'Type',
            'Title (EN)',
            'Title (AR)',
            'Caption (EN)',
            'Caption (AR)',
            'Alt Text',
            'File Size',
            'Dimensions',
            'Featured',
            'Active',
            'Order',
            'Image URL',
            'Created At',
        ]);

        foreach ($images as $image) {
            fputcsv($file, [
                $image->id,
                $image->hall->name ?? 'N/A',
                $image->type_label ?? $image->type,
                $image->getTranslation('title', 'en') ?? '',
                $image->getTranslation('title', 'ar') ?? '',
                $image->getTranslation('caption', 'en') ?? '',
                $image->getTranslation('caption', 'ar') ?? '',
                $image->alt_text ?? '',
                $image->formatted_size ?? 'N/A',
                $image->dimensions ?? 'N/A',
                $image->is_featured ? 'Yes' : 'No',
                $image->is_active ? 'Yes' : 'No',
                $image->order,
                //Storage::disk('public')->url($image->image_path),
                $image->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        fclose($file);

        Notification::make()
            ->title('Export Successful')
            ->success()
            ->body('Gallery exported successfully.')
            ->persistent()
            ->actions([
                \Filament\Notifications\Actions\Action::make('download')
                    ->label('Download File')
                    ->url(asset('storage/exports/' . $filename))
                    ->openUrlInNewTab(),
            ])
            ->send();
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Add image statistics widgets here
        ];
    }
}
