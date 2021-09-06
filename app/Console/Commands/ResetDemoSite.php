<?php namespace App\Console\Commands;

use App\Project;
use App\Services\ProjectRepository;
use App\Services\TemplateLoader;
use App\User;
use Artisan;
use Common\Auth\Permissions\Permission;
use Common\Localizations\Localization;
use Hash;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class ResetDemoSite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset demo site.';

    /**
     * @var User
     */
    private $user;
    /**
     * @var Localization
     */
    private $localization;

    /**
     * @var ProjectRepository
     */
    private $projectRepository;

    /**
     * @var Project
     */
    private $project;

    public function __construct(
        User $user,
        Localization $localization,
        ProjectRepository $projectRepository,
        Project $project
    ) {
        parent::__construct();

        $this->user = $user;
        $this->localization = $localization;
        $this->projectRepository = $projectRepository;
        $this->project = $project;
    }

    /**
     * @return void
     */
    public function handle()
    {
        if ( ! config('common.site.demo')) return;

        /** @var User $admin */
        $admin = $this->user->where('email', 'admin@admin.com')->firstOrFail();

        $admin->avatar = null;
        $admin->first_name = 'Demo';
        $admin->last_name = 'Admin';
        $admin->password = Hash::make('admin');

        $adminPermission = app(Permission::class)
            ->where('name', 'admin')
            ->first();
        $admin->permissions()->syncWithoutDetaching([$adminPermission->id]);

        $admin->save();

        $admin->subscriptions()->delete();

        //delete projects
        $this->project
            ->orderBy('id')
            ->chunk(50, function (Collection $projects) {
                $projects->each(function (Project $project) {
                    $this->projectRepository->delete($project);
                });
            });

        //create some demo projects
        app(TemplateLoader::class)
            ->loadAll()
            ->sortBy('updated_at', SORT_REGULAR, 'desc')
            ->take(9)
            ->reverse()
            ->values()
            ->each(function ($template, $key) use ($admin) {
                app(ProjectRepository::class)->create([
                    'name' => 'Demo ' . (9 - $key),
                    'userId' => $admin->id,
                    'template_name' => $template['name'],
                    'published' => true,
                    'updated_at' => now()->addMinutes($key),
                ]);
            });

        //delete localizations
        $this->localization->get()->each(function (Localization $localization) {
            if (strtolower($localization->name) !== 'english') {
                $localization->delete();
            }
        });

        Artisan::call('cache:clear');

        $this->info('Demo site reset.');
    }
}
