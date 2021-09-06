<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSlugsToLegacyProjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $projects = DB::table('projects')
            ->where('slug', '')
            ->orWhereNull('slug')
            ->cursor();

        foreach ($projects as $project) {
            DB::table('projects')->where('id', $project->id)->update(['slug' => slugify($project->name)]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
