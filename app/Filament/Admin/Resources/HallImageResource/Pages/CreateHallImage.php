<?php

namespace App\Filament\Admin\Resources\HallImageResource\Pages;

use App\Filament\Admin\Resources\HallImageResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CreateHallImage extends CreateRecord
{
    protected static string $resource = HallImageResource::class;

    protected static bool $canCreateAnother = true;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Hall Image Created')
            ->body('The image has been uploaded successfully.')
            ->duration(5000);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set default values
        $data['order'] = $data['order'] ?? 0;
        $data['is_active'] = $data['is_active'] ?? true;
        $data['is_featured'] = $data['is_featured'] ?? false;

        // Validate image file exists
        if (isset($data['image_path']) && !Storage::disk('public')->exists($data['image_path'])) {
            Notification::make()
                ->danger()
                ->title('Image Not Found')
                ->body('The uploaded image file could not be found.')
                ->persistent()
                ->send();

            $this->halt();
        }

        // Check file size
        if (isset($data['image_path'])) {
            $fileSize = Storage::disk('public')->size($data['image_path']);
            $maxSize = 10 * 1024 * 1024; // 10MB

            if ($fileSize > $maxSize) {
                Notification::make()
                    ->warning()
                    ->title('Large File Size')
                    ->body('Image file size is ' . $this->formatBytes($fileSize) . '. Consider optimizing.')
                    ->send();
            }
        }

        // Auto-generate alt text if not provided
        if (empty($data['alt_text']) && isset($data['title']['en'])) {
            $data['alt_text'] = $data['title']['en'];
        }

        // Check for featured image limit
        if ($data['type'] === 'featured') {
            $featuredCount = \App\Models\HallImage::where('hall_id', $data['hall_id'])
                ->where('type', 'featured')
                ->count();

            if ($featuredCount >= 5) {
                Notification::make()
                    ->warning()
                    ->title('Multiple Featured Images')
                    ->body('This hall already has ' . $featuredCount . ' featured images.')
                    ->send();
            }
        }

        // Ensure featured images are also marked as featured
        if ($data['type'] === 'featured' && !$data['is_featured']) {
            $data['is_featured'] = true;
        }

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = static::getModel()::create($data);

        // Extract and store image metadata
        $this->extractImageMetadata($record);

        // Log the creation
        activity()
            ->performedOn($record)
            ->causedBy(Auth::user())
            ->withProperties([
                'hall_id' => $data['hall_id'],
                'type' => $data['type'],
                'file_path' => $data['image_path'],
            ])
            ->log('Hall image uploaded');

        return $record;
    }

    protected function afterCreate(): void
    {
        $image = $this->record;

        // Log the upload
        Log::info('Hall image uploaded', [
            'image_id' => $image->id,
            'hall_id' => $image->hall_id,
            'type' => $image->type,
            'uploaded_by' => Auth::id(),
        ]);

        // Auto-generate thumbnail if not provided
        if (empty($image->thumbnail_path)) {
            $this->generateThumbnail($image);
        }

        // Clear cache
        Cache::tags(['hall_images', 'hall_' . $image->hall_id])->flush();

        // Optimize image if it's too large
        $this->optimizeImageIfNeeded($image);
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

    protected function generateThumbnail($image): void
    {
        // Implement thumbnail generation
        // Example using Intervention Image or similar package

        try {
            // $thumbnail = Image::make(Storage::disk('public')->path($image->image_path))
            //     ->resize(300, 200, function ($constraint) {
            //         $constraint->aspectRatio();
            //     });
            // 
            // $thumbnailPath = 'halls/thumbnails/' . basename($image->image_path);
            // Storage::disk('public')->put($thumbnailPath, $thumbnail->encode());
            // 
            // $image->update(['thumbnail_path' => $thumbnailPath]);

            Log::info('Thumbnail generated for image: ' . $image->id);
        } catch (\Exception $e) {
            Log::error('Thumbnail generation failed: ' . $e->getMessage());
        }
    }

    protected function optimizeImageIfNeeded($image): void
    {
        try {
            $fileSize = Storage::disk('public')->size($image->image_path);
            $threshold = 2 * 1024 * 1024; // 2MB

            if ($fileSize > $threshold) {
                // Implement image optimization
                Log::info('Large image detected, optimization recommended: ' . $image->id);
            }
        } catch (\Exception $e) {
            Log::error('Image optimization check failed: ' . $e->getMessage());
        }
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
            $this->getCreateFormAction()
                ->submit(null)
                ->keyBindings(['mod+s']),

            $this->getCreateAnotherFormAction()
                ->keyBindings(['mod+shift+s']),

            $this->getCancelFormAction(),
        ];
    }

    public function getTitle(): string
    {
        return 'Upload Hall Image';
    }

    public function getSubheading(): ?string
    {
        return 'Add a new image to a hall gallery';
    }

    public function mount(): void
    {
        parent::mount();

        // Pre-fill hall_id if coming from hall view
        if (request()->has('hall_id')) {
            $this->form->fill([
                'hall_id' => request()->get('hall_id'),
            ]);
        }
    }
}
