<?php

declare(strict_types=1);

/**
 * ViewExpense Page
 * 
 * Displays expense details with booking profitability if linked.
 * 
 * @package App\Filament\Owner\Resources\ExpenseResource\Pages
 * @author  Majalis Development Team
 * @version 1.0.0
 */

namespace App\Filament\Owner\Resources\ExpenseResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use App\Filament\Owner\Resources\ExpenseResource;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

/**
 * ViewExpense Page Class
 */
class ViewExpense extends ViewRecord
{
    /**
     * The resource this page belongs to.
     *
     * @var string
     */
    protected static string $resource = ExpenseResource::class;

    /**
     * Get the header actions for this page.
     *
     * @return array
     */
    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }

    /**
     * Define the infolist schema for viewing expense details.
     *
     * @param Schema $infolist
     * @return Schema
     */
    public function infolist(Schema $schema): Schema
    {
        return $infolist
            ->schema([
                // Expense Information Section
                Section::make(fn() => app()->getLocale() === 'ar' ? 'معلومات المصروف' : 'Expense Information')
                    ->schema([
                        TextEntry::make('expense_number')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'رقم المصروف' : 'Expense Number')
                            ->badge()
                            ->color('primary'),

                        TextEntry::make('expense_type')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'النوع' : 'Type')
                            ->badge(),

                        TextEntry::make('status')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'الحالة' : 'Status')
                            ->badge(),

                        TextEntry::make('title')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'العنوان' : 'Title')
                            ->formatStateUsing(fn($record) => $record->getTranslation('title', app()->getLocale())),

                        TextEntry::make('description')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'الوصف' : 'Description')
                            ->formatStateUsing(fn($record) => $record->getTranslation('description', app()->getLocale()) ?? '-')
                            ->columnSpanFull(),

                        TextEntry::make('category.name')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'الفئة' : 'Category')
                            ->formatStateUsing(fn($record) => $record->category?->getTranslation('name', app()->getLocale()) ?? '-'),

                        TextEntry::make('hall.name')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'القاعة' : 'Hall')
                            ->formatStateUsing(fn($record) => $record->hall?->getTranslation('name', app()->getLocale()) ?? '-'),

                        TextEntry::make('booking.booking_number')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'الحجز' : 'Booking')
                            ->url(fn($record) => $record->booking_id 
                                ? route('filament.owner.resources.bookings.view', $record->booking_id) 
                                : null)
                            ->visible(fn($record) => $record->booking_id !== null),
                    ])
                    ->columns(3),

                // Financial Details Section
                Section::make(fn() => app()->getLocale() === 'ar' ? 'التفاصيل المالية' : 'Financial Details')
                    ->schema([
                        TextEntry::make('amount')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'المبلغ' : 'Amount')
                            ->money('OMR'),

                        TextEntry::make('tax_amount')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'الضريبة' : 'Tax')
                            ->money('OMR'),

                        TextEntry::make('total_amount')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'المجموع' : 'Total')
                            ->money('OMR')
                            ->weight('bold')
                            ->color('success'),

                        TextEntry::make('expense_date')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'تاريخ المصروف' : 'Expense Date')
                            ->date('Y-m-d'),

                        TextEntry::make('payment_method')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'طريقة الدفع' : 'Payment Method')
                            ->badge(),

                        TextEntry::make('payment_status')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'حالة الدفع' : 'Payment Status')
                            ->badge(),

                        TextEntry::make('payment_reference')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'رقم المرجع' : 'Payment Reference')
                            ->visible(fn($record) => $record->payment_reference !== null),

                        TextEntry::make('due_date')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'تاريخ الاستحقاق' : 'Due Date')
                            ->date('Y-m-d')
                            ->visible(fn($record) => $record->due_date !== null),

                        TextEntry::make('paid_at')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'تاريخ الدفع' : 'Paid At')
                            ->dateTime('Y-m-d H:i')
                            ->visible(fn($record) => $record->paid_at !== null),
                    ])
                    ->columns(3),

                // Booking Profitability Section (if linked to booking)
                Section::make(fn() => app()->getLocale() === 'ar' ? 'ربحية الحجز' : 'Booking Profitability')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('profitability.revenue')
                                    ->label(fn() => app()->getLocale() === 'ar' ? 'الإيرادات' : 'Revenue')
                                    ->state(fn($record) => $record->getBookingProfitability()['revenue'] ?? 0)
                                    ->money('OMR')
                                    ->color('success'),

                                TextEntry::make('profitability.expenses')
                                    ->label(fn() => app()->getLocale() === 'ar' ? 'المصروفات' : 'Expenses')
                                    ->state(fn($record) => $record->getBookingProfitability()['expenses'] ?? 0)
                                    ->money('OMR')
                                    ->color('danger'),

                                TextEntry::make('profitability.profit')
                                    ->label(fn() => app()->getLocale() === 'ar' ? 'الربح' : 'Profit')
                                    ->state(fn($record) => $record->getBookingProfitability()['profit'] ?? 0)
                                    ->money('OMR')
                                    ->color(fn($record) => ($record->getBookingProfitability()['profit'] ?? 0) >= 0 ? 'success' : 'danger'),

                                TextEntry::make('profitability.margin')
                                    ->label(fn() => app()->getLocale() === 'ar' ? 'هامش الربح' : 'Profit Margin')
                                    ->state(fn($record) => ($record->getBookingProfitability()['margin'] ?? 0) . '%')
                                    ->color(fn($record) => ($record->getBookingProfitability()['margin'] ?? 0) >= 0 ? 'success' : 'danger'),
                            ]),
                    ])
                    ->visible(fn($record) => $record->booking_id !== null),

                // Vendor Information Section
                Section::make(fn() => app()->getLocale() === 'ar' ? 'معلومات المورد' : 'Vendor Information')
                    ->schema([
                        TextEntry::make('vendor_name')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'اسم المورد' : 'Vendor Name'),

                        TextEntry::make('vendor_phone')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'هاتف المورد' : 'Vendor Phone'),

                        TextEntry::make('vendor_email')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'بريد المورد' : 'Vendor Email'),
                    ])
                    ->columns(3)
                    ->visible(fn($record) => $record->vendor_name !== null)
                    ->collapsed(),

                // Recurring Settings Section
                Section::make(fn() => app()->getLocale() === 'ar' ? 'إعدادات التكرار' : 'Recurring Settings')
                    ->schema([
                        TextEntry::make('recurring_frequency')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'التكرار' : 'Frequency')
                            ->badge(),

                        TextEntry::make('recurring_start_date')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'تاريخ البداية' : 'Start Date')
                            ->date('Y-m-d'),

                        TextEntry::make('recurring_end_date')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'تاريخ النهاية' : 'End Date')
                            ->date('Y-m-d')
                            ->default(fn() => app()->getLocale() === 'ar' ? 'بلا نهاية' : 'Indefinite'),

                        TextEntry::make('recurring_count')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'عدد التكرارات' : 'Recurrence Count'),
                    ])
                    ->columns(4)
                    ->visible(fn($record) => $record->is_recurring)
                    ->collapsed(),

                // Attachments Section
                Section::make(fn() => app()->getLocale() === 'ar' ? 'المرفقات' : 'Attachments')
                    ->schema([
                        ImageEntry::make('attachments')
                            ->label('')
                            ->size(150)
                            ->stacked()
                            ->limit(10),
                    ])
                    ->visible(fn($record) => !empty($record->attachments))
                    ->collapsed(),

                // Notes Section
                Section::make(fn() => app()->getLocale() === 'ar' ? 'ملاحظات' : 'Notes')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('')
                            ->markdown(),
                    ])
                    ->visible(fn($record) => $record->notes !== null)
                    ->collapsed(),

                // Audit Information Section
                Section::make(fn() => app()->getLocale() === 'ar' ? 'معلومات التدقيق' : 'Audit Information')
                    ->schema([
                        TextEntry::make('creator.name')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'أنشئ بواسطة' : 'Created By'),

                        TextEntry::make('created_at')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'تاريخ الإنشاء' : 'Created At')
                            ->dateTime('Y-m-d H:i'),

                        TextEntry::make('approver.name')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'اعتمد بواسطة' : 'Approved By')
                            ->visible(fn($record) => $record->approved_by !== null),

                        TextEntry::make('approved_at')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'تاريخ الاعتماد' : 'Approved At')
                            ->dateTime('Y-m-d H:i')
                            ->visible(fn($record) => $record->approved_at !== null),

                        TextEntry::make('updated_at')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'آخر تحديث' : 'Last Updated')
                            ->dateTime('Y-m-d H:i'),
                    ])
                    ->columns(3)
                    ->collapsed(),
            ]);
    }
}
