<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnimalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->string('scientific_name');
            $table->string('zoo_wing');
            $table->string('image_url');
            $table->boolean('is_enabled')->default(true);
            $table->integer('likes')->default(0)->nullable();
            $table->integer('dislikes')->default(0)->nullable();
            $table->integer('interactions')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('animals');
    }
}
