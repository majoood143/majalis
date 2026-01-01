<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\GalleryResource\Pages;

use App\Filament\Owner\Resources\GalleryResource;
use App\Models\Hall;
use App\Models\HallImage;
use Filament\Resources\Pages\Page;
use Filament\Actions;
use Filament\Notifications\Notification;
use Livewire\Attributes\Computed;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * ManageGallery Page for Owner Panel
 *
 * Visual gallery management with drag-and-drop reordering.
 */
class ManageGallery2 extends Page
{
    /**
     * The resource this page belongs to.
     */
    protected static string $resource = GalleryResource::class;

    /**
     * The view for this page.
     */
    protected static string $view = 'filament.owner.resources.gallery-resource.pages.manage-gallery';

    /**
     * Selected hall ID.
     */
    public ?int $selectedHallId = null;

    /**
     * Filter by type.
     */
    public ?string $typeFilter = null;

    /**
     * Filter by status.
     */
    public ?string $statusFilter = null;

    /**
     * Mount the page.
     */
    public function mount(): void
    {
        $halls = $this->getOwnerHalls();

        // Pre-select first hall if available
        if ($halls->count() > 0) {
            $this->selectedHallId = $halls->first()->id;
        }

        // Check for hall_id in URL
        if (request()->has('hall_id')) {
            $hallId = (int) request()->get('hall_id');
            $hall = Hall::find($hallId);

            if ($hall && $hall->owner_id === Auth::id()) {
                $this->selectedHallId = $hallId;
            }
        }
    }

    /**
     * Get the page title.
     */
    public function getTitle(): string
    {
        return __('owner.gallery.manage.title') ?? 'Manage Gallery';
    }

    /**
     * Get the page heading.
     */
    public function getHeading(): string
    {
        return __('owner.gallery.manage.heading') ?? 'Visual Gallery Manager';
    }

    /**
     * Get the subheading.
     */
    public function getSubheading(): ?string
    {
        return __('owner.gallery.manage.subheading') ?? 'Drag to reorder, click to manage images';
    }

    /**
     * Get header actions.
     *
     * @return array<Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label(__('owner.gallery.actions.back_to_gallery') ?? 'Back to Gallery')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn () => GalleryResource::getUrl('index')),

            Actions\Action::make('bulk_upload')
                ->label(__('owner.gallery.actions.bulk_upload') ?? 'Bulk Upload')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->url(fn () => GalleryResource::getUrl('upload') .
                    ($this->selectedHallId ? '?hall_id=' . $this->selectedHallId : '')),
        ];
    }

    /**
     * Get owner's halls.
     */
    #[Computed]
    public function getOwnerHalls(): Collection
    {
        $user = Auth::user();

        return Hall::where('owner_id', $user?->id)
            ->withCount('images')
            ->orderBy('name->en')
            ->get();
    }

    /**
     * Get the selected hall.
     */
    #[Computed]
    public function selectedHall(): ?Hall
    {
        if (!$this->selectedHallId) {
            return null;
        }

        return Hall::find($this->selectedHallId);
    }

    /**
     * Get images for the selected hall.
     */
    #[Computed]
    public function hallImages(): Collection
    {
        if (!$this->selectedHallId) {
            return collect();
        }

        $query = HallImage::where('hall_id', $this->selectedHallId)
            ->orderBy('order')
            ->orderBy('created_at', 'desc');

        // Apply type filter
        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }

        // Apply status filter
        if ($this->statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($this->statusFilter === 'inactive') {
            $query->where('is_active', false);
        } elseif ($this->statusFilter === 'featured') {
            $query->where('is_featured', true);
        }

        return $query->get();
    }

    /**
     * Get gallery statistics.
     */
    #[Computed]
    public function galleryStats(): array
    {
        if (!$this->selectedHallId) {
            return [
                'total' => 0,
                'active' => 0,
                'featured' => 0,
                'inactive' => 0,
            ];
        }

        return [
            'total' => HallImage::where('hall_id', $this->selectedHallId)->count(),
            'active' => HallImage::where('hall_id', $this->selectedHallId)->where('is_active', true)->count(),
            'featured' => HallImage::where('hall_id', $this->selectedHallId)->where('is_featured', true)->count(),
            'inactive' => HallImage::where('hall_id', $this->selectedHallId)->where('is_active', false)->count(),
        ];
    }

