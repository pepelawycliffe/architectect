<?php

namespace App\Mail;

use App\Project;
use Arr;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserSiteFormMailable extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var array
     */
    public $data;

    /**
     * @var Project
     */
    public $project;

    public function __construct(array $data, Project $project)
    {
        $this->data = $data;
        $this->project = $project;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $siteName = config('app.name');
        $fromName = Arr::get($this->data, 'name');

        $subject = "$siteName Form: $fromName";
        $body = "You have received a new message from {$this->project->name} form on $siteName.\n\n"."Here are the details:\n";

        foreach ($this->data as $key => $value) {
            $key = ucfirst($key);
            $body .= "\n\n$key: $value";
        }

        $email = $this->project->users->first()->email;

        return $this->view('view.name')
            ->subject($subject)
            ->to($email)
            ->replyTo($email);
    }
}
