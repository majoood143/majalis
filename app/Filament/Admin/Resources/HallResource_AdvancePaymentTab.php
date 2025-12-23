<?php

/**
 * HallResource - Advance Payment Form Section
 *
 * Add this section to your existing HallResource.php form
 * Location: app/Filament/Admin/Resources/HallResource.php
 *
 * Insert this as a new Tab after the "Pricing" tab and before "Contact" tab
 */

use Filament\Forms;

// =============================================
// TAB: ADVANCE PAYMENT SETTINGS
// =============================================
Forms\Components\Tabs\Tab::make(__('advance_payment.advance_payment'))
    ->icon('heroicon-o-currency-dollar')
    ->schema([

        Forms\Components\Section::make(__('advance_payment.advance_payment_settings'))
            ->description(__('advance_payment.advance_payment_explanation'))
            ->schema([

                // Enable/Disable Toggle
                Forms\Components\Toggle::make('allows_advance_payment')
                    ->label(__('advance_payment.allows_advance_payment'))
                    ->helperText(__('advance_payment.allows_advance_payment_help'))
                    ->reactive()
                    ->default(false)
                    ->columnSpanFull(),

                // Advance Payment Type Selection
                Forms\Components\Radio::make('advance_payment_type')
                    ->label(__('advance_payment.advance_payment_type'))
                    ->helperText(__('advance_payment.advance_payment_type_help'))
                    ->options([
                        'fixed' => __('advance_payment.advance_type_fixed'),
                        'percentage' => __('advance_payment.advance_type_percentage'),
                    ])
                    ->default('percentage')
                    ->reactive()
                    ->inline()
                    ->visible(fn ($get) => $get('allows_advance_payment'))
                    ->required(fn ($get) => $get('allows_advance_payment')),

                // Fixed Amount Field
                Forms\Components\TextInput::make('advance_payment_amount')
                    ->label(__('advance_payment.advance_payment_amount'))
                    ->helperText(__('advance_payment.advance_payment_amount_help'))
                    ->numeric()
                    ->prefix('OMR')
                    ->step(0.001)
                    ->minValue(0)
                    ->placeholder(__('advance_payment.advance_payment_amount_placeholder'))
                    ->visible(fn ($get) => $get('allows_advance_payment') && $get('advance_payment_type') === 'fixed')
                    ->required(fn ($get) => $get('allows_advance_payment') && $get('advance_payment_type') === 'fixed')
                    ->rule('min:0.001')
                    ->reactive(),

                // Percentage Field
                Forms\Components\TextInput::make('advance_payment_percentage')
                    ->label(__('advance_payment.advance_payment_percentage'))
                    ->helperText(__('advance_payment.advance_payment_percentage_help'))
                    ->numeric()
                    ->suffix('%')
                    ->step(0.01)
                    ->minValue(0.01)
                    ->maxValue(100)
                    ->placeholder(__('advance_payment.advance_payment_percentage_placeholder'))
                    ->visible(fn ($get) => $get('allows_advance_payment') && $get('advance_payment_type') === 'percentage')
                    ->required(fn ($get) => $get('allows_advance_payment') && $get('advance_payment_type') === 'percentage')
                    ->rule('max:100')
                    ->reactive(),

                // Minimum Advance Payment
                Forms\Components\TextInput::make('minimum_advance_payment')
                    ->label(__('advance_payment.minimum_advance_payment'))
                    ->helperText(__('advance_payment.minimum_advance_payment_help'))
                    ->numeric()
                    ->prefix('OMR')
                    ->step(0.001)
                    ->minValue(0)
                    ->placeholder(__('advance_payment.minimum_advance_payment_placeholder'))
                    ->visible(fn ($get) => $get('allows_advance_payment')),

            ])->columns(2)->collapsible(),

        // Preview Section
        Forms\Components\Section::make(__('advance_payment.advance_payment_preview'))
            ->description(__('advance_payment.advance_payment_preview_help'))
            ->schema([

                Forms\Components\Placeholder::make('advance_preview')
                    ->label('')
                    ->content(function ($get, $record) {
                        // Get advance payment settings
                        $allowsAdvance = $get('allows_advance_payment');

                        if (!$allowsAdvance) {
                            return '<div class="text-sm text-gray-500">'
                                . __('advance_payment.advance_payment') . ' '
                                . __('Disabled')
                                . '</div>';
                        }

                        $type = $get('advance_payment_type');
                        $fixedAmount = (float) $get('advance_payment_amount');
                        $percentage = (float) $get('advance_payment_percentage');
                        $minimumAdvance = (float) $get('minimum_advance_payment');

                        // Get base price for preview (use price_per_slot or 1000 as example)
                        $basePrice = $record ? (float) $record->price_per_slot : 1000.000;

                        // Use a sample total price for preview
                        $sampleTotal = $basePrice + 200; // Assume 200 OMR in services

                        // Calculate advance based on type
                        $advanceAmount = 0.0;
                        if ($type === 'fixed') {
                            $advanceAmount = $fixedAmount;
                        } elseif ($type === 'percentage' && $percentage > 0) {
                            $advanceAmount = ($sampleTotal * $percentage) / 100;
                        }

                        // Apply minimum if set
                        if ($minimumAdvance > 0 && $advanceAmount < $minimumAdvance) {
                            $advanceAmount = $minimumAdvance;
                        }

                        // Ensure advance doesn't exceed total
                        if ($advanceAmount > $sampleTotal) {
                            $advanceAmount = $sampleTotal;
                        }

                        $balance = $sampleTotal - $advanceAmount;

                        return '<div class="p-4 space-y-3 border border-blue-200 rounded-lg bg-blue-50 dark:bg-blue-900/20 dark:border-blue-800">
                            <div class="text-sm font-semibold text-blue-900 dark:text-blue-100">
                                ' . __('advance_payment.preview_for_price', ['price' => number_format($sampleTotal, 3)]) . '
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <div class="text-xs tracking-wide text-blue-700 uppercase dark:text-blue-300">
                                        ' . __('advance_payment.customer_pays_advance') . '
                                    </div>
                                    <div class="text-2xl font-bold text-blue-900 dark:text-blue-100">
                                        ' . number_format($advanceAmount, 3) . ' <span class="text-sm font-normal">OMR</span>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <div class="text-xs tracking-wide text-blue-700 uppercase dark:text-blue-300">
                                        ' . __('advance_payment.balance_due_before_event') . '
                                    </div>
                                    <div class="text-2xl font-bold text-blue-900 dark:text-blue-100">
                                        ' . number_format($balance, 3) . ' <span class="text-sm font-normal">OMR</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 text-xs text-blue-600 dark:text-blue-400">
                                💡 ' . __('advance_payment.advance_includes_services') . '
                            </div>
                        </div>';
                    })
                    ->columnSpanFull(),

            ])->visible(fn ($get) => $get('allows_advance_payment')),

                ]);
