<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('serie_serie', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_serie_id');
            $table->unsignedBigInteger('child_serie_id');

            $table->foreign('parent_serie_id')->references('id')->on('series')->onDelete('cascade');
            $table->foreign('child_serie_id')->references('id')->on('series')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('serie_serie');
    }
};
