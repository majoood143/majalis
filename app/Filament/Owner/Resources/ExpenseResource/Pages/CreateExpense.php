<?php

declare(strict_types=1);

/**
 * CreateExpense Page
 * 
 * Handles the creation of new expenses with owner auto-assignment.
 * 
 * @package App\Filament\Owner\Resources\ExpenseResource\Pages
 * @author  Majalis Development Team
 * @version 1.0.0
 */

namespace App\Filament\Owner\Resources\ExpenseResource\Pages;

use App\Filament\Owner\Resources\ExpenseResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

/**
 * CreateExpense Page Class
 */
class CreateExpense extends CreateRecord
{
    /**
     * The resource this page belongs to.
     *
     * @var string
     */
    protected static string $resource = ExpenseResource::class;

    /**
     * Mutate form data before creating the record.
     *
     * @param array $data
     * @return array
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-assign owner_id to current authenticated user
        $data['owner_id'] = Auth::user()?->hallOwner?->id ?? Auth::id();
        
        // Set created_by
        $data['created_by'] = Auth::id();

        return $data;
    }

    /**
     * Get the redirect URL after creation.
     *
     * @return string
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Handle after record creation.
     *
     * @return void
     */
    protected function afterCreate(): void
    {
        Notification::make()
            ->title(app()->getLocale() === 'ar' ? 'تم إنشاء المصروف بنجاح' : 'Expense created successfully')
            ->success()
            ->send();
    }
}
