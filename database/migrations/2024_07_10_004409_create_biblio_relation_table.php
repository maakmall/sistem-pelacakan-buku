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
        Schema::create('biblio_relation', function (Blueprint $table) {
            $table->integer('biblio_id')->default(0);
            $table->integer('rel_biblio_id')->default(0);
            $table->integer('rel_type')->nullable()->default(1);

            $table->primary(['biblio_id', 'rel_biblio_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biblio_relation');
    }
};
