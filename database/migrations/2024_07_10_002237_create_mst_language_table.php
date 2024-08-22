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
        Schema::create('mst_language', function (Blueprint $table) {
            $table->char('language_id', 5)->primary();
            $table->string('language_name', 20)->unique('language_name');
            $table->date('input_date')->nullable()->useCurrent();
            $table->date('last_update')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_language');
    }
};
