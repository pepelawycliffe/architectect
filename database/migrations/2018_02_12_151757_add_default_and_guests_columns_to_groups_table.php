<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultAndGuestsColumnsToGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            if ( ! Schema::hasColumn('groups', 'default')) {
                $table->boolean('default')->default(0)->unsigned()->index();
            }

            if ( ! Schema::hasColumn('groups', 'guests')) {
                $table->boolean('guests')->default(0)->unsigned()->index();
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
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('default');
            $table->dropColumn('guests');
        });
    }
}
