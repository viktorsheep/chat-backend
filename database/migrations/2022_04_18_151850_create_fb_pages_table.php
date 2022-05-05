<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFbPagesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('fb_pages', function (Blueprint $table) {
      $table->id();
      $table->text('name');
      $table->text('url');
      $table->text('contact_person');
      $table->boolean('is_active');

      $table->foreignId('created_by')
        ->constrained('users')
        ->onDelete('cascade')
        ->onUpdate('cascade');

      $table->foreignId('updated_by')
        ->constrained('users')
        ->onDelete('cascade')
        ->onUpdate('cascade');

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
    Schema::dropIfExists('fb_pages');
  }
}
