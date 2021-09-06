<?php

namespace App\Http\Middleware;

use App\Notifications\UserSiteFormSubmitted;
use App\Project;
use App\Services\RenderUserSite;
use Arr;
use Auth;
use Closure;
use Common\Core\AppUrl;
use Common\Domains\CustomDomainController;
use Common\Settings\Settings;
use Illuminate\Http\Request;
use Notification;
use Str;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;

class MaybeRenderUserSite
{
    /**
     * @var Settings
     */
    private $settings;

    /**
     * @var AppUrl
     */
    private $appUrl;

    public function __construct(Settings $settings, AppUrl $appUrl)
    {
        $this->settings = $settings;
        $this->appUrl = $appUrl;
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // allow validating custom domain
        if ($request->path() === CustomDomainController::VALIDATE_CUSTOM_DOMAIN_PATH) {
            return $next($request);
        }

        if ($domain = $this->appUrl->matchedCustomDomain) {
            $project = app(Project::class)->findOrFail($domain->resource_id);
        } else if ($this->settings->get('builder.enable_subdomains') && !$this->appUrl->envAndCurrentHostsAreEqual) {
            try {
                $requestHost = $this->appUrl->getRequestHost();
            } catch (SuspiciousOperationException $e) {
                abort(403, $e->getMessage());
            }
            if (Str::contains($requestHost, '.')) {
                $subdomain = Arr::first(explode('.', $requestHost));
                $project = app(Project::class)->where('slug', $subdomain)->firstOrFail();
            }
        }

        if ($this->urlIsForUserSiteForm($request)) {
            if ( ! isset($project)) {
                $uuid = explode('/', $request->path())[3];
                $project = Project::where('uuid', $uuid)->firstOrFail();
            }

            Notification::route('mail', $project->formsEmail())
                ->notify(new UserSiteFormSubmitted($request->all(), $project));

            if ($request->expectsJson()) {
                return response()->json(['status' => 'success']);
            } else {
                return redirect()->back();
            }
        }

        if (isset($project) && ($project->published || $project->users->contains(Auth::user()))) {
            return response(app(RenderUserSite::class)->execute($project, $request->segment(1)));
        } else {
            return $next($request);
        }
    }

    private function urlIsForUserSiteForm(Request $request): bool
    {
        $path = $request->path();
        return $request->isMethod('post') && Str::endsWith($path, 'default-form-handler');
    }
}
