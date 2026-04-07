<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Make tickets.user_id nullable to support guest ticket submissions.
 * Guest tickets store the submitter's email in the metadata column
 * and are linked to a user account when they register.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable(false)
                ->change();
        });
    }
};
