<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('hall_owners', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('business_name_ar');
        });
    }

    public function down(): void
    {
        Schema::table('hall_owners', function (Blueprint $table) {
            $table->dropColumn('logo');
        });
    }
};
