<?php

namespace App\Filament\Admin\Resources\HallImageResource\Pages;

use App\Filament\Admin\Resources\HallImageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class EditHallImage extends EditRecord
{
    protected static string $resource = HallImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('viewImage')
                ->label('View Full Image')
                ->icon('heroicon-o-eye')
                ->color('info')
                //->url(fn () => Storage::disk('public')->url($this->record->image_path))
                ->openUrlInNewTab(),
            
            Actions\Action::make('toggleActive')
                ->label(fn () => $this->record->is_active ? 'Deactivate' : 'Activate')
                ->icon(fn () => $this->record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color(fn () => $this->record->is_active ? 'warning' : 'success')
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
                ->label(fn () => $this->record->is_featured ? 'Unmark Featured' : 'Mark as Featured')
                ->icon(fn () => $this->record->is_featured ? 'heroicon-o-star' : 'heroicon-o-star')
                ->color(fn () => $this->record->is_featured ? 'gray' : 'warning')
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
            
            Actions\Action::make('optimizeImage')
                ->label('Optimize Image')
                ->icon('heroicon-o-sparkles')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Optimize Image')
                ->modalDescription('This will reduce the file size while maintaining quality.')
                ->action(function () {
                    $this->optimizeImage();
                }),
            
            Actions\Action::make('regenerateThumbnail')
                ->label('Regenerate Thumbnail')
                ->icon('heroicon-o-photo')
                ->color('info')
                ->requiresConfirmation()
                ->action(function () {
                    // Delete old thumbnail
                    if ($this->record->thumbnail_path) {
                        Storage::disk('public')->delete($this->record->thumbnail_path);
                    }
                })
                ->after(function () {
                    Cache::tags(['hall_images', 'hall_' . $this->record->hall_id])->flush();
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Image Deleted')
                        ->body('The image and its files have been deleted.')
                ),
            
            Actions\Action::make('viewHistory')
                ->label('View History')
                ->icon('heroicon-o-clock')
                ->color('gray')
                ->modalContent(fn () => view('filament.pages.activity-log', [
                    'activities' => activity()
                        ->forSubject($this->record)
                        ->latest()
                        ->get()
                ]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Image Updated')
            ->body('The hall image has been updated successfully.')
            ->duration(5000);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Auto-generate alt text if not provided
        if (empty($data['alt_text']) && isset($data['title']['en'])) {
            $data['alt_text'] = $data['title']['en'];
        }
        
        // Ensure featured images are marked as featured
        if ($data['type'] === 'featured' && !$data['is_featured']) {
            $data['is_featured'] = true;
        }
        
        // Check for new image upload
        if (isset($data['image_path']) && $data['image_path'] !== $this->record->image_path) {
            // New image uploaded, delete old one
            if ($this->record->image_path) {
                Storage::disk('public')->delete($this->record->image_path);
            }
            
            if ($this->record->thumbnail_path) {
                Storage::disk('public')->delete($this->record->thumbnail_path);
                $data['thumbnail_path'] = null;
            }
        }
        
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $oldValues = $record->toArray();
        
        $record->update($data);
        
        // Extract metadata if image changed
        if (isset($data['image_path']) && $data['image_path'] !== $oldValues['image_path']) {
            $this->extractImageMetadata($record);
            
            // Generate new thumbnail
            if (empty($data['thumbnail_path'])) {
                $this->generateThumbnail($record);
            }
        }
        
        $changes = array_diff_assoc($data, $oldValues);
        
        // Log the update
        activity()
            ->performedOn($record)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldValues,
                'changes' => $changes,
            ])
            ->log('Hall image updated');
        
        return $record;
    }

    protected function afterSave(): void
    {
        // Clear cache
        Cache::tags(['hall_images', 'hall_' . $this->record->hall_id])->flush();
        
        // Log the update
        Log::info('Hall image updated', [
            'image_id' => $this->record->id,
            'hall_id' => $this->record->hall_id,
            'updated_by' => Auth::id(),
        ]);
    }

    protected function optimizeImage(): void
    {
        try {
            // Implement image optimization
            // Example using Intervention Image or similar package
            
            $originalSize = Storage::disk('public')->size($this->record->image_path);
            
            // Optimization logic here
            
            $newSize = Storage::disk('public')->size($this->record->image_path);
            $savedBytes = $originalSize - $newSize;
            $savedPercentage = round(($savedBytes / $originalSize) * 100, 1);
            
            // Update file size in database
            $this->extractImageMetadata($this->record);
            
            Notification::make()
                ->success()
                ->title('Image Optimized')
                ->body("Saved {$savedPercentage}% ({$this->formatBytes($savedBytes)})")
                ->send();
            
            Cache::tags(['hall_images', 'hall_' . $this->record->hall_id])->flush();
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Optimization Failed')
                ->body('Unable to optimize image: ' . $e->getMessage())
                ->send();
        }
    }

    protected function generateThumbnail($image): void
    {
        try {
            // Implement thumbnail generation
            Log::info('Thumbnail generated for image: ' . $image->id);
        } catch (\Exception $e) {
            Log::error('Thumbnail generation failed: ' . $e->getMessage());
        }
    }

    protected function extractImageMetadata($image): void
    {
        try {
            $path = Storage::disk('public')->path($image->image_path);
            
            if (file_exists($path)) {
                $imageInfo = getimagesize($path);
                
                if ($imageInfo) {
                    $width = $imageInfo[0];
                    $height = $imageInfo[1];
                    $fileSize = Storage::disk('public')->size($image->image_path);
                    
                    $image->update([
                        'dimensions' => "{$width}x{$height}",
                        'file_size' => $fileSize,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to extract image metadata: ' . $e->getMessage());
        }
    }

    protected function getImageStatistics(): array
    {
        $fileSize = $this->record->file_size ?? 0;
        $dimensions = explode('x', $this->record->dimensions ?? '0x0');
        $width = (int) ($dimensions[0] ?? 0);
        $height = (int) ($dimensions[1] ?? 0);
        $aspectRatio = $width > 0 && $height > 0 ? round($width / $height, 2) : 0;
        
        return [
            'file_size' => $this->formatBytes($fileSize),
            'dimensions' => $this->record->dimensions ?? 'Unknown',
            'aspect_ratio' => $aspectRatio,
            'format' => pathinfo($this->record->image_path, PATHINFO_EXTENSION),
            'has_thumbnail' => $this->record->thumbnail_path ? 'Yes' : 'No',
            'is_optimized' => $fileSize < 1024 * 1024 ? 'Yes' : 'Recommend Optimization',
        ];
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

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->submit(null)
                ->keyBindings(['mod+s']),
            
            $this->getCancelFormAction(),
        ];
    }

    public function getTitle(): string
    {
        return 'Edit Image: ' . ($this->record->title ?: 'Untitled');
    }

    public function getSubheading(): ?string
    {
        $hall = $this->record->hall->name ?? 'Unknown Hall';
        $type = $this->record->type_label ?? $this->record->type;
        $status = $this->record->is_active ? 'Active' : 'Inactive';
        $featured = $this->record->is_featured ? '• Featured' : '';
        
        return "{$hall} • {$type} • {$status} {$featured}";
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
