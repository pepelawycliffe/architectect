<?php namespace App\Http\Controllers;

use App\Services\TemplateLoader;
use App\Services\TemplateRepository;
use Common\Core\BaseController;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Str;

class TemplatesController extends BaseController
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var TemplateLoader
     */
    private $templateLoader;

    /**
     * @var TemplateRepository
     */
    private $repository;

    public function __construct(
        Request $request,
        TemplateLoader $templateLoader,
        TemplateRepository $repository
    ) {
        $this->request = $request;
        $this->repository = $repository;
        $this->templateLoader = $templateLoader;
    }

    public function index()
    {
        $this->authorize('index', 'Template');

        $templates = $this->templateLoader->loadAll();

        $perPage = $this->request->get('perPage', 15);
        $page = $this->request->get('page', 1);

        if ($this->request->get('query')) {
            $templates = $templates->filter(function ($template) {
                return Str::contains(
                    strtolower($template['name']),
                    $this->request->get('query'),
                );
            });
        }

        if ($orderBy = $this->request->get('order_by', 'updated_at')) {
            $desc = $this->request->get('order_dir', 'desc') === 'desc';
            $templates = $templates->sortBy($orderBy, SORT_REGULAR, $desc);
        }

        $pagination = new LengthAwarePaginator(
            $templates->slice($perPage * ($page - 1), $perPage)->values(),
            count($templates),
            $perPage,
            $page,
        );

        return $this->success(['pagination' => $pagination]);
    }

    public function show(string $name)
    {
        $this->authorize('show', 'Template');

        try {
            $template = $this->templateLoader->load($name);
        } catch (FileNotFoundException $exception) {
            return abort(404);
        }

        return $this->success(['template' => $template]);
    }

    public function store()
    {
        $this->authorize('store', 'Template');

        $this->validate($this->request, [
            'name' => 'required|string|min:1|max:255',
            'category' => 'required|string|min:1|max:255',
            'template' => 'required|file|mimes:zip',
            'thumbnail' => 'file|image',
        ]);

        $params = $this->request->except('template');
        $params['template'] = $this->request->file('template');
        $params['thumbnail'] = $this->request->file('thumbnail');

        if ($this->templateLoader->exists($params['name'])) {
            return $this->error('', [
                'name' => 'Template with this name already exists.',
            ]);
        }

        $this->repository->create($params);

        return $this->success([
            'template' => $this->templateLoader->load($params['name']),
        ]);
    }

    public function update(string $name)
    {
        $this->authorize('update', 'Template');

        $this->validate($this->request, [
            'name' => 'string|min:1|max:255',
            'category' => 'string|min:1|max:255',
            'template' => 'file|mimes:zip',
            'thumbnail' => 'file|image',
        ]);

        $params = $this->request->except('template');
        $params['template'] = $this->request->file('template');
        $params['thumbnail'] = $this->request->file('thumbnail');

        $this->repository->update($name, $params);

        return $this->success([
            'template' => $this->templateLoader->load($name),
        ]);
    }

    public function destroy()
    {
        $this->authorize('destroy', 'Template');

        $this->repository->delete($this->request->get('names'));

        return $this->success();
    }
}
