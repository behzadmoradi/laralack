<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Invitation;
use App\Mail\ChannelInvitation;
use Illuminate\Support\Facades\Mail;

class SendInvitationEmailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $invitations = Invitation::where('is_sent', 0)->get();
        if (count($invitations) > 0) {
            foreach($invitations as $invitation) {
                Mail::to($invitation->email)->send(new ChannelInvitation($invitation));
                //TODO
                // Need to make sure the email is properly sent
                $invitation->is_sent = 1;
                $invitation->save();
            }
        } 
    }
}
