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
        Schema::create('biblio_attachment', function (Blueprint $table) {
            $table->integer('biblio_id')->index('biblio_id');
            $table->integer('file_id')->index('file_id');
            $table->enum('access_type', ['public', 'private']);
            $table->text('access_limit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biblio_attachment');
    }
};
