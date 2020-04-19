<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //TODO
    public static function unreadCount()
    {
        return self::where('is_seen', 0)->where('is_seen', 0)->count();
    }
}
