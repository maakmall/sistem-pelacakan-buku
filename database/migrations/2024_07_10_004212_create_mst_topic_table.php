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
        Schema::create('mst_topic', function (Blueprint $table) {
            $table->integer('topic_id', true);
            $table->string('topic', 50);
            $table->enum('topic_type', ['t', 'g', 'n', 'tm', 'gr', 'oc']);
            $table->string('auth_list', 20)->nullable();
            $table->string('classification', 50)->comment('Classification Code');
            $table->date('input_date')->nullable()->useCurrent();
            $table->date('last_update')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_topic');
    }
};