    /**
     * Set selected hall.
     */
    public function setHall(?int $hallId): void
    {
        $this->selectedHallId = $hallId;
        unset($this->selectedHall);
        unset($this->hallImages);
        unset($this->galleryStats);
    }

    /**
     * Set type filter.
     */
    public function setTypeFilter(?string $type): void
    {
        $this->typeFilter = $type;
        unset($this->hallImages);
    }

    /**
     * Set status filter.
     */
    public function setStatusFilter(?string $status): void
    {
        $this->statusFilter = $status;
        unset($this->hallImages);
    }

    /**
     * Toggle image featured status.
     */
    public function toggleFeatured(int $imageId): void
    {
        $image = HallImage::find($imageId);

        if (!$image || $image->hall->owner_id !== Auth::id()) {
            return;
        }

        $image->update(['is_featured' => !$image->is_featured]);

        unset($this->hallImages);
        unset($this->galleryStats);

        Notification::make()
            ->success()
            ->title($image->is_featured ? 'Marked as Featured' : 'Removed from Featured')
            ->duration(2000)
            ->send();
    }

    /**
     * Toggle image active status.
     */
    public function toggleActive(int $imageId): void
    {
        $image = HallImage::find($imageId);

        if (!$image || $image->hall->owner_id !== Auth::id()) {
            return;
        }

        $image->update(['is_active' => !$image->is_active]);

        unset($this->hallImages);
        unset($this->galleryStats);

        Notification::make()
            ->success()
            ->title($image->is_active ? 'Image Activated' : 'Image Deactivated')
            ->duration(2000)
            ->send();
    }

    /**
     * Delete an image.
     */
    public function deleteImage(int $imageId): void
    {
        $image = HallImage::find($imageId);

        if (!$image || $image->hall->owner_id !== Auth::id()) {
            return;
        }

        // Delete files
        if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }
        if ($image->thumbnail_path && Storage::disk('public')->exists($image->thumbnail_path)) {
            Storage::disk('public')->delete($image->thumbnail_path);
        }

        $image->delete();

        unset($this->hallImages);
        unset($this->galleryStats);

        Notification::make()
            ->success()
            ->title('Image Deleted')
            ->duration(2000)
            ->send();
    }

    /**
     * Update image order after drag-and-drop.
     */
    public function updateOrder(array $orderedIds): void
    {
        foreach ($orderedIds as $index => $imageId) {
            $image = HallImage::find($imageId);

            if ($image && $image->hall->owner_id === Auth::id()) {
                $image->update(['order' => $index]);
            }
        }

        unset($this->hallImages);

        Notification::make()
            ->success()
            ->title('Order Updated')
            ->duration(2000)
            ->send();
    }

    /**
     * Set as hall featured image.
     */
    public function setAsFeaturedImage(int $imageId): void
    {
        $image = HallImage::find($imageId);

        if (!$image || $image->hall->owner_id !== Auth::id()) {
            return;
        }

        // Update hall's featured_image
        $image->hall->update(['featured_image' => $image->image_path]);

        // Mark this image as featured and unmark others
        HallImage::where('hall_id', $image->hall_id)
            ->where('id', '!=', $imageId)
            ->update(['is_featured' => false]);

        $image->update(['is_featured' => true]);

        unset($this->hallImages);
        unset($this->galleryStats);

        Notification::make()
            ->success()
            ->title('Hall Cover Set')
            ->body('This image is now the hall\'s main cover image')
            ->send();
    }

    /**
     * Change image type.
     */
    public function changeType(int $imageId, string $type): void
    {
        $image = HallImage::find($imageId);

        if (!$image || $image->hall->owner_id !== Auth::id()) {
            return;
        }

        $image->update(['type' => $type]);

        unset($this->hallImages);

        Notification::make()
            ->success()
            ->title('Image type changed')
            ->duration(2000)
            ->send();
    }
}
