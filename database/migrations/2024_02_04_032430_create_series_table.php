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


        Schema::create('series', function (Blueprint $table) {
            $table->id();
            $table->string("status");
            $table->string("title");
            $table->string("url");
            $table->string("cover");
            $table->string("author")->nullable();
            $table->string("company")->nullable();
            $table->string("artists")->nullable();
            $table->string("type")->nullable();
            $table->longtext("description")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('series');
    }
};
