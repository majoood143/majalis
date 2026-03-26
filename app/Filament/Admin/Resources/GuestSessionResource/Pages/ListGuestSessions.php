<?php

namespace App\Filament\Admin\Resources\GuestSessionResource\Pages;

use App\Filament\Admin\Resources\GuestSessionResource;
use App\Models\GuestSession;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListGuestSessions extends ListRecords
{
    protected static string $resource = GuestSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('guest-session.tabs.all'))
                ->icon('heroicon-o-squares-2x2')
                ->badge(fn() => GuestSession::count()),

            'active' => Tab::make(__('guest-session.tabs.active'))
                ->icon('heroicon-o-bolt')
                ->modifyQueryUsing(fn(Builder $query) => $query
                    ->where('expires_at', '>', now())
                    ->whereNotIn('status', ['expired', 'cancelled', 'completed']))
                ->badge(fn() => GuestSession::active()->count())
                ->badgeColor('info'),

            'pending' => Tab::make(__('guest-session.tabs.pending'))
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending'))
                ->badge(fn() => GuestSession::where('status', 'pending')->count())
                ->badgeColor('warning'),

            'verified' => Tab::make(__('guest-session.tabs.verified'))
                ->icon('heroicon-o-check-badge')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'verified'))
                ->badge(fn() => GuestSession::where('status', 'verified')->count())
                ->badgeColor('info'),

            'completed' => Tab::make(__('guest-session.tabs.completed'))
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'completed'))
                ->badge(fn() => GuestSession::where('status', 'completed')->count())
                ->badgeColor('success'),

            'expired' => Tab::make(__('guest-session.tabs.expired'))
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'expired'))
                ->badge(fn() => GuestSession::where('status', 'expired')->count())
                ->badgeColor('gray'),

            'cancelled' => Tab::make(__('guest-session.tabs.cancelled'))
                ->icon('heroicon-o-no-symbol')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'cancelled'))
                ->badge(fn() => GuestSession::where('status', 'cancelled')->count())
                ->badgeColor('danger'),
        ];
    }
}
