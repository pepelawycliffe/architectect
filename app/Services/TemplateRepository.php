<?php namespace App\Services;

use Chumper\Zipper\Zipper;
use Composer\Package\Archiver\ZipArchiver;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Storage;
use Str;
use Arr;

class TemplateRepository
{
    /**
     * @param array $params
     * @throws FileNotFoundException
     */
    public function create($params)
    {
        $name = isset($params['name']) ? $params['name'] : $params['display_name'];
        $this->update($name, $params);
    }

    /**
     * @param string $name
     * @param array $params
     * @throws FileNotFoundException
     * @throws Exception
     */
    public function update($name, $params)
    {
        $name = slugify($name);
        $templatePath = "templates/$name";
        $storage = Storage::disk('builder');

        // extract template files
        if (isset($params['template'])) {

            $zip = new \ZipArchive();
            $zip->open($params['template']->getRealPath());
            $zip->extractTo($storage->path($templatePath));
            $zip->close();

            // if there are multiple index.html files, get the one that is closest to root
            $indexFilePath = collect($storage->allFiles($templatePath))->filter(function($path) {
                return Str::contains($path, 'index.html');
            })->sortBy(function($path) {
                return substr_count($path, '/');
            })->first();

            if ( ! $indexFilePath) {
                // make sure there is always an index.html file in template folder
                $storage->put("$templatePath/index.html", 'Could not find index.html file in the template, so this file was created automatically.');
            } else {
                // move template files to root if they were nested inside .zip file
                $nestedIndexFolder = str_replace('/index.html', '', $indexFilePath);
                $nestedIndexFolder = trim(str_replace($templatePath, '', $nestedIndexFolder), '/');
                if ($nestedIndexFolder) {
                    foreach ($storage->allFiles("$templatePath/$nestedIndexFolder") as $oldPath) {
                        $newPath = preg_replace("/$nestedIndexFolder/", '', $oldPath, 1);
                        $newPath = str_replace('//', '/', $newPath);
                        $storage->move($oldPath, $newPath);
                    }
                    $storage->deleteDirectory("$templatePath/$nestedIndexFolder");
                }
            }
        }

        // load config file if it exists
        $configPath = "$templatePath/config.json";
        $config = [];
        if ($storage->exists($configPath)) {
            $config = json_decode($storage->get($configPath), true);
        }

        // update config file
        foreach (Arr::except($params, ['template', 'thumbnail']) as $key => $value) {
            $config[$key] = castToBoolean($value);
        }
        $storage->put($configPath, json_encode($config, JSON_PRETTY_PRINT));

        // update thumbnail
        if (isset($params['thumbnail'])) {
            $storage->put("$templatePath/thumbnail.png", file_get_contents($params['thumbnail']));
        }
    }

    /**
     * Delete specified templates.
     *
     * @param array $names
     */
    public function delete($names)
    {
        foreach ($names as $name) {
            Storage::disk('builder')->deleteDirectory("templates/$name");
        }
    }
}
