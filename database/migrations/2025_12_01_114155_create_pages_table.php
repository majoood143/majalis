<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the pages table for storing static content pages like About Us, Terms, etc.
     */
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();

            // Page identification
            $table->string('slug')->unique()->comment('URL-friendly identifier');
            $table->string('title_en')->comment('Page title in English');
            $table->string('title_ar')->comment('Page title in Arabic');

            // Page content
            $table->longText('content_en')->comment('Page content in English');
            $table->longText('content_ar')->comment('Page content in Arabic');

            // SEO meta tags
            $table->string('meta_title_en')->nullable()->comment('SEO meta title in English');
            $table->string('meta_title_ar')->nullable()->comment('SEO meta title in Arabic');
            $table->text('meta_description_en')->nullable()->comment('SEO meta description in English');
            $table->text('meta_description_ar')->nullable()->comment('SEO meta description in Arabic');

            // Status and ordering
            $table->boolean('is_active')->default(true)->comment('Whether the page is visible');
            $table->integer('order')->default(0)->comment('Display order in navigation');

            // Show in footer/header navigation
            $table->boolean('show_in_footer')->default(false)->comment('Display link in footer');
            $table->boolean('show_in_header')->default(false)->comment('Display link in header');

            $table->timestamps();
            $table->softDeletes();

            // Indexes for better performance
            $table->index('slug');
            $table->index('is_active');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
