<?php

namespace App\Filament\Admin\Resources\HallImageResource\Pages;

use App\Filament\Admin\Resources\HallImageResource;
use App\Services\ImageOptimizationService;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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

        Log::info('Hall image uploaded', [
            'image_id' => $image->id,
            'hall_id'  => $image->hall_id,
            'type'     => $image->type,
            'uploaded_by' => Auth::id(),
        ]);

        // Compress the main image before anything else
        $optimizer = app(ImageOptimizationService::class);
        $result    = $optimizer->compress($image->image_path);

        if (isset($result['saved_bytes']) && $result['saved_bytes'] > 0) {
            Log::info('Image compressed', [
                'image_id'      => $image->id,
                'saved_bytes'   => $result['saved_bytes'],
                'saved_percent' => $result['saved_percent'],
            ]);
        }

        // Auto-generate thumbnail if not provided
        if (empty($image->thumbnail_path)) {
            $this->generateThumbnail($image);
        }

        // Refresh file_size after compression
        $this->extractImageMetadata($image);
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
                        'width' => $width,
                        'height' => $height,
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
        try {
            $thumbnailPath = 'halls/thumbnails/' . pathinfo($image->image_path, PATHINFO_FILENAME) . '.jpg';
            $success = app(ImageOptimizationService::class)
                ->generateThumbnail($image->image_path, $thumbnailPath);

            if ($success) {
                $image->update(['thumbnail_path' => $thumbnailPath]);
                Log::info('Thumbnail generated for image: ' . $image->id);
            }
        } catch (\Exception $e) {
            Log::error('Thumbnail generation failed: ' . $e->getMessage());
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
                //->submit(null)
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
