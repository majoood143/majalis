<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PromoCodeResource\Pages;

use App\Filament\Admin\Resources\PromoCodeResource;
use App\Models\PromoCode;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListPromoCodes extends ListRecords
{
    protected static string $resource = PromoCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Actions\Action::make('exportPromoCodes')
                ->label(__('promo.export_btn'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn () => $this->exportPromoCodes()),
        ];
    }

    protected function exportPromoCodes(): void
    {
        $codes = PromoCode::with('hall')->get();

        $filename = 'promo_codes_' . now()->format('Y_m_d_His') . '.csv';
        $path     = storage_path('app/public/exports/' . $filename);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $file = fopen($path, 'w');

        fputcsv($file, [
            __('promo.col_code'),
            __('promo.col_name'),
            __('promo.col_discount'),
            __('promo.col_hall'),
            __('promo.col_used'),
            __('promo.field_max_uses'),
            __('promo.field_valid_from'),
            __('promo.col_valid_until'),
            __('promo.col_active'),
        ]);

        foreach ($codes as $code) {
            fputcsv($file, [
                $code->code,
                $code->name,
                $code->discount_label,
                $code->hall ? $code->hall->getTranslation('name', 'en') : __('promo.all_halls'),
                $code->used_count,
                $code->max_uses ?? '∞',
                $code->valid_from?->format('Y-m-d H:i') ?? '',
                $code->valid_until?->format('Y-m-d H:i') ?? __('promo.no_expiry'),
                $code->is_active ? __('user.yes') : __('user.no'),
            ]);
        }

        fclose($file);

        Notification::make()
            ->success()
            ->title(__('promo.export_success_title'))
            ->body(__('promo.export_success_body', ['filename' => $filename]))
            ->actions([
                \Filament\Notifications\Actions\Action::make('download')
                    ->label(__('promo.export_download'))
                    ->url(asset('storage/exports/' . $filename))
                    ->openUrlInNewTab(),
            ])
            ->send();
    }
}
