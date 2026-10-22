<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Expenses\Api\Requests;

use App\Rules\FileOrPath;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateExpenseRequest extends FormRequest
{
    /**
     * Access is checked by the route middleware (token, panel access, permission).
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'expense_type' => ['sometimes', 'in:booking,operational,recurring,one_time'],
            'hall_id' => ['nullable', 'integer', Rule::exists('halls', 'id')->where('owner_id', $this->user()->id)->whereNull('deleted_at')],
            'booking_id' => ['nullable', 'integer', Rule::exists('bookings', 'id')->where(fn ($query) => $query->whereIn('hall_id', fn ($halls) => $halls->select('id')->from('halls')->where('owner_id', $this->user()->id)))],
            'category_id' => ['nullable', 'integer', Rule::exists('expense_categories', 'id')->where(fn ($query) => $query->whereNull('owner_id')->orWhereIn('owner_id', array_filter([$this->user()->id, $this->user()->hallOwner?->id])))],
            'title' => ['required', 'array'],
            'title.en' => ['required', 'string'],
            'title.ar' => ['required', 'string'],
            'description' => ['nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0'],
            'tax_amount' => ['sometimes', 'numeric', 'min:0'],
            'expense_date' => ['required', 'date'],
            'payment_method' => ['sometimes', 'in:cash,bank_transfer,card,cheque,other'],
            'payment_status' => ['sometimes', 'in:pending,paid,partial,cancelled'],
            'payment_reference' => ['nullable', 'string', 'max:100'],
            'due_date' => ['nullable', 'date'],
            'vendor_name' => ['nullable', 'string', 'max:255'],
            'vendor_phone' => ['nullable', 'string', 'max:20'],
            'vendor_email' => ['nullable', 'string', 'max:255', 'email'],
            'is_recurring' => ['sometimes', 'boolean'],
            'recurring_frequency' => ['nullable', 'in:daily,weekly,monthly,quarterly,yearly'],
            'recurring_start_date' => ['nullable', 'date'],
            'recurring_end_date' => ['nullable', 'date'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => [new FileOrPath(image: false)],
            'notes' => ['nullable', 'string'],
        ];
    }
}
