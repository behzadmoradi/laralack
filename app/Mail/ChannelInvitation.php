<?php
namespace App\Mail;

use App\Models\Invitation;
// use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ChannelInvitation extends Mailable
{
    use Queueable, SerializesModels;

    protected $invitation;

    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }

    public function build()
    {
        if ($this->invitation->link) {
            $invitationLink = $this->invitation->link;
        } else {
            $invitationLink = config('app.url') . '/login';
        }
        return $this->markdown('emails.invitation')->with([
            'link' => $invitationLink,
            'user_name' => $this->invitation->user->name
        ]);
    }
}