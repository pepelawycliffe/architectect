<?php

namespace App\Console\Commands\Legacy;

use App\BuilderPage;
use App\Project;
use App\Services\ProjectRepository;
use App\Services\TemplateLoader;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Arr;
use Str;

class MigrateLegacyProjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'legacy:projects';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate legacy projects to new version.';

    /**
     * @var Project
     */
    private $project;

    /**
     * @var ProjectRepository
     */
    private $repository;

    /**
     * @var BuilderPage
     */
    private $page;

    /**
     * @var TemplateLoader
     */
    private $templateLoader;

    /**
     * Map for matching template css to template name.
     * @var array
     */
    private $templateCssMap = [
        'capital-city' => 'move special fonts to html head for better performance',
        'pratt' => '#features .ac a',
        'storystrap' => 'h1,h2,h3,.highlight,.navbar a,#masthead h4',
        'minimal-blog' => '#head a.logo,#head a.logo:hover',
        'product-launch' => '.icon-home a, .icon-home a:hover, .icon-home a:focus',
    ];

    /**
     * Create a new command instance.
     *
     * @param Project $project
     * @param BuilderPage $page
     * @param ProjectRepository $repository
     * @param TemplateLoader $templateLoader
     */
    public function __construct(Project $project, BuilderPage $page, ProjectRepository $repository, TemplateLoader $templateLoader)
    {
        parent::__construct();

        $this->page = $page;
        $this->project = $project;
        $this->repository = $repository;
        $this->templateLoader = $templateLoader;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //update model namespaces in database
        $this->page->where('pageable_type', 'Project')->update(['pageable_type' => Project::class]);
        $this->page->where('pageable_type', 'Template')->update(['pageable_type' => 'App\Template']);

        $this->project->with('pages', 'users')->orderBy('id')->chunk(100, function(Collection $projects)  {
            $projects->each(function(Project $project) {
                if ($project->uuid) return;

                //add uuid to legacy projects
                $project->fill(['uuid' => Str::random(36), 'framework' => 'temp'])->save();

                $templateNames = $this->templateLoader->loadAll()->pluck('name');

                $data = $project->toArray();
                $data['framework'] = 'bootstrap-3';

                if ($project->pages->isNotEmpty()) {
                    $data['theme'] = $project->pages->first()->theme;
                    $css = strtolower($project->pages->first()->css);

                    //extract template name from project css
                    $data['template'] = $templateNames->first(function($name) use($project, $css) {
                        if ($name === 'grayscale') $name = 'grayscale bootstrap theme';
                        if ($name === 'minimal-dark') $name = 'project name: minimal';
                        return Str::contains($css, str_replace('-', ' ', $name));
                    });

                    //match templates that don't have their name in css
                   if ( ! Arr::get($data, 'template')) {
                       foreach ($this->templateCssMap as $name => $cssPart) {
                           if (Str::contains($css, $cssPart)) {
                               $data['template'] = $name; break;
                           }
                       }
                   }

                    //remove "templates/name" references from html
                    if (Arr::get($data, 'template')) {
                        $data['pages'] = array_map(function($page) use($data) {
                            $page['html'] = str_replace("templates/{$data['template']}/", '', $page['html']);
                            return $page;
                        }, $data['pages']);
                    }

                    //compile css of all pages into single string
                    //that should be inserted into custom_css file
                    $data['css'] = $project->pages->map(function($page) {
                        return $page->css;
                    })->implode("\n");

                    //remove "templates/name" references from css
                    if (Arr::get($data, 'template')) {
                        $data['css'] = str_replace("templates/{$data['template']}/", '../', $data['css']);
                    }

                    //compile js of all pages into single string
                    //that should be inserted into custom_js file
                    $data['js'] = $project->pages->map(function($page) {
                        return $page->js;
                    })->implode("\n");

                    //need to include css of all custom elements
                    $files = \File::files(public_path('builder/elements'));

                    $customElementsCss = array_map(function($path) {
                        $contents = \File::get($path);
                        preg_match('/<style.*?>(.+?)<\/style>/s', $contents, $css);
                        return isset($css[1]) ? trim($css[1]) : '';
                    }, $files);

                    $data['custom_element_css'] = implode("\n", $customElementsCss);
                }

                $this->repository->update($project, $data, false);

                //store default thumbnail, if needed
                $thumbnailPath = $this->repository->getProjectPath($project->fresh()) . '\thumbnail.png';
                if ( ! \Storage::disk('public')->exists($thumbnailPath)) {
                    \Storage::disk('public')->put($thumbnailPath, \File::get(public_path(TemplateLoader::DEFAULT_THUMBNAIL)));
                }
            });
        });

        $this->info('Migrated legacy projects.');
    }
}
