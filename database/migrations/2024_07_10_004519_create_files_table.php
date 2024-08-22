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
        Schema::create('files', function (Blueprint $table) {
            $table->integer('file_id', true);
            $table->text('file_title');
            $table->text('file_name')->fulltext('file_name');
            $table->text('file_url')->nullable();
            $table->text('file_dir')->nullable()->fulltext('file_dir');
            $table->string('mime_type', 100)->nullable();
            $table->text('file_desc')->nullable();
            $table->integer('uploader_id');
            $table->dateTime('input_date')->nullable()->useCurrent();
            $table->dateTime('last_update')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
