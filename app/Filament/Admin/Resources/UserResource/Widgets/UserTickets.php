<?php

namespace App\Filament\Admin\Resources\UserResource\Widgets;

use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class UserTickets extends BaseWidget
{
    public ?User $record = null;

    protected static ?string $heading = 'Support Tickets';

    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return Ticket::query()
            ->where('user_id', $this->record?->id)
            ->latest();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('ticket_number')
                    ->label('Ticket #')
                    ->searchable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('subject')
                    ->label('Subject')
                    ->searchable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state instanceof \BackedEnum ? $state->value : $state),

                Tables\Columns\TextColumn::make('priority')
                    ->label('Priority')
                    ->badge()
                    ->color(fn($state): string => match (true) {
                        $state === TicketPriority::URGENT || $state?->value === 'urgent' => 'danger',
                        $state === TicketPriority::HIGH || $state?->value === 'high' => 'warning',
                        $state === TicketPriority::MEDIUM || $state?->value === 'medium' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => $state instanceof \BackedEnum ? ucfirst($state->value) : ucfirst((string) $state)),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state): string => match (true) {
                        $state === TicketStatus::OPEN || $state?->value === 'open' => 'info',
                        $state === TicketStatus::IN_PROGRESS || $state?->value === 'in_progress' => 'primary',
                        $state === TicketStatus::RESOLVED || $state?->value === 'resolved' => 'success',
                        $state === TicketStatus::CLOSED || $state?->value === 'closed' => 'gray',
                        $state === TicketStatus::CANCELLED || $state?->value === 'cancelled' => 'danger',
                        $state === TicketStatus::ESCALATED || $state?->value === 'escalated' => 'danger',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn($state) => $state instanceof \BackedEnum
                        ? ucfirst(str_replace('_', ' ', $state->value))
                        : ucfirst(str_replace('_', ' ', (string) $state))),

                Tables\Columns\TextColumn::make('booking.booking_number')
                    ->label('Booking')
                    ->placeholder('—')
                    ->copyable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Opened')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading('No tickets')
            ->emptyStateDescription('This user has not submitted any support tickets.');
    }
}
