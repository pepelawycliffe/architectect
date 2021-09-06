<?php

namespace App\Http\Controllers;

use App\Project;
use App\Services\ProjectRepository;
use Carbon\Carbon;
use Common\Core\BaseController;
use Storage;
use ZipStream\Option\Archive;
use ZipStream\ZipStream;

class ProjectDownloadController extends BaseController
{
    /**
     * @var ProjectRepository
     */
    private $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function download(Project $project)
    {
        $projectPath = $this->projectRepository->getProjectPath($project);
        $disk = Storage::disk('projects');

        $this->authorize('download', $project);

        $options = new Archive();
        $options->setSendHttpHeaders(true);

        $timestamp = Carbon::now()->getTimestamp();
        $zip = new ZipStream("download-$timestamp.zip", $options);

        $paths = $disk->allFiles($projectPath);
        foreach ($paths as $relativePath) {
            $zip->addFileFromPath(
                str_replace($projectPath, '', $relativePath),
                $disk->path($relativePath),
            );
        }

        $zip->finish();
    }
}
