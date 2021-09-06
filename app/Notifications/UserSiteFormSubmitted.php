<?php

namespace App\Notifications;

use App\Project;
use Arr;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserSiteFormSubmitted extends Notification
{
    use Queueable;

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
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        $siteName = config('app.name');
        $fromName = Arr::get($this->data, 'name');
        $replyTo = Arr::get($this->data, 'email');

        $subject = "$siteName Form: $fromName";
        $intro = "You have received a new message from your :projectName project form on :siteName.\n\n"."Here are the details:\n";
        $body = __($intro, ['projectName' => $this->project->name, 'siteName' => $siteName]);

        $message = (new MailMessage)
            ->subject($subject)
            ->line($body);

        foreach ($this->data as $key => $value) {
            $key = ucfirst($key);
            $message->line("$key: $value");
        }

        if ($replyTo) {
            $message->replyTo($replyTo);
        }

        return $message->action(__('View Project'), url('/dashboard'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
