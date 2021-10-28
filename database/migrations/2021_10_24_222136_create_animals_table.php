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
            $table->string('nickname');
            $table->string('scientific_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('zoo_wing');
            $table->string('image_url');
            $table->boolean('is_enabled')->default(true);
            $table->integer('likes')->nullable();
            $table->integer('dislikes')->nullable();
            $table->integer('interactions')->default(0);
            $table->timestamps();
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
