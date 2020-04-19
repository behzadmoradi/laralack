<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $fillable = [
        'user_id',
        'channel_id',
        'email',
        'link',
        'status',
        'already_registered',
        'is_sent',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
