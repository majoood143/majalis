<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Grace-period analytics flag (review submitted 7–14 days post-event)
            $table->boolean('is_late_review')->default(false)->after('owner_response_at');

            // Customer opted in to future marketing communications
            $table->boolean('marketing_consent')->default(false)->after('is_late_review');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['is_late_review', 'marketing_consent']);
        });
    }
};
