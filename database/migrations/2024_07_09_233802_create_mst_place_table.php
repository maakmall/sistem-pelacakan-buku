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
        Schema::create('mst_place', function (Blueprint $table) {
            $table->integer('place_id', true);
            $table->string('place_name', 30)->unique('place_name');
            $table->date('input_date')->nullable()->useCurrent();
            $table->date('last_update')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_place');
    }
};
