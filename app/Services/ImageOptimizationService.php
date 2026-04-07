<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageOptimizationService
{
    private const MAX_WIDTH   = 1920;
    private const MAX_HEIGHT  = 1080;
    private const JPEG_QUALITY = 82;
    private const WEBP_QUALITY = 82;
    private const THUMB_WIDTH  = 400;
    private const THUMB_HEIGHT = 300;
    private const THUMB_QUALITY = 75;

    private ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Compress and resize an image in-place on the given disk.
     *
     * - JPEG/JPG: re-encoded at JPEG_QUALITY
     * - WebP:     re-encoded at WEBP_QUALITY
     * - PNG:      scaled down only (lossless)
     *
     * @return array{original_size:int,new_size:int,saved_bytes:int,saved_percent:float}|array{error:string}
     */
    public function compress(string $diskPath, string $disk = 'public'): array
    {
        $fullPath = Storage::disk($disk)->path($diskPath);

        if (!file_exists($fullPath)) {
            return ['error' => 'File not found: ' . $diskPath];
        }

        $originalSize = filesize($fullPath);
        $extension    = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        try {
            $image = $this->manager->read($fullPath);
            $image->scaleDown(self::MAX_WIDTH, self::MAX_HEIGHT);

            $content = match ($extension) {
                'jpg', 'jpeg' => (string) $image->toJpeg(self::JPEG_QUALITY),
                'webp'        => (string) $image->toWebp(self::WEBP_QUALITY),
                default       => (string) $image->toPng(), // PNG stays lossless
            };

            file_put_contents($fullPath, $content);
            clearstatcache(true, $fullPath);

            $newSize = filesize($fullPath);

            return [
                'original_size'  => $originalSize,
                'new_size'        => $newSize,
                'saved_bytes'    => max(0, $originalSize - $newSize),
                'saved_percent'  => $originalSize > 0
                    ? round((($originalSize - $newSize) / $originalSize) * 100, 1)
                    : 0.0,
            ];
        } catch (\Throwable $e) {
            Log::error('Image compression failed', ['path' => $diskPath, 'error' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Compress multiple images in-place (e.g. a gallery array of disk paths).
     * Skips nulls and empty strings silently.
     */
    public function compressMany(array $diskPaths, string $disk = 'public'): void
    {
        foreach ($diskPaths as $path) {
            if (!empty($path)) {
                $this->compress($path, $disk);
            }
        }
    }

    /**
     * Generate a thumbnail from a source image and store it at $thumbnailDiskPath.
     * Uses cover (crop-to-fill) so thumbnails have exact dimensions.
     *
     * @return bool  True on success, false on failure.
     */
    public function generateThumbnail(
        string $sourceDiskPath,
        string $thumbnailDiskPath,
        string $disk = 'public'
    ): bool {
        $sourcePath = Storage::disk($disk)->path($sourceDiskPath);

        if (!file_exists($sourcePath)) {
            return false;
        }

        try {
            $content = (string) $this->manager
                ->read($sourcePath)
                ->cover(self::THUMB_WIDTH, self::THUMB_HEIGHT)
                ->toJpeg(self::THUMB_QUALITY);

            Storage::disk($disk)->put($thumbnailDiskPath, $content);
            return true;
        } catch (\Throwable $e) {
            Log::error('Thumbnail generation failed', [
                'source' => $sourceDiskPath,
                'error'  => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Human-readable file size string.
     */
    public function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $pow   = $bytes > 0 ? (int) floor(log($bytes) / log(1024)) : 0;
        $pow   = min($pow, count($units) - 1);

        return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
    }
}
