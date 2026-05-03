<?php

declare(strict_types=1);

/**
 * EditExpense Page
 * 
 * Handles editing of existing expenses with validation.
 * 
 * @package App\Filament\Owner\Resources\ExpenseResource\Pages
 * @author  Majalis Development Team
 * @version 1.0.0
 */

namespace App\Filament\Owner\Resources\ExpenseResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use App\Filament\Owner\Resources\ExpenseResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

/**
 * EditExpense Page Class
 */
class EditExpense extends EditRecord
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
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    /**
     * Get the redirect URL after update.
     *
     * @return string
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Handle after record update.
     *
     * @return void
     */
    protected function afterSave(): void
    {
        Notification::make()
            ->title(app()->getLocale() === 'ar' ? 'تم تحديث المصروف بنجاح' : 'Expense updated successfully')
            ->success()
            ->send();
    }
}
