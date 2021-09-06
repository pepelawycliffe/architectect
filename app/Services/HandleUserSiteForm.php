<?php


namespace App\Services;


use App\Notifications\UserSiteFormSubmitted;
use App\Project;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Mail;

class HandleUserSiteForm
{
    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function execute(Project $project)
    {


    }
}
