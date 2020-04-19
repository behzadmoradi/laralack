<?php
namespace App\Mail;

use App\Models\Poke;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ChatPoking extends Mailable
{
    use Queueable, SerializesModels;

    public $poke;
    public function __construct(Poke $poke)
    {
        $this->poke = $poke;
    }

    public function build()
    {
        return $this->markdown('emails.poke')->with([
            'link' => config('app.url') . '/login',
            'user_name' => $this->poke->user->name,
        ]);
    }
}
