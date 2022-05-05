<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPagesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('user_pages', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')
        ->constrained('users')
        ->onDelete('cascade')
        ->onUpdate('cascade');

      $table->foreignId('page_id')
        ->constrained('fb_pages')
        ->onDelete('cascade')
        ->onUpdate('cascade');

      $table->boolean('is_joined');
      $table->dateTime('joined_date');
      $table->dateTime('left_date')->nullable();
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
    Schema::dropIfExists('user_pages');
  }
}
