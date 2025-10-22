<?php

namespace App\Filament\Admin\Resources\ReviewResource\Pages;

use App\Filament\Admin\Resources\ReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

class ListReviews extends ListRecords
{
    protected static string $resource = ReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('exportReviews')
                ->label('Export Reviews')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn() => $this->exportReviews()),

            Actions\Action::make('bulkApprove')
                ->label('Approve Pending')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    $count = \App\Models\Review::where('is_approved', false)->get()->each->approve()->count();
                    Notification::make()->success()->title("{$count} review(s) approved")->send();
                    $this->redirect(static::getUrl());
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Reviews')
                ->badge(fn() => \App\Models\Review::count()),

            'pending' => Tab::make('Pending Approval')
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_approved', false))
                ->badge(fn() => \App\Models\Review::where('is_approved', false)->count())
                ->badgeColor('warning'),

            'approved' => Tab::make('Approved')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_approved', true))
                ->badge(fn() => \App\Models\Review::where('is_approved', true)->count())
                ->badgeColor('success'),

            'featured' => Tab::make('Featured')
                ->icon('heroicon-o-star')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_featured', true))
                ->badge(fn() => \App\Models\Review::where('is_featured', true)->count())
                ->badgeColor('warning'),

            '5_stars' => Tab::make('5 Stars')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('rating', 5))
                ->badge(fn() => \App\Models\Review::where('rating', 5)->count())
                ->badgeColor('success'),

            'low_rated' => Tab::make('Low Rated (≤2)')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('rating', '<=', 2))
                ->badge(fn() => \App\Models\Review::where('rating', '<=', 2)->count())
                ->badgeColor('danger'),

            'with_response' => Tab::make('With Response')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('owner_response'))
                ->badge(fn() => \App\Models\Review::whereNotNull('owner_response')->count())
                ->badgeColor('info'),
        ];
    }

    protected function exportReviews(): void
    {
        $reviews = \App\Models\Review::with(['hall', 'user', 'booking'])->get();

        $filename = 'reviews_' . now()->format('Y_m_d_His') . '.csv';
        $path = storage_path('app/public/exports/' . $filename);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $file = fopen($path, 'w');

        fputcsv($file, [
            'ID',
            'Hall',
            'User',
            'Booking',
            'Rating',
            'Comment',
            'Cleanliness',
            'Service',
            'Value',
            'Location',
            'Approved',
            'Featured',
            'Owner Response',
            'Created At'
        ]);

        foreach ($reviews as $review) {
            fputcsv($file, [
                $review->id,
                $review->hall->getTranslation('name', 'en') ?? 'N/A',
                $review->user->name ?? 'N/A',
                $review->booking->booking_number ?? 'N/A',
                $review->rating,
                $review->comment ?? '',
                $review->cleanliness_rating ?? '',
                $review->service_rating ?? '',
                $review->value_rating ?? '',
                $review->location_rating ?? '',
                $review->is_approved ? 'Yes' : 'No',
                $review->is_featured ? 'Yes' : 'No',
                $review->owner_response ?? '',
                $review->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        fclose($file);

        Notification::make()
            ->success()
            ->title('Export Successful')
            ->actions([
                \Filament\Notifications\Actions\Action::make('download')
                    ->url(asset('storage/exports/' . $filename))
                    ->openUrlInNewTab(),
            ])
            ->send();
    }
}
