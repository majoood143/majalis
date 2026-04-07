<?php

namespace App\Filament\Admin\Resources\TicketResource\Widgets;

use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\TicketPriority;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class TicketStatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $totalOpen   = Ticket::open()->count();
        $myTickets   = Ticket::where('assigned_to', Auth::id())->open()->count();
        $unassigned  = Ticket::whereNull('assigned_to')
            ->where('status', '!=', TicketStatus::CLOSED->value)
            ->count();
        $overdue     = Ticket::overdue()->count();
        $urgent      = Ticket::where('priority', TicketPriority::URGENT->value)->open()->count();
        $resolved    = Ticket::where('status', TicketStatus::RESOLVED->value)->count();

        $avgResponseTime = Ticket::whereNotNull('first_response_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, first_response_at)) as avg_hours')
            ->value('avg_hours');

        $avgResolutionTime = Ticket::whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
            ->value('avg_hours');

        $thisWeekCount = Ticket::whereBetween('created_at', [
            now()->startOfWeek(), now()->endOfWeek()
        ])->count();

        $lastWeekCount = Ticket::whereBetween('created_at', [
            now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()
        ])->count();

        $weeklyChange = $lastWeekCount > 0
            ? (($thisWeekCount - $lastWeekCount) / $lastWeekCount) * 100
            : 0;

        return [
            Stat::make(__('ticket_admin.stat_open_tickets'), $totalOpen)
                ->description(__('ticket_admin.stat_open_tickets_desc'))
                ->descriptionIcon('heroicon-o-envelope-open')
                ->color($totalOpen > 20 ? 'danger' : ($totalOpen > 10 ? 'warning' : 'success'))
                ->chart($this->getLastSevenDaysChart(TicketStatus::OPEN)),

            Stat::make(__('ticket_admin.stat_my_tickets'), $myTickets)
                ->description(__('ticket_admin.stat_my_tickets_desc'))
                ->descriptionIcon('heroicon-o-user')
                ->color($myTickets > 10 ? 'danger' : 'primary')
                ->url(route('filament.admin.resources.tickets.index', ['activeTab' => 'my_tickets'])),

            Stat::make(__('ticket_admin.stat_unassigned'), $unassigned)
                ->description(__('ticket_admin.stat_unassigned_desc'))
                ->descriptionIcon('heroicon-o-inbox')
                ->color($unassigned > 5 ? 'warning' : 'gray')
                ->url(route('filament.admin.resources.tickets.index', ['activeTab' => 'unassigned'])),

            Stat::make(__('ticket_admin.stat_overdue'), $overdue)
                ->description(__('ticket_admin.stat_overdue_desc'))
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($overdue > 0 ? 'danger' : 'success')
                ->url(route('filament.admin.resources.tickets.index', ['activeTab' => 'overdue'])),

            Stat::make(__('ticket_admin.stat_urgent'), $urgent)
                ->description(__('ticket_admin.stat_urgent_desc'))
                ->descriptionIcon('heroicon-o-fire')
                ->color($urgent > 0 ? 'danger' : 'success')
                ->url(route('filament.admin.resources.tickets.index', ['activeTab' => 'urgent'])),

            Stat::make(__('ticket_admin.stat_resolved'), $resolved)
                ->description(__('ticket_admin.stat_resolved_desc'))
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->url(route('filament.admin.resources.tickets.index', ['activeTab' => 'resolved'])),

            Stat::make(__('ticket_admin.stat_avg_response'), $this->formatHours($avgResponseTime))
                ->description(__('ticket_admin.stat_avg_response_desc'))
                ->descriptionIcon('heroicon-o-clock')
                ->color($this->getResponseTimeColor($avgResponseTime)),

            Stat::make(__('ticket_admin.stat_avg_resolution'), $this->formatHours($avgResolutionTime))
                ->description(__('ticket_admin.stat_avg_resolution_desc'))
                ->descriptionIcon('heroicon-o-check-badge')
                ->color($this->getResolutionTimeColor($avgResolutionTime)),

            Stat::make(__('ticket_admin.stat_this_week'), $thisWeekCount)
                ->description(
                    abs(round($weeklyChange, 1)) . '% ' .
                    ($weeklyChange >= 0
                        ? __('ticket_admin.stat_increase')
                        : __('ticket_admin.stat_decrease'))
                )
                ->descriptionIcon($weeklyChange >= 0
                    ? 'heroicon-o-arrow-trending-up'
                    : 'heroicon-o-arrow-trending-down'
                )
                ->color($weeklyChange >= 0 ? 'warning' : 'success')
                ->chart($this->getLastSevenDaysChart()),
        ];
    }

    protected function formatHours(?float $hours): string
    {
        if (!$hours) return __('ticket_admin.stat_na');

        if ($hours < 1)  return round($hours * 60) . 'm';
        if ($hours < 24) return round($hours, 1) . 'h';

        $days           = floor($hours / 24);
        $remainingHours = round($hours % 24);

        return $days . 'd ' . ($remainingHours > 0 ? $remainingHours . 'h' : '');
    }

    protected function getResponseTimeColor(?float $hours): string
    {
        if (!$hours)      return 'gray';
        if ($hours <= 4)  return 'success';
        if ($hours <= 12) return 'warning';
        return 'danger';
    }

    protected function getResolutionTimeColor(?float $hours): string
    {
        if (!$hours)      return 'gray';
        if ($hours <= 24) return 'success';
        if ($hours <= 48) return 'warning';
        return 'danger';
    }

    protected function getLastSevenDaysChart(?TicketStatus $status = null): array
    {
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date  = now()->subDays($i)->startOfDay();
            $query = Ticket::whereDate('created_at', $date);

            if ($status) {
                $query->where('status', $status->value);
            }

            $data[] = $query->count();
        }

        return $data;
    }
}
