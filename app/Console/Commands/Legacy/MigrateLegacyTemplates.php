<?php

namespace App\Console\Commands\Legacy;

use File;
use Str;
use App\BuilderPage;
use Illuminate\Console\Command;

class MigrateLegacyTemplates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'legacy:templates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate legacy templates to to version.';

    /**
     * @var BuilderPage
     */
    private $builderPage;

    /**
     * Create a new command instance.
     *
     * @param BuilderPage $builderPage
     */
    public function __construct(BuilderPage $builderPage)
    {
        parent::__construct();

        $this->builderPage = $builderPage;
    }

    /**
     * Execute the console command.
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        //on disk
        $path = config('filesystems.disks.public.root').'/templates';
        $templates = File::directories($path);

        foreach ($templates as $templatePath) {
            $paths = File::AllFiles($templatePath);

            collect($paths)->filter(function($path) {
                return Str::contains($path, '.html');
            })->each(function($path) {
                File::put($path, $this->fixHtmlImagePaths(File::get($path)));
            });

            collect($paths)->filter(function($path) {
                return Str::contains($path, '.css');
            })->each(function($path) {
                File::put($path, $this->fixCssImagePaths(File::get($path)));
            });

            //convert config file from php to json
            $config = \File::getRequire("$templatePath/config.php");
            $config['framework'] = 'bootstrap-3';
            $json = json_encode($config, JSON_PRETTY_PRINT);
            File::put("$templatePath/config.json", $json);
            File::delete("$templatePath/config.php");
        }

        $this->info('Fixed legacy templates.');
    }

    /**
     * @param string $html
     * @return string
     */
    public function fixHtmlImagePaths($html)
    {
        return preg_replace("/templates\/.+?\/(images\/.+?)/i", "$1", $html);
    }

    /**
     * @param string css
     * @return string
     */
    public function fixCssImagePaths($css)
    {
        return preg_replace("/url\(templates\/.+?\/(images\/.+?)\)/i", 'url("../$1")', $css);
    }
}
