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
        Schema::create('mst_gmd', function (Blueprint $table) {
            $table->integer('gmd_id', true);
            $table->string('gmd_code', 3)->unique('gmd_code')->nullable()->default(null);
            $table->string('gmd_name', 30)->unique('gmd_name');
            $table->string('icon_image', 100)->nullable()->default(null);
            $table->date('input_date')->nullable()->useCurrent();
            $table->date('last_update')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_gmd');
    }
};
