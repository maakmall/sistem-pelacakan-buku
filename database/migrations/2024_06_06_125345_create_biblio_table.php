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
        Schema::create('biblio', function (Blueprint $table) {
            $table->integer('biblio_id', true);
            $table->integer('gmd_id')->nullable();
            $table->text('title');
            $table->string('sor', 200)->nullable();
            $table->string('edition', 50)->nullable();
            $table->string('isbn_issn', 20)->nullable();
            $table->integer('publisher_id')->nullable();
            $table->string('publish_year', 20)->nullable();
            $table->string('collation', 50)->nullable();
            $table->string('series_title', 200)->nullable();
            $table->string('call_number', 50)->nullable();
            $table->char('language_id', 5)->nullable()->default('en');
            $table->string('source', 3)->nullable();
            $table->integer('publish_place_id')->nullable();
            $table->string('classification', 40)->nullable();
            $table->text('notes')->nullable();
            $table->string('image', 100)->nullable();
            $table->string('file_att', 255)->nullable();
            $table->smallInteger('opac_hide')->nullable()->default(0);
            $table->smallInteger('promoted')->nullable()->default(0);
            $table->text('labels')->nullable();
            $table->integer('frequency_id')->default(0);
            $table->text('spec_detail_info')->nullable();
            $table->integer('content_type_id')->nullable();
            $table->integer('media_type_id')->nullable();
            $table->integer('carrier_type_id')->nullable();
            $table->dateTime('input_date')->nullable()->useCurrent();
            $table->dateTime('last_update')->nullable()->useCurrent();

            // Indexes
            $table->index(['gmd_id', 'publisher_id', 'language_id', 'publish_place_id'], 'references_idx');
            $table->index('classification', 'classification');
            $table->index(['opac_hide', 'promoted'], 'biblio_flag_idx');
            $table->index(['content_type_id', 'media_type_id', 'carrier_type_id'], 'content_type_id');

            // Fulltext Indexes (only for MySQL)
            $table->fullText(['title', 'series_title'], 'title_ft_idx');
            $table->fullText('notes', 'notes_ft_idx');
            $table->fullText('labels', 'labels');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biblio');
    }
};
