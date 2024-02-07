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
        Schema::create('serie_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBiginteger('serie_id')->unsigned();
            $table->unsignedBiginteger('user_id')->unsigned();

            $table->foreign('serie_id')->references('id')
                ->on('series')->onDelete('cascade');
            $table->foreign('user_id')->references('id')
                ->on('users')->onDelete('cascade');
            });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_serie');
    }
};
