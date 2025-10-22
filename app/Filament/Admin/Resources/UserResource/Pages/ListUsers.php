<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->color('primary'),

            Actions\Action::make('exportUsers')
                ->label('Export Users')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn() => $this->exportUsers()),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Users')
                ->badge(fn() => \App\Models\User::count()),

            'admin' => Tab::make('Administrators')
                ->icon('heroicon-o-shield-check')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('role', 'admin'))
                ->badge(fn() => \App\Models\User::where('role', 'admin')->count())
                ->badgeColor('danger'),

            'hall_owners' => Tab::make('Hall Owners')
                ->icon('heroicon-o-building-storefront')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('role', 'hall_owner'))
                ->badge(fn() => \App\Models\User::where('role', 'hall_owner')->count())
                ->badgeColor('warning'),

            'customers' => Tab::make('Customers')
                ->icon('heroicon-o-user-group')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('role', 'customer'))
                ->badge(fn() => \App\Models\User::where('role', 'customer')->count())
                ->badgeColor('info'),

            'active' => Tab::make('Active')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', true))
                ->badge(fn() => \App\Models\User::where('is_active', true)->count())
                ->badgeColor('success'),

            'inactive' => Tab::make('Inactive')
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', false))
                ->badge(fn() => \App\Models\User::where('is_active', false)->count())
                ->badgeColor('danger'),

            'verified' => Tab::make('Email Verified')
                ->icon('heroicon-o-check-badge')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('email_verified_at'))
                ->badge(fn() => \App\Models\User::whereNotNull('email_verified_at')->count())
                ->badgeColor('success'),

            'unverified' => Tab::make('Unverified')
                ->icon('heroicon-o-exclamation-triangle')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNull('email_verified_at'))
                ->badge(fn() => \App\Models\User::whereNull('email_verified_at')->count())
                ->badgeColor('warning'),
        ];
    }

    protected function exportUsers(): void
    {
        $users = \App\Models\User::all();

        $filename = 'users_' . now()->format('Y_m_d_His') . '.csv';
        $path = storage_path('app/public/exports/' . $filename);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $file = fopen($path, 'w');

        fputcsv($file, ['ID', 'Name', 'Email', 'Role', 'Phone', 'Email Verified', 'Active', 'Created At']);

        foreach ($users as $user) {
            fputcsv($file, [
                $user->id,
                $user->name,
                $user->email,
                ucfirst($user->role),
                $user->phone ?? '',
                $user->email_verified_at ? 'Yes' : 'No',
                $user->is_active ? 'Yes' : 'No',
                $user->created_at->format('Y-m-d H:i:s'),
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
