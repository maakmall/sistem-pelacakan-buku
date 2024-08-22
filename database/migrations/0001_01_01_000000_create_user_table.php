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
        Schema::create('user', function (Blueprint $table) {
            $table->integer('user_id', true);
            $table->string('username', 50)->unique('username');
            $table->string('realname', 100)->index('realname');
            $table->string('passwd', 64);
            $table->string('email', 200)->nullable();
            $table->smallInteger('user_type')->nullable();
            $table->string('user_image', 250)->nullable();
            $table->text('social_media')->nullable();
            $table->dateTime('last_login')->nullable();
            $table->char('last_login_ip', 15)->nullable();
            $table->string('groups', 200)->nullable();
            $table->date('input_date')->nullable();
            $table->date('last_update')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
