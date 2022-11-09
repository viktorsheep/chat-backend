<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPageIdFiledToFacebookNotificationLogsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('facebook_notification_logs', function (Blueprint $table) {
            $table->string('page_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('facebook_notification_logs', function (Blueprint $table) {
            $table->dropColumn('page_id');
        });
    }
}
