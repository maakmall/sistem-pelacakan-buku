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
        Schema::create('mst_author', function (Blueprint $table) {
            $table->integer('author_id', true);
            $table->string('author_name', 100);
            $table->string('author_year', 20)->nullable();
            $table->enum('authority_type', ['p', 'o', 'c'])->nullable()->default('p');
            $table->string('auth_list', 20)->nullable();
            $table->date('input_date')->nullable()->useCurrent();
            $table->date('last_update')->nullable()->useCurrent();

            $table->unique(['author_name', 'authority_type'], 'author_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_author');
    }
};
