<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Expenses\Api\Requests;

use App\Rules\FileOrPath;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExpenseRequest extends FormRequest
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
            'expense_type' => ['sometimes', 'required', 'in:booking,operational,recurring,one_time'],
            'hall_id' => ['sometimes', 'nullable', 'integer', Rule::exists('halls', 'id')->where('owner_id', $this->user()->id)->whereNull('deleted_at')],
            'booking_id' => ['sometimes', 'nullable', 'integer', Rule::exists('bookings', 'id')->where(fn ($query) => $query->whereIn('hall_id', fn ($halls) => $halls->select('id')->from('halls')->where('owner_id', $this->user()->id)))],
            'category_id' => ['sometimes', 'nullable', 'integer', Rule::exists('expense_categories', 'id')->where(fn ($query) => $query->whereNull('owner_id')->orWhereIn('owner_id', array_filter([$this->user()->id, $this->user()->hallOwner?->id])))],
            'title' => ['sometimes', 'required', 'array'],
            'title.en' => ['nullable', 'string'],
            'title.ar' => ['nullable', 'string'],
            'description' => ['sometimes', 'nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],
            'amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'tax_amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'expense_date' => ['sometimes', 'required', 'date'],
            'payment_method' => ['sometimes', 'required', 'in:cash,bank_transfer,card,cheque,other'],
            'payment_status' => ['sometimes', 'required', 'in:pending,paid,partial,cancelled'],
            'payment_reference' => ['sometimes', 'nullable', 'string', 'max:100'],
            'due_date' => ['sometimes', 'nullable', 'date'],
            'vendor_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'vendor_phone' => ['sometimes', 'nullable', 'string', 'max:20'],
            'vendor_email' => ['sometimes', 'nullable', 'string', 'max:255', 'email'],
            'is_recurring' => ['sometimes', 'required', 'boolean'],
            'recurring_frequency' => ['sometimes', 'nullable', 'in:daily,weekly,monthly,quarterly,yearly'],
            'recurring_start_date' => ['sometimes', 'nullable', 'date'],
            'recurring_end_date' => ['sometimes', 'nullable', 'date'],
            'attachments' => ['sometimes', 'nullable', 'array'],
            'attachments.*' => [new FileOrPath(image: false)],
            'notes' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
