<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuilderPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('builder_pages')) return;

        Schema::create('builder_pages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 150);
            $table->integer('pageable_id')->index();
            $table->string('pageable_type', 50)->default('App\Project');
            $table->string('description')->nullable();
            $table->string('tags')->nullable();
            $table->string('title')->nullable();
            $table->timestamps();

            $table->unique(['name', 'pageable_type', 'pageable_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('builder_pages');
    }
}
