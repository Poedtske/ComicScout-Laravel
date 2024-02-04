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
        Schema::create('genre_serie', function (Blueprint $table) {
            $table->id();
            $table->unsignedBiginteger('genre_id')->unsigned();
            $table->unsignedBiginteger('serie_id')->unsigned();

            $table->foreign('genre_id')->references('id')
                ->on('genres')->onDelete('cascade');
            $table->foreign('serie_id')->references('id')
                ->on('series')->onDelete('cascade');
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('genre_serie');
    }
};
