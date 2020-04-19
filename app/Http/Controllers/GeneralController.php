<?php
namespace App\Http\Controllers;

use App\Mail\ChannelInvitation;
use App\Mail\ChatPoking;
use App\Models\Invitation;
use App\Models\Poke;
use Illuminate\Support\Facades\Mail;

class GeneralController extends Controller
{
    public function homepage()
    {
        return view('welcome');
    }

    public function workspace()
    {
        return view('workspace.index');
    }

    /*
    This function needs to be removed
     */
    public function mailTester()
    {
        // $invitation = Invitation::find(2);
        // Mail::to($invitation->email)->send(new ChannelInvitation($invitation));
        
        // $poke = Poke::find(1);
        // return new ChatPoking($poke);

    

        $invitations = Invitation::where('is_sent', 0)->get();
        if (count($invitations) > 0) {
            foreach ($invitations as $invitation) {
                //show in the browser
                return new ChannelInvitation($invitation);
                // Mail::to($invitation->email)->send(new ChannelInvitation($invitation));
                // $invitation->is_sent = 1;
                // $invitation->save();
            }
        }
    }
}
