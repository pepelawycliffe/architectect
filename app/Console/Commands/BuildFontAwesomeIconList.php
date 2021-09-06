<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BuildFontAwesomeIconList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fa:build';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build a list of all available font awesome icons.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->fromFontAwesomeSite();
    }

    private function fromFontAwesomeSite()
    {
        $html = file_get_contents(public_path('builder/font-awesome/from-site.html'));

        preg_match_all('/<i class="(.+?)">/', $html, $matches);

        $matches = array_map(function($match) {
            $match = explode(' ', $match)[1];
            return "fa $match";
        }, $matches[1]);

        $content = 'export const fontAwesomeIconsList = ' . str_replace('"', "'", trim(json_encode($matches), '"')) . ';';

        file_put_contents(base_path('../client/src/app/html-builder/font-awesome-icons-list.ts'), $content);
    }

    private function fromCssFile()
    {
        $css = file_get_contents(public_path('builder/font-awesome/font-awesome.min.css'));
        preg_match_all('/\.(fa-[a-z-]+):before{/', $css, $matches);

        $matches = array_map(function($match) {
            return "fa $match";
        }, $matches[1]);

        $content = 'export const fontAwesomeIconsList = ' . str_replace('"', "'", trim(json_encode($matches), '"')) . ';';

        file_put_contents(base_path('../client/src/app/html-builder/font-awesome-icons-list.ts'), $content);
    }
}
