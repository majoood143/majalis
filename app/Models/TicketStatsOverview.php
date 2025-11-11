<?php

namespace App\Filament\Admin\Resources\TicketResource\Widgets;

use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\TicketPriority;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

/**
 * Ticket Stats Overview Widget
 * 
 * Displays key ticket statistics and metrics in a dashboard widget format.
 * Shows real-time data about ticket counts, response times, and performance.
 * 
 * @package App\Filament\Admin\Resources\TicketResource\Widgets
 * @version 1.0.0
 */
class TicketStatsOverview extends BaseWidget
{
    /**
     * Widget polling interval (auto-refresh every 30 seconds).
     *
     * @var string
     */
    protected static ?string $pollingInterval = '30s';

    /**
     * Define the statistics to display.
     * 
     * @return array
     */
    protected function getStats(): array
    {
        // Calculate statistics
        $totalOpen = Ticket::open()->count();
        $myTickets = Ticket::where('assigned_to', Auth::id())->open()->count();
        $unassigned = Ticket::whereNull('assigned_to')
            ->where('status', '!=', TicketStatus::CLOSED->value)
            ->count();
        $overdue = Ticket::overdue()->count();
        $urgent = Ticket::where('priority', TicketPriority::URGENT->value)
            ->open()
            ->count();
        $resolved = Ticket::where('status', TicketStatus::RESOLVED->value)->count();

        // Calculate average response time (in hours) for tickets with first response
        $avgResponseTime = Ticket::whereNotNull('first_response_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, first_response_at)) as avg_hours')
            ->value('avg_hours');

        // Calculate average resolution time (in hours)
        $avgResolutionTime = Ticket::whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
            ->value('avg_hours');

        // Calculate this week's tickets vs last week for trend
        $thisWeekCount = Ticket::whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->count();

        $lastWeekCount = Ticket::whereBetween('created_at', [
            now()->subWeek()->startOfWeek(),
            now()->subWeek()->endOfWeek()
        ])->count();

        $weeklyChange = $lastWeekCount > 0 
            ? (($thisWeekCount - $lastWeekCount) / $lastWeekCount) * 100 
            : 0;

        // Build statistics array
        return [
            // Open Tickets
            Stat::make('Open Tickets', $totalOpen)
                ->description('Total open tickets')
                ->descriptionIcon('heroicon-o-envelope-open')
                ->color($totalOpen > 20 ? 'danger' : ($totalOpen > 10 ? 'warning' : 'success'))
                ->chart($this->getLastSevenDaysChart(TicketStatus::OPEN)),

            // My Tickets
            Stat::make('My Tickets', $myTickets)
                ->description('Assigned to me')
                ->descriptionIcon('heroicon-o-user')
                ->color($myTickets > 10 ? 'danger' : 'primary')
                ->url(route('filament.admin.resources.tickets.index', ['activeTab' => 'my_tickets'])),

            // Unassigned Tickets
            Stat::make('Unassigned', $unassigned)
                ->description('Awaiting assignment')
                ->descriptionIcon('heroicon-o-inbox')
                ->color($unassigned > 5 ? 'warning' : 'gray')
                ->url(route('filament.admin.resources.tickets.index', ['activeTab' => 'unassigned'])),

            // Overdue Tickets
            Stat::make('Overdue', $overdue)
                ->description('Past due date')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($overdue > 0 ? 'danger' : 'success')
                ->url(route('filament.admin.resources.tickets.index', ['activeTab' => 'overdue'])),

            // Urgent Tickets
            Stat::make('Urgent', $urgent)
                ->description('High priority')
                ->descriptionIcon('heroicon-o-fire')
                ->color($urgent > 0 ? 'danger' : 'success')
                ->url(route('filament.admin.resources.tickets.index', ['activeTab' => 'urgent'])),

            // Resolved Tickets
            Stat::make('Resolved', $resolved)
                ->description('Awaiting confirmation')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->url(route('filament.admin.resources.tickets.index', ['activeTab' => 'resolved'])),

            // Average Response Time
            Stat::make('Avg. Response Time', $this->formatHours($avgResponseTime))
                ->description('Time to first response')
                ->descriptionIcon('heroicon-o-clock')
                ->color($this->getResponseTimeColor($avgResponseTime)),

            // Average Resolution Time
            Stat::make('Avg. Resolution Time', $this->formatHours($avgResolutionTime))
                ->description('Time to resolution')
                ->descriptionIcon('heroicon-o-check-badge')
                ->color($this->getResolutionTimeColor($avgResolutionTime)),

            // Weekly Trend
            Stat::make('This Week', $thisWeekCount)
                ->description(abs(round($weeklyChange, 1)) . '% ' . ($weeklyChange >= 0 ? 'increase' : 'decrease'))
                ->descriptionIcon($weeklyChange >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->color($weeklyChange >= 0 ? 'warning' : 'success')
                ->chart($this->getLastSevenDaysChart()),
        ];
    }

    /**
     * Format hours into human-readable format.
     * 
     * @param float|null $hours
     * @return string
     */
    protected function formatHours(?float $hours): string
    {
        if (!$hours) {
            return 'N/A';
        }

        if ($hours < 1) {
            return round($hours * 60) . 'm';
        }

        if ($hours < 24) {
            return round($hours, 1) . 'h';
        }

        $days = floor($hours / 24);
        $remainingHours = round($hours % 24);
        
        return $days . 'd ' . ($remainingHours > 0 ? $remainingHours . 'h' : '');
    }

    /**
     * Get color for response time based on performance.
     * 
     * @param float|null $hours
     * @return string
     */
    protected function getResponseTimeColor(?float $hours): string
    {
        if (!$hours) return 'gray';
        
        // Good: < 4 hours
        // Average: 4-12 hours
        // Poor: > 12 hours
        if ($hours <= 4) return 'success';
        if ($hours <= 12) return 'warning';
        return 'danger';
    }

    /**
     * Get color for resolution time based on performance.
     * 
     * @param float|null $hours
     * @return string
     */
    protected function getResolutionTimeColor(?float $hours): string
    {
        if (!$hours) return 'gray';
        
        // Good: < 24 hours
        // Average: 24-48 hours
        // Poor: > 48 hours
        if ($hours <= 24) return 'success';
        if ($hours <= 48) return 'warning';
        return 'danger';
    }

    /**
     * Get chart data for the last 7 days.
     * 
     * @param TicketStatus|null $status
     * @return array
     */
    protected function getLastSevenDaysChart(?TicketStatus $status = null): array
    {
        $data = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            
            $query = Ticket::whereDate('created_at', $date);
            
            if ($status) {
                $query->where('status', $status->value);
            }
            
            $data[] = $query->count();
        }
        
        return $data;
    }
}
