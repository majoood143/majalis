<?php

namespace App\Filament\Admin\Resources\ReviewResource\Pages;

use App\Filament\Admin\Resources\ReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class ListReviews extends ListRecords
{
    protected static string $resource = ReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('exportReviews')
                ->label(__('review.actions.export'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn() => $this->exportReviews())
                ->requiresConfirmation()
                ->modalHeading(__('review.actions.export_modal_heading'))
                ->modalDescription(__('review.actions.export_modal_description'))
                ->modalSubmitActionLabel(__('review.actions.export')),

            Actions\Action::make('bulkApprove')
                ->label(__('review.actions.bulk_approve'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading(__('review.actions.bulk_approve_modal_heading'))
                ->modalDescription(__('review.actions.bulk_approve_modal_description'))
                ->action(function () {
                    DB::beginTransaction();
                    try {
                        $count = \App\Models\Review::where('is_approved', false)->get()->each->approve()->count();
                        DB::commit();
                        Notification::make()
                            ->success()
                            ->title(__('review.notifications.bulk_approve_success'))
                            ->body(__('review.notifications.bulk_approve_success_body', ['count' => $count]))
                            ->send();
                        $this->redirect(static::getUrl());
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Notification::make()
                            ->danger()
                            ->title(__('review.notifications.update_error'))
                            ->body($e->getMessage())
                            ->send();
                    }
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('review.tabs.all'))
                ->badge(fn() => \App\Models\Review::count()),

            'pending' => Tab::make(__('review.tabs.pending'))
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_approved', false))
                ->badge(fn() => \App\Models\Review::where('is_approved', false)->count())
                ->badgeColor('warning'),

            'approved' => Tab::make(__('review.tabs.approved'))
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_approved', true))
                ->badge(fn() => \App\Models\Review::where('is_approved', true)->count())
                ->badgeColor('success'),

            'featured' => Tab::make(__('review.tabs.featured'))
                ->icon('heroicon-o-star')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_featured', true))
                ->badge(fn() => \App\Models\Review::where('is_featured', true)->count())
                ->badgeColor('warning'),

            '5_stars' => Tab::make(__('review.tabs.5_stars'))
                ->modifyQueryUsing(fn(Builder $query) => $query->where('rating', 5))
                ->badge(fn() => \App\Models\Review::where('rating', 5)->count())
                ->badgeColor('success'),

            'low_rated' => Tab::make(__('review.tabs.low_rated'))
                ->modifyQueryUsing(fn(Builder $query) => $query->where('rating', '<=', 2))
                ->badge(fn() => \App\Models\Review::where('rating', '<=', 2)->count())
                ->badgeColor('danger'),

            'with_response' => Tab::make(__('review.tabs.with_response'))
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('owner_response'))
                ->badge(fn() => \App\Models\Review::whereNotNull('owner_response')->count())
                ->badgeColor('info'),
        ];
    }

    protected function exportReviews(): void
    {
        DB::beginTransaction();

        try {
            $reviews = \App\Models\Review::with(['hall', 'user', 'booking'])->get();

            $filename = 'reviews_' . now()->format('Y_m_d_His') . '.csv';
            $path = storage_path('app/public/exports/' . $filename);

            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }

            $file = fopen($path, 'w');

            // Add UTF-8 BOM for Excel compatibility
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                __('review.export.id'),
                __('review.export.hall'),
                __('review.export.user'),
                __('review.export.booking'),
                __('review.export.rating'),
                __('review.export.comment'),
                __('review.export.cleanliness'),
                __('review.export.service'),
                __('review.export.value'),
                __('review.export.location'),
                __('review.export.approved'),
                __('review.export.featured'),
                __('review.export.owner_response'),
                __('review.export.created_at')
            ]);

            foreach ($reviews as $review) {
                fputcsv($file, [
                    $review->id,
                    $review->hall->getTranslation('name', 'en') ?? __('review.n_a'),
                    $review->user->name ?? __('review.n_a'),
                    $review->booking->booking_number ?? __('review.n_a'),
                    $review->rating,
                    $review->comment ?? '',
                    $review->cleanliness_rating ?? '',
                    $review->service_rating ?? '',
                    $review->value_rating ?? '',
                    $review->location_rating ?? '',
                    $review->is_approved ? __('review.yes') : __('review.no'),
                    $review->is_featured ? __('review.yes') : __('review.no'),
                    $review->owner_response ?? '',
                    $review->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);

            DB::commit();

            Notification::make()
                ->success()
                ->title(__('review.notifications.export_success'))
                ->body(__('review.notifications.export_success_body'))
                ->actions([
                    \Filament\Notifications\Actions\Action::make('download')
                        ->label(__('review.actions.download'))
                        ->url(asset('storage/exports/' . $filename))
                        ->openUrlInNewTab(),
                ])
                ->send();

        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->danger()
                ->title(__('review.notifications.export_error'))
                ->body($e->getMessage())
                ->send();
        }
    }
}
