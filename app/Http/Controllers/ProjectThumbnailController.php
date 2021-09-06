<?php

namespace App\Http\Controllers;

use App\Project;
use Common\Core\BaseController;
use Illuminate\Http\Request;
use Image;
use Intervention\Image\Constraint;
use Storage;

class ProjectThumbnailController extends BaseController
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Project
     */
    private $project;

    public function __construct(Request $request, Project $project)
    {
        $this->request = $request;
        $this->project = $project;
    }

    public function store($projectId)
    {
        $project = $this->project->find($projectId);

        $this->authorize('update', $project);

        $userId = $this->request->user()->id;
        $path = "$userId/$project->uuid/thumbnail.png";
        $thumbnail = $this->generateThumbnail();

        Storage::disk('projects')->put($path, $thumbnail);

        return $this->success(['path' => $path]);
    }

    private function generateThumbnail(): string
    {
        $string = preg_replace('/data:image\/.+?;base64,/', '', $this->request->get('dataUrl'));

        $img = Image::make(base64_decode($string));

        $img->fit(385, 240);

        return $img->encode('jpg', 100);
    }
}
