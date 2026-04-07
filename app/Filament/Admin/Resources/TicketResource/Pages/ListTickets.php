<?php

namespace App\Filament\Admin\Resources\TicketResource\Pages;

use App\Filament\Admin\Resources\TicketResource;
use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\TicketPriority;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->label(__('ticket_admin.new_ticket')),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TicketResource\Widgets\TicketStatsOverview::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('ticket_admin.tab_all'))
                ->badge(Ticket::count())
                ->badgeColor('gray'),

            'open' => Tab::make(__('ticket_admin.tab_open'))
                ->icon('heroicon-o-envelope-open')
                ->modifyQueryUsing(fn (Builder $query) => $query->open())
                ->badge(Ticket::open()->count())
                ->badgeColor('info'),

            'my_tickets' => Tab::make(__('ticket_admin.tab_my_tickets'))
                ->icon('heroicon-o-user')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('assigned_to', Auth::id())
                )
                ->badge(Ticket::where('assigned_to', Auth::id())->count())
                ->badgeColor('success'),

            'unassigned' => Tab::make(__('ticket_admin.tab_unassigned'))
                ->icon('heroicon-o-inbox')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereNull('assigned_to')
                        ->where('status', '!=', TicketStatus::CLOSED->value)
                )
                ->badge(Ticket::whereNull('assigned_to')
                    ->where('status', '!=', TicketStatus::CLOSED->value)
                    ->count())
                ->badgeColor('warning'),

            'urgent' => Tab::make(__('ticket_admin.tab_urgent'))
                ->icon('heroicon-o-fire')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('priority', TicketPriority::URGENT->value)->open()
                )
                ->badge(Ticket::where('priority', TicketPriority::URGENT->value)->open()->count())
                ->badgeColor('danger'),

            'overdue' => Tab::make(__('ticket_admin.tab_overdue'))
                ->icon('heroicon-o-exclamation-triangle')
                ->modifyQueryUsing(fn (Builder $query) => $query->overdue())
                ->badge(Ticket::overdue()->count())
                ->badgeColor('danger'),

            'in_progress' => Tab::make(__('ticket_admin.tab_in_progress'))
                ->icon('heroicon-o-arrow-path')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status', TicketStatus::IN_PROGRESS->value)
                )
                ->badge(Ticket::where('status', TicketStatus::IN_PROGRESS->value)->count())
                ->badgeColor('primary'),

            'resolved' => Tab::make(__('ticket_admin.tab_resolved'))
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status', TicketStatus::RESOLVED->value)
                )
                ->badge(Ticket::where('status', TicketStatus::RESOLVED->value)->count())
                ->badgeColor('success'),

            'closed' => Tab::make(__('ticket_admin.tab_closed'))
                ->icon('heroicon-o-lock-closed')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status', TicketStatus::CLOSED->value)
                )
                ->badge(Ticket::where('status', TicketStatus::CLOSED->value)->count())
                ->badgeColor('gray'),
        ];
    }
}
