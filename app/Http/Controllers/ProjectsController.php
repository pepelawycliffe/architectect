<?php namespace App\Http\Controllers;

use App\Project;
use App\Services\ProjectRepository;
use Common\Core\BaseController;
use Common\Database\Datasource\DatasourceFilters;
use Common\Database\Datasource\MysqlDataSource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProjectsController extends BaseController
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Project
     */
    private $project;

    /**
     * @var ProjectRepository
     */
    private $repository;

    public function __construct(
        Request $request,
        Project $project,
        ProjectRepository $repository
    ) {
        $this->request = $request;
        $this->project = $project;
        $this->repository = $repository;
    }
    public function index()
    {
        $builder = $this->project->with(['domain', 'users']);
        $filters = new DatasourceFilters(
            $this->request->get('filters'),
            $builder->getModel(),
        );
        $userId =
            $this->request->get('user_id') ?? $filters->getAndRemove('user_id');

        $this->authorize('index', [Project::class, $userId]);

        if ($userId) {
            $builder->whereHas('users', function (Builder $q) use ($userId) {
                return $q->where('users.id', $userId);
            });
        }

        if (
            $this->request->has('published') &&
            $this->request->get('published') !== 'all'
        ) {
            $builder->where('published', $this->request->get('published'));
        }

        $datasource = new MysqlDataSource(
            $builder,
            $this->request->all(),
            $filters,
        );

        return $this->success(['pagination' => $datasource->paginate()]);
    }

    public function show($id)
    {
        $project = $this->project->with('pages', 'users')->findOrFail($id);

        $this->authorize('show', $project);

        $project = $this->repository->load($project);

        return $this->success(['project' => $project]);
    }

    public function update(int $id)
    {
        $project = $this->project->with('users')->find($id);

        $this->authorize('update', $project);

        $this->validate($this->request, [
            'name' => 'string|min:3|max:255',
            'css' => 'nullable|string|min:1',
            'js' => 'nullable|string|min:1',
            'template' => 'nullable|string|min:1|max:255',
            'custom_element_css' => 'nullable|string|min:1',
            'published' => 'boolean',
            'pages' => 'array',
            'pages.*' => 'array',
        ]);

        $this->repository->update($project, $this->request->all());

        return $this->success(['project' => $this->repository->load($project)]);
    }

    public function toggleState(Project $project)
    {
        $this->authorize('update', $project);

        $project
            ->fill(['published' => $this->request->get('published')])
            ->save();

        return $this->success(['project' => $project]);
    }

    public function store()
    {
        $this->authorize('store', Project::class);

        $this->validate($this->request, [
            'name' => 'required|string|min:3|max:255|unique:projects',
            'slug' => 'string|min:3|max:30|unique:projects',
            'css' => 'nullable|string|min:1|max:255',
            'js' => 'nullable|string|min:1|max:255',
            'template_name' => 'nullable|string',
            'published' => 'boolean',
        ]);

        $project = $this->repository->create($this->request->all());

        return $this->success(['project' => $this->repository->load($project)]);
    }

    public function destroy(string $ids)
    {
        $projectIds = explode(',', $ids);
        foreach ($projectIds as $id) {
            $project = $this->project->findOrFail($id);

            $this->authorize('destroy', $project);

            $this->repository->delete($project);
        }

        return $this->success();
    }
}
