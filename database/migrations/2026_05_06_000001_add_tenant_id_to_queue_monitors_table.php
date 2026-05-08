<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('queue_monitors', function (Blueprint $table) {
            $table->string('tenant_id')->nullable()->index()->after('exception_message');
        });
    }

    public function down(): void
    {
        Schema::table('queue_monitors', function (Blueprint $table) {
            $table->dropColumn('tenant_id');
        });
    }
};
