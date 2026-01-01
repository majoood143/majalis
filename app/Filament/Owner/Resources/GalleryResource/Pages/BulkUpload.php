<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\GalleryResource\Pages;

use App\Filament\Owner\Resources\GalleryResource;
use App\Models\Hall;
use App\Models\HallImage;
use Filament\Resources\Pages\Page;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

/**
 * BulkUpload Page for Owner Panel
 *
 * Upload multiple images at once to a hall gallery.
 * Uses Filament form components for simple integration.
 */
class BulkUpload extends Page implements HasForms
{
    use InteractsWithForms;

    /**
     * The resource this page belongs to.
     */
    protected static string $resource = GalleryResource::class;

    /**
     * The view for this page.
     */
    protected static string $view = 'filament.owner.resources.gallery-resource.pages.bulk-upload';

    /**
     * Form data.
     */
    public ?array $data = [];

    /**
     * Mount the page.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $halls = Hall::where('owner_id', $user?->id)->where('is_active', true)->get();

        // Pre-select first hall if only one
        $defaultHallId = null;
        if ($halls->count() === 1) {
            $defaultHallId = $halls->first()->id;
        }

        // Check for hall_id in URL
        if (request()->has('hall_id')) {
            $hallId = (int) request()->get('hall_id');
            $hall = Hall::find($hallId);
            if ($hall && $hall->owner_id === Auth::id()) {
                $defaultHallId = $hallId;
            }
        }

        $this->form->fill([
            'hall_id' => $defaultHallId,
            'type' => 'gallery',
        ]);
    }

    /**
     * Get the page title.
     */
    public function getTitle(): string
    {
        return 'Bulk Upload';
    }

    /**
     * Get the page heading.
     */
    public function getHeading(): string
    {
        return 'Bulk Image Upload';
    }

    /**
     * Get the subheading.
     */
    public function getSubheading(): ?string
    {
        return 'Upload multiple images at once';
    }

    /**
     * Get header actions.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back to Gallery')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn () => GalleryResource::getUrl('index')),
        ];
    }

    /**
     * Define the form.
     */
    public function form(Form $form): Form
    {
        $user = Auth::user();

        return $form
            ->schema([
                Forms\Components\Section::make('Upload Settings')
                    ->schema([
                        Forms\Components\Select::make('hall_id')
                            ->label('Hall')
                            ->options(
                                Hall::where('owner_id', $user?->id)
                                    ->where('is_active', true)
                                    ->get()
                                    ->mapWithKeys(fn ($hall) => [
                                        $hall->id => $hall->getTranslation('name', app()->getLocale())
                                    ])
                            )
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false),

                        Forms\Components\Select::make('type')
                            ->label('Image Type')
                            ->options([
                                'gallery' => 'Gallery',
                                'exterior' => 'Exterior',
                                'interior' => 'Interior',
                                'floor_plan' => 'Floor Plan',
                            ])
                            ->default('gallery')
                            ->required()
                            ->native(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Images')
                    ->schema([
                        Forms\Components\FileUpload::make('images')
                            ->label('Select Images')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->disk('public')
                            ->directory('halls/images')
                            ->visibility('public')
                            ->maxFiles(20)
                            ->maxSize(5120) // 5MB
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->helperText('Max 20 images, 5MB each. Formats: JPEG, PNG, WebP')
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    /**
     * Process the uploads.
     */
    public function upload(): void
    {
        $data = $this->form->getState();

        if (empty($data['hall_id'])) {
            Notification::make()
                ->warning()
                ->title('Please select a hall first')
                ->send();
            return;
        }

        if (empty($data['images'])) {
            Notification::make()
                ->warning()
                ->title('No images selected')
                ->send();
            return;
        }

        // Verify hall ownership
        $hall = Hall::find($data['hall_id']);
        if (!$hall || $hall->owner_id !== Auth::id()) {
            Notification::make()
                ->danger()
                ->title('Unauthorized')
                ->send();
            return;
        }

        $successCount = 0;
        $currentOrder = HallImage::where('hall_id', $data['hall_id'])->max('order') ?? 0;

        foreach ($data['images'] as $imagePath) {
            try {
                // Extract metadata
                $fullPath = Storage::disk('public')->path($imagePath);
                $imageInfo = @getimagesize($fullPath);

                // Create record
                HallImage::create([
                    'hall_id' => $data['hall_id'],
                    'image_path' => $imagePath,
                    'type' => $data['type'],
                    'is_active' => true,
                    'is_featured' => false,
                    'order' => ++$currentOrder,
                    'file_size' => Storage::disk('public')->size($imagePath),
                    'mime_type' => Storage::disk('public')->mimeType($imagePath),
                    'width' => $imageInfo[0] ?? null,
                    'height' => $imageInfo[1] ?? null,
                ]);

                $successCount++;
            } catch (\Exception $e) {
                Log::error('Bulk upload failed: ' . $e->getMessage());
            }
        }

        if ($successCount > 0) {
            Notification::make()
                ->success()
                ->title('Images Uploaded')
                ->body("{$successCount} image(s) uploaded successfully")
                ->send();

            // Reset form
            $this->form->fill([
                'hall_id' => $data['hall_id'],
                'type' => 'gallery',
                'images' => [],
            ]);
        }
    }
}
