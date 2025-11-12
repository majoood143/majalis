<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds a language preference column to store user's preferred language
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('language_preference', 5)
                ->default('en')
                ->after('email')
                ->comment('User preferred language (en, ar)');

            // Add index for better performance when filtering by language
            $table->index('language_preference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['language_preference']);
            $table->dropColumn('language_preference');
        });
    }
};
