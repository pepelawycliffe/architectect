<?php

namespace App\Http\Controllers;

use App\Project;
use App\Services\RenderUserSite;
use Common\Core\BaseController;

class UserSiteController extends BaseController
{
    /**
     * @var Project
     */
    private $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function show(
        $projectSlug,
        string $pageName = 'index',
        string $tls = null,
        string $page = null
    ): string {
        $project = $this->project->where('slug', $projectSlug)->firstOrFail();

        //if it's subdomain routing, laravel will pass subdomain, domain, tls and then page name
        $pageName = $page ? $page : $pageName;

        $this->authorize('show', $project);

        return app(RenderUserSite::class)->execute($project, $pageName);
    }
}
