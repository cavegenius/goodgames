<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Games extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('userId')->unsigned();
            $table->foreign('userId')->references('id')->on('users');
            $table->string('name',255);
            $table->integer('igdbId');
            $table->enum('status', ['Might Play','Backlog','In Progress','Completed','Wont Play','Abandoned','Unbeatable','Paused','Wishlist']);
            $table->string('platform',20);
            $table->enum('platformType', ['PC', 'Console', 'Other']);
            $table->enum('favorite', ['Yes', 'No']);
            $table->enum('format', ['physical', 'digital']);
            $table->enum('rating', [1,2,3,4,5]);
            $table->string('notes',255);
            $table->boolean('owned');
            $table->boolean('wishlist');
            $table->boolean('backlog');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games');
    }
}
