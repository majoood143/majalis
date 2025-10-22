<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hall_owners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Business Information
            $table->string('business_name');
            $table->string('business_name_ar')->nullable();
            $table->string('commercial_registration')->unique();
            $table->string('tax_number')->nullable();

            // Contact Details
            $table->string('business_phone', 20);
            $table->string('business_email')->nullable();
            $table->text('business_address');
            $table->text('business_address_ar')->nullable();

            // Bank Details
            $table->string('bank_name')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('iban')->nullable();

            // Documents
            $table->string('commercial_registration_document')->nullable();
            $table->string('tax_certificate')->nullable();
            $table->string('identity_document')->nullable();
            $table->json('additional_documents')->nullable();

            // Verification
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->text('verification_notes')->nullable();

            // Commission Override (if different from global)
            $table->string('commission_type')->nullable(); // percentage or fixed
            $table->decimal('commission_value', 10, 2)->nullable();

            // Status
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('user_id');
            $table->index('commercial_registration');
            $table->index(['is_verified', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hall_owners');
    }
};
