<?php

namespace App\Services;

use App\Project;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Str;
use Symfony\Component\DomCrawler\Crawler;

class RenderUserSite
{
    /**
     * @var ProjectRepository
     */
    private $repository;
    /**
     * @var Request
     */
    private $request;

    public function __construct(ProjectRepository $repository, Request $request)
    {
        $this->repository = $repository;
        $this->request = $request;
    }

    public function execute(Project $project, string $pageName = 'index')
    {
        try {
            $html = $this->repository->getPageHtml($project, $pageName);
            $html = preg_replace('/<base.href=".+?">/', '', $html);
            return $this->replaceRelativeLinks($project, $html);
        } catch (FileNotFoundException $e) {
            abort(404);
        }
    }

    private function replaceRelativeLinks(Project $project, string $html)
    {
        $assetBaseUri = url(
            "storage/projects/{$project->users->first()->id}/$project->uuid",
        );
        $pathParts = explode('/', $this->request->path());
        $crawler = new Crawler($html);

        // on "sites/xxx" version project homepage with no page name in url, need to prefix all links with project name
        if (count($pathParts) === 2 && $pathParts[0] == 'sites') {
            $aHref = $crawler->filter('a')->extract(['href']);
            $html = $this->prefixAssetUrls($html, $aHref, $pathParts[1]);
        }

        $styleLinks = $crawler->filter('link')->extract(['href']);
        $html = $this->prefixAssetUrls($html, $styleLinks, $assetBaseUri);

        $scriptSrc = $crawler->filter('script')->extract(['src']);
        $html = $this->prefixAssetUrls($html, $scriptSrc, $assetBaseUri);

        $formActions = $crawler->filter('form')->extract(['action']);
        $html = $this->prefixAssetUrls($html, $formActions, $assetBaseUri);

        $imgSrc = $crawler->filter('img')->extract(['src']);
        $html = $this->prefixAssetUrls($html, $imgSrc, $assetBaseUri);

        $stylesWithUrl = collect(
            $crawler->filter('*[style*="url("]')->extract(['style']),
        );
        $styleUrls = $stylesWithUrl
            ->flatMap(function ($cssStyles) {
                return explode(';', $cssStyles);
            })
            ->flatMap(function ($cssPropAndValue) {
                return explode(':', $cssPropAndValue, 2);
            })
            ->map(function ($cssValue) {
                return trim($cssValue);
            })
            ->filter(function ($cssValue) {
                return Str::startsWith($cssValue, 'url');
            })
            ->map(function ($valueWithUrl) {
                $valueWithUrl = preg_replace('/^url\(/', '', $valueWithUrl);
                return trim($valueWithUrl, ')"');
            });
        return $this->prefixAssetUrls(
            $html,
            $styleUrls->toArray(),
            $assetBaseUri,
        );
    }

    private function prefixAssetUrls(string $html, array $urls, string $baseUri)
    {
        foreach (array_unique($urls) as $url) {
            $url = str_replace('"', '', htmlspecialchars_decode($url));

            // prefix only if not already absolute url
            if ($url && !Str::startsWith($url, ['//', 'http'])) {
                $html = str_replace($url, "$baseUri/$url", $html);
            }
        }

        return $html;
    }
}
