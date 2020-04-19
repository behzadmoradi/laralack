<?php
namespace App\Jobs;

use App\Mail\ChatPoking;
use App\Models\Poke;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendChatPokingEmailsJob implements ShouldQueue
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
        $pokes = Poke::where('status', 0)->get();
        if (count($pokes) > 0) {
            foreach($pokes as $poke) {
                Mail::to($poke->email)->send(new ChatPoking($poke));
                $poke->status = 1;
                $poke->save();
            }
        } 
    }
}
