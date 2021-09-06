<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenamePagesTableToBuilderPages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ( ! Schema::hasTable('pages') || Schema::hasTable('builder_pages')) return;

        Schema::table('pages', function (Blueprint $table) {
            $table->rename('builder_pages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('builder_pages', function (Blueprint $table) {
            $table->rename('pages');
        });
    }
}
