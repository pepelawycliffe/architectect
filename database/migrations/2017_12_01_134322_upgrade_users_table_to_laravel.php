<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpgradeUsersTableToLaravel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ( ! Schema::hasTable('users')) return;

        Schema::table('users', function (Blueprint $table) {
            if ( ! Schema::hasColumn('users', 'remember_token')) {
                $table->rememberToken();
                $table->string('card_brand')->nullable();
                $table->string('card_last_four')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropRememberToken();
            $table->dropColumn('card_brand');
            $table->dropColumn('card_last_four');
        });
    }
}
