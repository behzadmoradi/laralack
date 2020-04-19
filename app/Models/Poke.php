<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poke extends Model
{
    protected $fillable = [
        'user_id',
        'recipient_id',
        'email',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
