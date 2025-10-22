<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('customer')->after('email');
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('phone_country_code', 5)->default('+968')->after('phone');
            $table->boolean('is_active')->default(true)->after('remember_token');
            $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
            $table->softDeletes();

            $table->index('role');
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['phone']);
            $table->dropColumn([
                'role',
                'phone',
                'phone_country_code',
                'is_active',
                'phone_verified_at',
                'deleted_at'
            ]);
        });
    }
};
