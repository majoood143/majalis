<?php

namespace App\Livewire;

use App\Models\HallOwner;
use App\Models\User;
use App\Notifications\HallOwnerApplicationAcknowledgementNotification;
use App\Notifications\HallOwnerApplicationNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Livewire\WithFileUploads;

class HallOwnerRegistration extends Component
{
    use WithFileUploads;

    public int $currentStep = 1;
    public int $totalSteps  = 5;

    // Step 1: Account Info
    public string $name                  = '';
    public string $email                 = '';
    public string $phone                 = '';
    public string $password              = '';
    public string $password_confirmation = '';

    // Step 2: Business Info
    public string $business_name           = '';
    public string $business_name_ar        = '';
    public string $commercial_registration = '';
    public string $tax_number              = '';

    // Step 3: Contact Details
    public string $business_phone      = '';
    public string $business_email      = '';
    public string $business_address    = '';
    public string $business_address_ar = '';

    // Step 4: Bank Details
    public string $bank_name           = '';
    public string $bank_account_name   = '';
    public string $bank_account_number = '';
    public string $iban                = '';

    // Step 5: Documents
    public $commercial_registration_document = null;
    public $identity_document                = null;
    public $tax_certificate                  = null;

    // ─── Step rules ───────────────────────────────────────────────────────────

    protected function rulesForStep(int $step): array
    {
        return match ($step) {
            1 => [
                'name'                  => ['required', 'string', 'max:255'],
                'email'                 => ['required', 'email', 'max:255', 'unique:users,email'],
                'phone'                 => ['required', 'string', 'max:20'],
                'password'              => ['required', 'string', 'min:8', 'same:password_confirmation'],
                'password_confirmation' => ['required', 'string'],
            ],
            2 => [
                'business_name'           => ['required', 'string', 'max:255'],
                'business_name_ar'        => ['nullable', 'string', 'max:255'],
                'commercial_registration' => ['required', 'string', 'max:100', 'unique:hall_owners,commercial_registration'],
                'tax_number'              => ['nullable', 'string', 'max:50'],
            ],
            3 => [
                'business_phone'      => ['required', 'string', 'max:20'],
                'business_email'      => ['nullable', 'email', 'max:255'],
                'business_address'    => ['required', 'string', 'max:500'],
                'business_address_ar' => ['nullable', 'string', 'max:500'],
            ],
            4 => [
                'bank_name'           => ['nullable', 'string', 'max:255'],
                'bank_account_name'   => ['nullable', 'string', 'max:255'],
                'bank_account_number' => ['nullable', 'string', 'max:17', 'regex:/^\d*$/'],
                'iban'                => ['nullable', 'string', 'max:34'],
            ],
            5 => [
                'commercial_registration_document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
                'identity_document'                => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
                'tax_certificate'                  => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            ],
            default => [],
        };
    }

    protected function messagesForStep(int $step): array
    {
        return match ($step) {
            1 => [
                'name.required'                  => 'Please enter your full name.',
                'email.required'                 => 'Please enter your email address.',
                'email.email'                    => 'Please enter a valid email address.',
                'email.unique'                   => 'This email is already registered. Please use a different email or sign in.',
                'phone.required'                 => 'Please enter your phone number.',
                'password.required'              => 'Please enter a password.',
                'password.min'                   => 'Password must be at least 8 characters.',
                'password.same'                  => 'Passwords do not match.',
                'password_confirmation.required' => 'Please confirm your password.',
            ],
            2 => [
                'business_name.required'           => 'Please enter your business name.',
                'commercial_registration.required' => 'Please enter your commercial registration number.',
                'commercial_registration.unique'   => 'This commercial registration number is already registered.',
            ],
            3 => [
                'business_phone.required'   => 'Please enter your business phone number.',
                'business_address.required' => 'Please enter your business address.',
                'business_email.email'      => 'Please enter a valid business email address.',
            ],
            4 => [
                'bank_account_number.regex' => 'Account number must contain digits only.',
            ],
            5 => [
                'commercial_registration_document.required' => 'Please upload your commercial registration document.',
                'commercial_registration_document.mimes'    => 'Document must be a PDF, JPG, or PNG file.',
                'commercial_registration_document.max'      => 'Document must not exceed 5MB.',
                'identity_document.required'                => 'Please upload your identity document.',
                'identity_document.mimes'                   => 'Document must be a PDF, JPG, or PNG file.',
                'identity_document.max'                     => 'Document must not exceed 5MB.',
                'tax_certificate.mimes'                     => 'Tax certificate must be a PDF, JPG, or PNG file.',
                'tax_certificate.max'                       => 'Tax certificate must not exceed 5MB.',
            ],
            default => [],
        };
    }

    // ─── Navigation ───────────────────────────────────────────────────────────

    public function nextStep(): void
    {
        $this->resetErrorBag();

        $this->validate(
            $this->rulesForStep($this->currentStep),
            $this->messagesForStep($this->currentStep)
        );

        $this->currentStep++;
    }

    public function prevStep(): void
    {
        $this->resetErrorBag();

        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    // ─── Submit ───────────────────────────────────────────────────────────────

    public function submit(): void
    {
        $this->resetErrorBag();

        $this->validate(
            $this->rulesForStep($this->currentStep),
            $this->messagesForStep($this->currentStep)
        );

        DB::transaction(function () {
            $user = User::create([
                'name'      => $this->name,
                'email'     => $this->email,
                'phone'     => $this->phone,
                'password'  => Hash::make($this->password),
                'role'      => 'customer',
                'is_active' => true,
            ]);

            $crDocPath  = $this->commercial_registration_document->store('hall-owner-documents', 'public');
            $idDocPath  = $this->identity_document->store('hall-owner-documents', 'public');
            $taxDocPath = $this->tax_certificate
                ? $this->tax_certificate->store('hall-owner-documents', 'public')
                : null;

            HallOwner::create([
                'user_id'                          => $user->id,
                'business_name'                    => $this->business_name,
                'business_name_ar'                 => $this->business_name_ar ?: null,
                'commercial_registration'          => $this->commercial_registration,
                'tax_number'                       => $this->tax_number ?: null,
                'business_phone'                   => $this->business_phone,
                'business_email'                   => $this->business_email ?: null,
                'business_address'                 => $this->business_address,
                'business_address_ar'              => $this->business_address_ar ?: null,
                'bank_name'                        => $this->bank_name ?: null,
                'bank_account_name'                => $this->bank_account_name ?: null,
                'bank_account_number'              => $this->bank_account_number ?: null,
                'iban'                             => $this->iban ?: null,
                'commercial_registration_document' => $crDocPath,
                'identity_document'                => $idDocPath,
                'tax_certificate'                  => $taxDocPath,
                'is_verified'                      => false,
                'is_active'                        => true,
            ]);

            // Acknowledge the applicant
            $user->notify(new HallOwnerApplicationAcknowledgementNotification($this->business_name));

            // Notify every admin with a direct link to the new record
            $hallOwnerRecord = HallOwner::where('user_id', $user->id)->first();
            $admins = User::admins()->get();
            foreach ($admins as $admin) {
                $admin->notify(new HallOwnerApplicationNotification($user, $this->business_name, $hallOwnerRecord));
            }
        });

        $this->redirect(route('hall-owner.register.success'), navigate: false);
    }

    public function render()
    {
        return view('livewire.hall-owner-registration');
    }
}
