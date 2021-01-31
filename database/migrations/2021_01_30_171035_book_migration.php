<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BookMigration extends Migration
{
    /**
     * Note : all migrations are intentionally used in one migrations.
     */

    public function up()
    {

        Schema::create('authors', function(Blueprint $table){
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('books', function(Blueprint $table){
            $table->id();
            $table->string('title');
            $table->foreignId('author_id')->constrained('authors');
            $table->integer('year');
            $table->integer('quantity');
            $table->timestamps();
        });

        Schema::create('statuses', function(Blueprint $table){
            $table->id();
            $table->string('title');
        });

        Schema::create('actions', function(Blueprint $table){
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('book_id')->constrained('books');
            $table->foreignId('status_id')->constrained('statuses');
            $table->dateTime('valid_from');
            $table->dateTime('valid_to');
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
        Schema::dropIfExists('actions');
        Schema::dropIfExists('books');
        Schema::dropIfExists('authors');
        Schema::dropIfExists('statuses');
    }
}
