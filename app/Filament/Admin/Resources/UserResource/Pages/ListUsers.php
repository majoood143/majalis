<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Schemas\Components\Tabs\Tab;
use App\Models\User;
use App\Filament\Admin\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus')
                ->color('primary'),

            Action::make('exportUsers')
                ->label(__('user.export_users'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn() => $this->exportUsers()),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('user.tabs.all_users'))
                ->badge(fn() => User::count()),

            'admin' => Tab::make(__('user.tabs.administrators'))
                ->icon('heroicon-o-shield-check')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('role', 'admin'))
                ->badge(fn() => User::where('role', 'admin')->count())
                ->badgeColor('danger'),

            'hall_owners' => Tab::make(__('user.tabs.hall_owners'))
                ->icon('heroicon-o-building-storefront')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('role', 'hall_owner'))
                ->badge(fn() => User::where('role', 'hall_owner')->count())
                ->badgeColor('warning'),

            'customers' => Tab::make(__('user.tabs.customers'))
                ->icon('heroicon-o-user-group')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('role', 'customer'))
                ->badge(fn() => User::where('role', 'customer')->count())
                ->badgeColor('info'),

            'active' => Tab::make(__('user.tabs.active'))
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', true))
                ->badge(fn() => User::where('is_active', true)->count())
                ->badgeColor('success'),

            'inactive' => Tab::make(__('user.tabs.inactive'))
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', false))
                ->badge(fn() => User::where('is_active', false)->count())
                ->badgeColor('danger'),

            'verified' => Tab::make(__('user.tabs.email_verified'))
                ->icon('heroicon-o-check-badge')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('email_verified_at'))
                ->badge(fn() => User::whereNotNull('email_verified_at')->count())
                ->badgeColor('success'),

            'unverified' => Tab::make(__('user.tabs.unverified'))
                ->icon('heroicon-o-exclamation-triangle')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNull('email_verified_at'))
                ->badge(fn() => User::whereNull('email_verified_at')->count())
                ->badgeColor('warning'),
        ];
    }

    protected function exportUsers(): void
    {
        $users = User::all();

        $filename = 'users_' . now()->format('Y_m_d_His') . '.csv';
        $path = storage_path('app/public/exports/' . $filename);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $file = fopen($path, 'w');

        fputcsv($file, [
            __('user.export.id'),
            __('user.export.name'),
            __('user.export.email'),
            __('user.export.role'),
            __('user.export.phone'),
            __('user.export.email_verified'),
            __('user.export.active'),
            __('user.export.created_at'),
        ]);

        foreach ($users as $user) {
            fputcsv($file, [
                $user->id,
                $user->name,
                $user->email,
                ucfirst($user->role->value),
                //$user->role,
                $user->phone ?? '',
                $user->email_verified_at ? __('user.yes') : __('user.no'),
                $user->is_active ? __('user.yes') : __('user.no'),
                $user->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        fclose($file);

        Notification::make()
            ->success()
            ->title(__('user.export.success_title'))
            ->body(__('user.export.success_body', ['filename' => $filename]))
            ->actions([
                Action::make('download')
                    ->label(__('user.export.download'))
                    ->url(asset('storage/exports/' . $filename))
                    ->openUrlInNewTab(),
            ])
            ->send();
    }
}
