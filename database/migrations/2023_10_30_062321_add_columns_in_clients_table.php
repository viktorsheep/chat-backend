<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInClientsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('clients', function (Blueprint $table) {
            $table->foreignId('responder_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('status')
                ->nullable()
                ->constrained('client_status')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign('clients_responder_id_foreign');
            $table->dropColumn('responder_id');
            $table->dropForeign('clients_status_foreign');
            $table->dropColumn('status');
        });
    }
}
