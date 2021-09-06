<?php namespace App\Http\Controllers;

use App\Project;
use App\Services\ProjectRepository;
use Common\Core\BaseController;
use Exception;
use Illuminate\Http\Request;
use League\Flysystem\Adapter\Ftp as Adapter;
use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use Storage;

class ExportProjectToFTPController extends BaseController
{
    /**
     * Request instance.
     *
     * @var Request
     */
    private $request;

    /**
     * @var ProjectRepository
     */
    private $repository;

    public function __construct(Request $request, ProjectRepository $repository)
    {
        $this->request = $request;
        $this->repository = $repository;
    }

    public function export(Project $project)
    {
        $this->authorize('publish', $project);

        $this->validate($this->request, [
            'host' => 'required|string|min:1',
            'username' => 'required|string|min:1',
            'password' => 'string|min:1|nullable',
            'port' => 'integer|min:1',
            'root' => 'string|min:1',
            'ssl' => 'boolean',
        ]);

        try {
            $this->exportToFTP($project);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }

        return $this->success();
    }

    private function exportToFTP(Project $project)
    {
        $directory = $this->request->get('directory');

        $ftp = new Filesystem(
            new Adapter([
                'host' => $this->request->get('host'),
                'username' => $this->request->get('username'),
                'password' => $this->request->get('password'),
                'port' => $this->request->get('port', $this->getDefaultPort()),
                'passive' => true,
                'ssl' => $this->request->get('ssl', false),
                'timeout' => 30,
            ]),
        );

        $manager = new MountManager([
            'ftp' => $ftp,
            'local' => Storage::disk('projects')->getDriver(),
        ]);

        if ($directory && !$ftp->has($directory)) {
            $ftp->createDir($directory);
        }

        if ($this->request->get('remember')) {
            $settings = $project->settings;
            $settings['ftpCredentials'] = $this->request->all();
            $project->fill(['settings' => $settings])->save();
        }

        $projectRoot = $this->repository->getProjectPath($project);

        foreach (
            $manager->listContents("local://$projectRoot", true)
            as $file
        ) {
            if ($file['type'] !== 'file') {
                continue;
            }
            $filePath = str_replace($projectRoot, $directory, $file['path']);

            // delete old files from ftp
            if ($ftp->has($filePath)) {
                $ftp->delete($filePath);
            }

            // copy file from local disk to ftp
            $manager->copy('local://' . $file['path'], 'ftp://' . $filePath);
        }
    }

    private function getDefaultPort(): int
    {
        return $this->request->get('ssl') ? 22 : 21;
    }
}
