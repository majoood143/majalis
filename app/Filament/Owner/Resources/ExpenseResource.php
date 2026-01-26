<?php

declare(strict_types=1);

/**
 * ExpenseResource
 * 
 * Filament resource for managing expenses in the Owner Panel.
 * Provides full CRUD operations with filtering, exporting, and reporting.
 * 
 * @package App\Filament\Owner\Resources
 * @author  Majalis Development Team
 * @version 1.0.0
 */

namespace App\Filament\Owner\Resources;

use App\Enums\ExpensePaymentMethod;
use App\Enums\ExpensePaymentStatus;
use App\Enums\ExpenseStatus;
use App\Enums\ExpenseType;
use App\Enums\RecurringFrequency;
use App\Filament\Owner\Resources\ExpenseResource\Pages;
use App\Filament\Owner\Resources\ExpenseResource\RelationManagers;
use App\Models\Booking;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Hall;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

/**
 * ExpenseResource Class
 * 
 * Manages expense records for hall owners.
 * Features:
 * - Create/Edit expenses with bilingual support
 * - Link expenses to bookings or halls
 * - Track payment status and methods
 * - Support for recurring expenses
 * - Comprehensive filtering and reporting
 */
class ExpenseResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|null
     */
    protected static ?string $model = Expense::class;

    /**
     * The navigation icon for the resource.
     *
     * @var string|null
     */
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    /**
     * The navigation group for the resource.
     *
     * @var string|null
     */
    protected static ?string $navigationGroup = 'Financial Management';

    /**
     * The navigation sort order.
     *
     * @var int|null
     */
    protected static ?int $navigationSort = 10;

    /**
     * The plural label for the resource.
     *
     * @return string
     */
    public static function getPluralLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'المصروفات' : 'Expenses';
    }

    /**
     * The singular label for the resource.
     *
     * @return string
     */
    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'مصروف' : 'Expense';
    }

    /**
     * Get the navigation badge.
     *
     * @return string|null
     */
    public static function getNavigationBadge(): ?string
    {
        $ownerId = Auth::user()?->hallOwner?->id ?? Auth::id();
        
        return (string) static::getEloquentQuery()
            ->where('payment_status', ExpensePaymentStatus::Pending)
            ->count();
    }

    /**
     * Get the navigation badge color.
     *
     * @return string|null
     */
    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    /**
     * Define the form schema for creating/editing expenses.
     *
     * @param Form $form
     * @return Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Main Information Section
                Forms\Components\Section::make(fn() => app()->getLocale() === 'ar' ? 'معلومات المصروف' : 'Expense Information')
                    ->description(fn() => app()->getLocale() === 'ar' 
                        ? 'أدخل التفاصيل الأساسية للمصروف' 
                        : 'Enter the basic expense details')
                    ->schema([
                        // Expense Number (auto-generated, read-only on edit)
                        Forms\Components\TextInput::make('expense_number')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'رقم المصروف' : 'Expense Number')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn($record) => $record !== null),

                        // Expense Type
                        Forms\Components\Select::make('expense_type')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'نوع المصروف' : 'Expense Type')
                            ->options(ExpenseType::toArray())
                            ->required()
                            ->default(ExpenseType::Operational->value)
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set, ?string $state) {
                                // Clear booking_id if not a booking expense
                                if ($state !== ExpenseType::Booking->value) {
                                    $set('booking_id', null);
                                }
                                // Reset recurring fields if not recurring type
                                if ($state !== ExpenseType::Recurring->value) {
                                    $set('is_recurring', false);
                                    $set('recurring_frequency', null);
                                }
                            })
                            ->helperText(fn($state) => $state ? ExpenseType::tryFrom($state)?->getDescription() : null),

                        // Hall Selection
                        Forms\Components\Select::make('hall_id')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'القاعة' : 'Hall')
                            ->relationship(
                                name: 'hall',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn(Builder $query) => $query->where('owner_id', Auth::user()?->hallOwner?->id ?? Auth::id())
                            )
                            ->getOptionLabelFromRecordUsing(fn(Hall $record) => $record->getTranslation('name', app()->getLocale()))
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->live()
                            ->helperText(fn() => app()->getLocale() === 'ar' 
                                ? 'اختر القاعة المرتبطة بهذا المصروف (اختياري)' 
                                : 'Select the hall related to this expense (optional)'),

                        // Booking Selection (only for booking expenses)
                        Forms\Components\Select::make('booking_id')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'الحجز' : 'Booking')
                            ->options(function (Forms\Get $get) {
                                $hallId = $get('hall_id');
                                $ownerId = Auth::user()?->hallOwner?->id ?? Auth::id();
                                
                                $query = Booking::query()
                                    ->whereHas('hall', fn($q) => $q->where('owner_id', $ownerId));
                                
                                if ($hallId) {
                                    $query->where('hall_id', $hallId);
                                }
                                
                                return $query
                                    ->orderBy('booking_date', 'desc')
                                    ->limit(100)
                                    ->get()
                                    ->mapWithKeys(fn(Booking $booking) => [
                                        $booking->id => "{$booking->booking_number} - {$booking->customer_name} ({$booking->booking_date->format('Y-m-d')})"
                                    ]);
                            })
                            ->searchable()
                            ->nullable()
                            ->visible(fn(Forms\Get $get) => $get('expense_type') === ExpenseType::Booking->value)
                            ->helperText(fn() => app()->getLocale() === 'ar' 
                                ? 'ربط هذا المصروف بحجز معين' 
                                : 'Link this expense to a specific booking'),

                        // Category Selection
                        Forms\Components\Select::make('category_id')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'الفئة' : 'Category')
                            ->relationship(
                                name: 'category',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn(Builder $query) => $query
                                    ->where(function ($q) {
                                        $q->where('owner_id', Auth::user()?->hallOwner?->id ?? Auth::id())
                                          ->orWhere('is_system', true);
                                    })
                                    ->where('is_active', true)
                            )
                            ->getOptionLabelFromRecordUsing(fn(ExpenseCategory $record) => $record->getTranslation('name', app()->getLocale()))
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name.en')
                                    ->label('Name (English)')
                                    ->required(),
                                Forms\Components\TextInput::make('name.ar')
                                    ->label('Name (Arabic)')
                                    ->required(),
                                Forms\Components\ColorPicker::make('color')
                                    ->label('Color')
                                    ->default('#6366f1'),
                            ])
                            ->createOptionUsing(function (array $data) {
                                $data['owner_id'] = Auth::user()?->hallOwner?->id ?? Auth::id();
                                return ExpenseCategory::create($data)->id;
                            }),
                    ])
                    ->columns(2),

                // Title and Description Section
                Forms\Components\Section::make(fn() => app()->getLocale() === 'ar' ? 'الوصف' : 'Description')
                    ->schema([
                        // Title (Bilingual)
                        Forms\Components\Tabs::make('title_tabs')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make(app()->getLocale() === 'ar' ? 'العربية' : 'Arabic')
                                    ->schema([
                                        Forms\Components\TextInput::make('title.ar')
                                            ->label(fn() => app()->getLocale() === 'ar' ? 'العنوان بالعربية' : 'Title (Arabic)')
                                            ->required()
                                            ->maxLength(255)
                                            ->extraAttributes(['dir' => 'rtl']),
                                    ]),
                                Forms\Components\Tabs\Tab::make(app()->getLocale() === 'ar' ? 'الإنجليزية' : 'English')
                                    ->schema([
                                        Forms\Components\TextInput::make('title.en')
                                            ->label(fn() => app()->getLocale() === 'ar' ? 'العنوان بالإنجليزية' : 'Title (English)')
                                            ->required()
                                            ->maxLength(255),
                                    ]),
                            ])
                            ->columnSpanFull(),

                        // Description (Bilingual)
                        Forms\Components\Tabs::make('description_tabs')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make(app()->getLocale() === 'ar' ? 'العربية' : 'Arabic')
                                    ->schema([
                                        Forms\Components\Textarea::make('description.ar')
                                            ->label(fn() => app()->getLocale() === 'ar' ? 'الوصف بالعربية' : 'Description (Arabic)')
                                            ->rows(3)
                                            ->extraAttributes(['dir' => 'rtl']),
                                    ]),
                                Forms\Components\Tabs\Tab::make(app()->getLocale() === 'ar' ? 'الإنجليزية' : 'English')
                                    ->schema([
                                        Forms\Components\Textarea::make('description.en')
                                            ->label(fn() => app()->getLocale() === 'ar' ? 'الوصف بالإنجليزية' : 'Description (English)')
                                            ->rows(3),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ]),

                // Financial Details Section
                Forms\Components\Section::make(fn() => app()->getLocale() === 'ar' ? 'التفاصيل المالية' : 'Financial Details')
                    ->schema([
                        // Amount
                        Forms\Components\TextInput::make('amount')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'المبلغ (ريال عماني)' : 'Amount (OMR)')
                            ->required()
                            ->numeric()
                            ->minValue(0.001)
                            ->step(0.001)
                            ->suffix('OMR')
                            ->live(onBlur: true),

                        // Tax Amount
                        Forms\Components\TextInput::make('tax_amount')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'الضريبة (ريال عماني)' : 'Tax (OMR)')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.001)
                            ->default(0)
                            ->suffix('OMR')
                            ->live(onBlur: true),

                        // Total Display (calculated)
                        Forms\Components\Placeholder::make('calculated_total')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'المجموع' : 'Total')
                            ->content(function (Forms\Get $get) {
                                $amount = (float) ($get('amount') ?? 0);
                                $tax = (float) ($get('tax_amount') ?? 0);
                                $total = $amount + $tax;
                                return number_format($total, 3) . ' OMR';
                            }),

                        // Expense Date
                        Forms\Components\DatePicker::make('expense_date')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'تاريخ المصروف' : 'Expense Date')
                            ->required()
                            ->default(now())
                            ->maxDate(now()),

                        // Payment Method
                        Forms\Components\Select::make('payment_method')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'طريقة الدفع' : 'Payment Method')
                            ->options(ExpensePaymentMethod::toArray())
                            ->default(ExpensePaymentMethod::Cash->value)
                            ->required()
                            ->live(),

                        // Payment Status
                        Forms\Components\Select::make('payment_status')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'حالة الدفع' : 'Payment Status')
                            ->options(ExpensePaymentStatus::toArray())
                            ->default(ExpensePaymentStatus::Paid->value)
                            ->required()
                            ->live(),

                        // Payment Reference
                        Forms\Components\TextInput::make('payment_reference')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'رقم المرجع' : 'Payment Reference')
                            ->maxLength(100)
                            ->visible(fn(Forms\Get $get) => 
                                ExpensePaymentMethod::tryFrom($get('payment_method') ?? '')?->requiresReference() ?? false
                            ),

                        // Due Date (for pending payments)
                        Forms\Components\DatePicker::make('due_date')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'تاريخ الاستحقاق' : 'Due Date')
                            ->visible(fn(Forms\Get $get) => 
                                in_array($get('payment_status'), [ExpensePaymentStatus::Pending->value, ExpensePaymentStatus::Partial->value])
                            ),
                    ])
                    ->columns(3),

                // Vendor Information Section
                Forms\Components\Section::make(fn() => app()->getLocale() === 'ar' ? 'معلومات المورد' : 'Vendor Information')
                    ->schema([
                        Forms\Components\TextInput::make('vendor_name')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'اسم المورد' : 'Vendor Name')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('vendor_phone')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'هاتف المورد' : 'Vendor Phone')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('vendor_email')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'بريد المورد' : 'Vendor Email')
                            ->email()
                            ->maxLength(255),
                    ])
                    ->columns(3)
                    ->collapsed()
                    ->collapsible(),

                // Recurring Expense Settings
                Forms\Components\Section::make(fn() => app()->getLocale() === 'ar' ? 'إعدادات التكرار' : 'Recurring Settings')
                    ->schema([
                        Forms\Components\Toggle::make('is_recurring')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'مصروف متكرر' : 'Recurring Expense')
                            ->live()
                            ->default(false),

                        Forms\Components\Select::make('recurring_frequency')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'تكرار' : 'Frequency')
                            ->options(RecurringFrequency::toArray())
                            ->visible(fn(Forms\Get $get) => $get('is_recurring'))
                            ->required(fn(Forms\Get $get) => $get('is_recurring')),

                        Forms\Components\DatePicker::make('recurring_start_date')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'تاريخ البداية' : 'Start Date')
                            ->visible(fn(Forms\Get $get) => $get('is_recurring'))
                            ->required(fn(Forms\Get $get) => $get('is_recurring')),

                        Forms\Components\DatePicker::make('recurring_end_date')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'تاريخ النهاية' : 'End Date')
                            ->visible(fn(Forms\Get $get) => $get('is_recurring'))
                            ->helperText(fn() => app()->getLocale() === 'ar' 
                                ? 'اتركه فارغاً للتكرار بلا نهاية' 
                                : 'Leave empty for indefinite recurring'),
                    ])
                    ->columns(2)
                    ->visible(fn(Forms\Get $get) => $get('expense_type') === ExpenseType::Recurring->value)
                    ->collapsible(),

                // Attachments Section
                Forms\Components\Section::make(fn() => app()->getLocale() === 'ar' ? 'المرفقات' : 'Attachments')
                    ->schema([
                        Forms\Components\FileUpload::make('attachments')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'إيصالات ومستندات' : 'Receipts & Documents')
                            ->multiple()
                            ->directory('expenses/attachments')
                            ->acceptedFileTypes(['image/*', 'application/pdf'])
                            ->maxSize(5120) // 5MB
                            ->downloadable()
                            ->reorderable()
                            ->helperText(fn() => app()->getLocale() === 'ar' 
                                ? 'يمكنك رفع صور أو ملفات PDF (حد أقصى 5 ميجابايت لكل ملف)' 
                                : 'Upload images or PDF files (max 5MB each)'),
                    ])
                    ->collapsed()
                    ->collapsible(),

                // Notes Section
                Forms\Components\Section::make(fn() => app()->getLocale() === 'ar' ? 'ملاحظات' : 'Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'ملاحظات داخلية' : 'Internal Notes')
                            ->rows(3)
                            ->maxLength(1000),
                    ])
                    ->collapsed()
                    ->collapsible(),
            ]);
    }

    /**
     * Define the table schema for listing expenses.
     *
     * @param Table $table
     * @return Table
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Expense Number
                Tables\Columns\TextColumn::make('expense_number')
                    ->label(fn() => app()->getLocale() === 'ar' ? 'الرقم' : 'Number')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                // Title
                Tables\Columns\TextColumn::make('title')
                    ->label(fn() => app()->getLocale() === 'ar' ? 'العنوان' : 'Title')
                    ->formatStateUsing(fn($record) => $record->getTranslation('title', app()->getLocale()))
                    ->searchable()
                    ->limit(30)
                    ->tooltip(fn($record) => $record->getTranslation('title', app()->getLocale())),

                // Expense Type
                Tables\Columns\TextColumn::make('expense_type')
                    ->label(fn() => app()->getLocale() === 'ar' ? 'النوع' : 'Type')
                    ->badge()
                    ->sortable(),

                // Category
                Tables\Columns\TextColumn::make('category.name')
                    ->label(fn() => app()->getLocale() === 'ar' ? 'الفئة' : 'Category')
                    ->formatStateUsing(fn($record) => $record->category?->getTranslation('name', app()->getLocale()) ?? '-')
                    ->sortable()
                    ->toggleable(),

                // Hall
                Tables\Columns\TextColumn::make('hall.name')
                    ->label(fn() => app()->getLocale() === 'ar' ? 'القاعة' : 'Hall')
                    ->formatStateUsing(fn($record) => $record->hall?->getTranslation('name', app()->getLocale()) ?? '-')
                    ->sortable()
                    ->toggleable(),

                // Booking
                Tables\Columns\TextColumn::make('booking.booking_number')
                    ->label(fn() => app()->getLocale() === 'ar' ? 'الحجز' : 'Booking')
                    ->sortable()
                    ->toggleable()
                    ->url(fn($record) => $record->booking_id 
                        ? route('filament.owner.resources.bookings.view', $record->booking_id) 
                        : null),

                // Amount
                Tables\Columns\TextColumn::make('total_amount')
                    ->label(fn() => app()->getLocale() === 'ar' ? 'المبلغ' : 'Amount')
                    ->money('OMR')
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('OMR'),
                    ]),

                // Expense Date
                Tables\Columns\TextColumn::make('expense_date')
                    ->label(fn() => app()->getLocale() === 'ar' ? 'التاريخ' : 'Date')
                    ->date('Y-m-d')
                    ->sortable(),

                // Payment Status
                Tables\Columns\TextColumn::make('payment_status')
                    ->label(fn() => app()->getLocale() === 'ar' ? 'حالة الدفع' : 'Payment')
                    ->badge()
                    ->sortable(),

                // Payment Method
                Tables\Columns\TextColumn::make('payment_method')
                    ->label(fn() => app()->getLocale() === 'ar' ? 'طريقة الدفع' : 'Method')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Vendor
                Tables\Columns\TextColumn::make('vendor_name')
                    ->label(fn() => app()->getLocale() === 'ar' ? 'المورد' : 'Vendor')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Status
                Tables\Columns\TextColumn::make('status')
                    ->label(fn() => app()->getLocale() === 'ar' ? 'الحالة' : 'Status')
                    ->badge()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Created At
                Tables\Columns\TextColumn::make('created_at')
                    ->label(fn() => app()->getLocale() === 'ar' ? 'تاريخ الإنشاء' : 'Created')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Expense Type Filter
                Tables\Filters\SelectFilter::make('expense_type')
                    ->label(fn() => app()->getLocale() === 'ar' ? 'النوع' : 'Type')
                    ->options(ExpenseType::toArray()),

                // Category Filter
                Tables\Filters\SelectFilter::make('category_id')
                    ->label(fn() => app()->getLocale() === 'ar' ? 'الفئة' : 'Category')
                    ->relationship('category', 'name')
                    ->getOptionLabelFromRecordUsing(fn(ExpenseCategory $record) => $record->getTranslation('name', app()->getLocale())),

                // Hall Filter
                Tables\Filters\SelectFilter::make('hall_id')
                    ->label(fn() => app()->getLocale() === 'ar' ? 'القاعة' : 'Hall')
                    ->relationship(
                        'hall',
                        'name',
                        fn(Builder $query) => $query->where('owner_id', Auth::user()?->hallOwner?->id ?? Auth::id())
                    )
                    ->getOptionLabelFromRecordUsing(fn(Hall $record) => $record->getTranslation('name', app()->getLocale())),

                // Payment Status Filter
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label(fn() => app()->getLocale() === 'ar' ? 'حالة الدفع' : 'Payment Status')
                    ->options(ExpensePaymentStatus::toArray()),

                // Payment Method Filter
                Tables\Filters\SelectFilter::make('payment_method')
                    ->label(fn() => app()->getLocale() === 'ar' ? 'طريقة الدفع' : 'Payment Method')
                    ->options(ExpensePaymentMethod::toArray()),

                // Date Range Filter
                Tables\Filters\Filter::make('expense_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'من' : 'From'),
                        Forms\Components\DatePicker::make('until')
                            ->label(fn() => app()->getLocale() === 'ar' ? 'إلى' : 'Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('expense_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('expense_date', '<=', $date),
                            );
                    }),

                // Booking Linked Filter
                Tables\Filters\TernaryFilter::make('has_booking')
                    ->label(fn() => app()->getLocale() === 'ar' ? 'مرتبط بحجز' : 'Linked to Booking')
                    ->queries(
                        true: fn(Builder $query) => $query->whereNotNull('booking_id'),
                        false: fn(Builder $query) => $query->whereNull('booking_id'),
                    ),

                // Trashed Filter
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                
                // Mark as Paid Action
                Tables\Actions\Action::make('mark_paid')
                    ->label(fn() => app()->getLocale() === 'ar' ? 'تم الدفع' : 'Mark Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn($record) => $record->payment_status !== ExpensePaymentStatus::Paid)
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->markAsPaid()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    
                    // Bulk Mark as Paid
                    Tables\Actions\BulkAction::make('bulk_mark_paid')
                        ->label(fn() => app()->getLocale() === 'ar' ? 'تم الدفع' : 'Mark as Paid')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn($records) => $records->each->markAsPaid()),
                ]),
            ])
            ->defaultSort('expense_date', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    /**
     * Get the relationships available for the resource.
     *
     * @return array
     */
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * Get the pages available for the resource.
     *
     * @return array
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'view' => Pages\ViewExpense::route('/{record}'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }

    /**
     * Get the Eloquent query for the resource.
     *
     * @return Builder
     */
    public static function getEloquentQuery(): Builder
    {
        $ownerId = Auth::user()?->hallOwner?->id ?? Auth::id();

        return parent::getEloquentQuery()
            ->where('owner_id', $ownerId)
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
