<?php

namespace App\Http\Controllers;

use App\Project;
use Common\Core\BaseController;

class ProjectSettingsController extends BaseController
{
    public function store(Project $project)
    {
        $this->authorize('update', $project);
        $project->settings = request()->all();

        foreach ($project->settings as $key => $value) {
            if ($key === 'slug') {
                $project->slug = $value;
            }
        }

        $project->save();

        return $this->success(['project' => $project]);
    }
}
