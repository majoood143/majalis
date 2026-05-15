<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hall_event_type', function (Blueprint $table) {
            $table->foreignId('hall_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_type_id')->constrained()->cascadeOnDelete();
            $table->primary(['hall_id', 'event_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hall_event_type');
    }
};
