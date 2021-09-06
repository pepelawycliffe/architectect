<?php namespace App\Http\Controllers;

use Common\Core\BaseController;
use Exception;
use File;
use Symfony\Component\DomCrawler\Crawler;

class ElementsController extends BaseController
{
    public function custom()
    {
        try {
            $files = File::files(public_path('builder/elements'));
            $module = '';

            foreach ($files as $key => $file) {
                $crawler = new Crawler(File::get($file));
                $script = trim(
                    $crawler
                        ->filter('script')
                        ->first()
                        ->html(),
                );
                $template = trim(
                    $crawler
                        ->filter('template')
                        ->first()
                        ->html(),
                );
                $style = trim(
                    $crawler
                        ->filter('style')
                        ->first()
                        ->html(),
                );
                $module .= $script;
                if ($style) {
                    $module .= "export const style$key = `$style`;";
                }
                if ($template) {
                    $module .= "export const template$key = `$template`;";
                }
            }
        } catch (Exception $e) {
            $module = '';
        }

        return response($module)->header('Content-Type', 'text/javascript');
    }
}
