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
            $table->integer('igdbId')->default(0);
            $table->enum('status', ['Might Play','Backlog','In Progress','Completed','Wont Play','Abandoned','Unbeatable','Paused','Wishlist','None'])->default('None');
            $table->string('platform',20)->default('');
            $table->enum('platformType', ['PC', 'Console', 'Other']);
            $table->enum('favorite', ['Yes', 'No'])->default('No');
            $table->enum('format', ['Physical', 'Digital', 'Not Set']);
            $table->string('genre',255)->default('');
            $table->enum('rating', ['0','1','2','3','4','5'])->default('0');
            $table->string('notes',255)->default('');
            $table->boolean('owned');
            $table->boolean('wishlist');
            $table->boolean('backlog');
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
        Schema::dropIfExists('games');
    }
}
