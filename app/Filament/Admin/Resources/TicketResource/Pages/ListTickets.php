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

/**
 * List Tickets Page
 * 
 * Displays a comprehensive list of all tickets with filtering, searching, and statistics.
 * Includes dashboard widgets showing key metrics and tabbed navigation for quick filtering.
 * 
 * @package App\Filament\Admin\Resources\TicketResource\Pages
 * @version 1.0.0
 */
class ListTickets extends ListRecords
{
    /**
     * The resource this page belongs to.
     *
     * @var string
     */
    protected static string $resource = TicketResource::class;

    /**
     * Get the header actions for the page.
     * 
     * @return array
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->label('New Ticket'),
        ];
    }

    /**
     * Get the header widgets for the page.
     * These widgets display key statistics about tickets.
     * 
     * @return array
     */
    protected function getHeaderWidgets(): array
    {
        return [
            TicketResource\Widgets\TicketStatsOverview::class,
        ];
    }

    /**
     * Define tabs for quick filtering of tickets.
     * 
     * @return array
     */
    public function getTabs(): array
    {
        return [
            // All tickets
            'all' => Tab::make('All Tickets')
                ->badge(Ticket::count())
                ->badgeColor('gray'),

            // Open tickets (not closed or cancelled)
            'open' => Tab::make('Open')
                ->icon('heroicon-o-envelope-open')
                ->modifyQueryUsing(fn (Builder $query) => $query->open())
                ->badge(Ticket::open()->count())
                ->badgeColor('info'),

            // My tickets (assigned to current user)
            'my_tickets' => Tab::make('My Tickets')
                ->icon('heroicon-o-user')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('assigned_to', Auth::id())
                )
                ->badge(Ticket::where('assigned_to', Auth::id())->count())
                ->badgeColor('success'),

            // Unassigned tickets
            'unassigned' => Tab::make('Unassigned')
                ->icon('heroicon-o-inbox')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->whereNull('assigned_to')
                        ->where('status', '!=', TicketStatus::CLOSED->value)
                )
                ->badge(Ticket::whereNull('assigned_to')
                    ->where('status', '!=', TicketStatus::CLOSED->value)
                    ->count())
                ->badgeColor('warning'),

            // Urgent tickets
            'urgent' => Tab::make('Urgent')
                ->icon('heroicon-o-fire')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('priority', TicketPriority::URGENT->value)
                        ->open()
                )
                ->badge(Ticket::where('priority', TicketPriority::URGENT->value)
                    ->open()
                    ->count())
                ->badgeColor('danger'),

            // Overdue tickets
            'overdue' => Tab::make('Overdue')
                ->icon('heroicon-o-exclamation-triangle')
                ->modifyQueryUsing(fn (Builder $query) => $query->overdue())
                ->badge(Ticket::overdue()->count())
                ->badgeColor('danger'),

            // In progress
            'in_progress' => Tab::make('In Progress')
                ->icon('heroicon-o-arrow-path')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('status', TicketStatus::IN_PROGRESS->value)
                )
                ->badge(Ticket::where('status', TicketStatus::IN_PROGRESS->value)->count())
                ->badgeColor('primary'),

            // Resolved tickets
            'resolved' => Tab::make('Resolved')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('status', TicketStatus::RESOLVED->value)
                )
                ->badge(Ticket::where('status', TicketStatus::RESOLVED->value)->count())
                ->badgeColor('success'),

            // Closed tickets
            'closed' => Tab::make('Closed')
                ->icon('heroicon-o-lock-closed')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('status', TicketStatus::CLOSED->value)
                )
                ->badge(Ticket::where('status', TicketStatus::CLOSED->value)->count())
                ->badgeColor('gray'),
        ];
    }
}
