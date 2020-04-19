<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatUser extends Model
{
    protected $fillable = [
        'user_id',
        'recipient_id',
    ];

    protected $table = 'chat_user';

    public $primary = 'user_id';
}