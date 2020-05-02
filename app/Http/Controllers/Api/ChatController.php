<?php
namespace App\Http\Controllers\Api;

use App\Events\ChatMessageEvent;
use App\Events\StartChatEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\MessageForm;
use App\Models\Channel;
use App\Models\Chat;
use App\Models\ChatUser;
use App\Models\Poke;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// https://github.com/erusev/parsedown
use Parsedown;

class ChatController extends Controller
{
    public function fetchChatsByUserId(int $userId)
    {
        $userId = $this->clean($userId);
        if ($userId != Auth::user()->id) {
            return $this->jsonResponse([
                'message' => 'Not Acceptable',
            ], 406, false);
        } else {
            $chats = ChatUser::where('user_id', $userId)
                ->join('users', 'users.id', '=', 'chat_user.recipient_id')
                ->select('users.id', 'users.username', 'users.email')
                ->get();
            if (count($chats) > 0) {
                $result = [];
                foreach ($chats as $key => $chat) {
                    $result[$key]['id'] = $chat->id;
                    $result[$key]['username'] = $chat->username;
                    $result[$key]['email'] = $chat->email;
                    $result[$key]['count'] = Chat::where('user_id', $chat->id)->where('is_seen', 0)->get()->count();
                }
                return $this->jsonResponse([
                    'chats' => $result,
                ]);
            }
        }
    }

    public function messagesByRecipientId(int $userId, int $recipientId)
    {
        if ($userId != Auth::user()->id) {
            return $this->jsonResponse([
                'message' => 'Not Acceptable',
            ], 406, false);
        } else {
            $recipientId = $this->clean($recipientId);
            $userId = $this->clean($userId);
            $allMessages = Chat::where('user_id', $userId)
                ->where('recipient_id', $recipientId)
                ->orWhere(function ($query) use ($recipientId, $userId) {
                    $query->where('user_id', $recipientId)
                        ->where('recipient_id', $userId);
                })->with(['user' => function ($query) {
                $query->select('id', 'name', 'username', 'avatar');
            }])->get();
            $messages = [];
            if ($allMessages) {
                Chat::where('user_id', $recipientId)->where('is_seen', 0)->update(['is_seen' => 1]);
                $imgRoute = '/img/avatars/';
                foreach ($allMessages as $index => $msg) {
                    $messages[$index]['id'] = $msg->id;
                    $messages[$index]['user_id'] = $msg->user_id;
                    $messages[$index]['user_name'] = $msg->user->name;
                    $messages[$index]['user_avatar'] = ($msg->user->avatar ? $msg->user->avatar : $imgRoute . 'default-avatar.png');
                    $messages[$index]['message'] = $msg->message;
                    $messages[$index]['updated_at'] = $msg->updated_at->format('F j, Y, g:i a');
                }
            }
            return $this->jsonResponse([
                'messages' => $messages,
            ]);
        }
    }

    public function fetchRecipientsByUserId(int $userId)
    {
        $userId = $this->clean($userId);
        if ($userId != Auth::user()->id) {
            return $this->jsonResponse([
                'message' => 'Not Acceptable',
            ], 406, false);
        } else {
            $allChannels = User::find($userId)->channels()->get();
            if (count($allChannels) > 0) {
                $users = [];
                foreach ($allChannels as $channel) {
                    foreach (Channel::find($channel->id)->users()->where('id', '<>', $userId)->select('id', 'email', 'username', 'name')->get() as $user) {
                        $users[] = $user;
                    }
                }
                $users = $this->makeArrayValuesUnique($users, 'email');
                $users = array_values($users);
                $chats = ChatUser::where('user_id', $userId)
                    ->join('users', 'users.id', '=', 'chat_user.recipient_id')
                    ->select('users.id', 'users.username', 'users.email')
                    ->get();
                $finalUsersList = [];
                if (count($chats) > 0) {
                    foreach ($users as $index => $user) {
                        foreach ($chats as $chat) {
                            $finalUsersList[$index] = $user;
                            unset($finalUsersList[$index]['pivot']);
                            if ($user->id == $chat->id) {
                                $finalUsersList[$index]['already_created'] = true;
                            }
                        }
                    }
                } else {
                    $finalUsersList = $users;
                }
                return $this->jsonResponse([
                    'users' => $finalUsersList,
                ]);
            } else {
                return $this->jsonResponse([
                    'message' => 'No Content',
                    'users' => [],
                ]);
            }
        }
    }

    public function startChat(Request $request)
    {
        $this->setDelay();
        $data = [];
        $loggedinUserId = Auth::user()->id;
        $userId = (int) $this->clean($request->input('user_id'));
        $alreadyExists = ChatUser::where('user_id', $userId)->orWhere('recipient_id', $userId)->get();
        if (count($alreadyExists) > 0) {
            if (!$alreadyExists->contains('user_id', $loggedinUserId)) {
                $data[0]['user_id'] = $loggedinUserId;
                $data[0]['recipient_id'] = $userId;
            }
        } else {
            $data[0]['user_id'] = $loggedinUserId;
            $data[0]['recipient_id'] = $userId;
            $data[1]['user_id'] = $userId;
            $data[1]['recipient_id'] = $loggedinUserId;
        }
        ChatUser::insert($data);

        broadcast(new StartChatEvent($userId, [
            'id' => Auth::user()->id,
            'name' => Auth::user()->name,
            'username' => Auth::user()->username,
        ]))->toOthers();

        return $this->jsonResponse([
            'message' => 'Chatrooms created',
            'recipient_info' => User::find($userId, ['id', 'name', 'username']),
        ]);
    }

    public function makeMessagesRead(int $userId, int $recipientId)
    {
        Chat::where('recipient_id', $recipientId)->where('user_id', $userId)->where('is_seen', 0)->update(['is_seen' => 1]);
        return $this->jsonResponse([
            'message' => 'Made all messages read.',
        ]);
    }

    //TODO
    // check if the user is allowed to store the msg
    public function storeChatMessage(MessageForm $request, int $userId, int $recipientId)
    {
        $validData = $request->validated();
        $notAllowed = false;
        if ($userId != Auth::user()->id) {
            $notAllowed = true;
        } else {
            if ($validData['type'] == 'chat') {
                $imgRoute = '/img/avatars/';
                $message = new Chat();
                $parsedown = new Parsedown();
                $parsedown->setSafeMode(true);
                $message->message = $parsedown->line($validData['message']);
                $message->user_id = $userId;
                $message->recipient_id = $recipientId;
                if ($request->input('parent_id')) {
                    $message->parent_id = (int) $this->clean($validData['parent_id']);
                }
                $message->save();
                $newlyAddedMessage = $message->refresh();
                $messageInfo = [
                    'id' => $newlyAddedMessage->id,
                    'user_id' => $newlyAddedMessage->user_id,
                    'user_name' => $newlyAddedMessage->user->name,
                    'user_avatar' => ($newlyAddedMessage->user->avatar ? $newlyAddedMessage->user->avatar : $imgRoute . 'default-avatar.png'),
                    'message' => $newlyAddedMessage->message,
                    'updated_at' => $newlyAddedMessage->updated_at->format('F j, Y, g:i a'),
                ];
                broadcast(new ChatMessageEvent($messageInfo, $userId, $recipientId))->toOthers();
                return $this->jsonResponse([
                    'new_message_is_added' => true,
                    'message' => 'Your new message is successfully created.',
                    'message_info' => $messageInfo,
                ]);
            } else {
                $notAllowed = true;
            }
        }
        if ($notAllowed) {
            return $this->jsonResponse([
                'message' => 'Not Acceptable',
            ], 406, false);
        }
    }

    public function deleteChat(int $userId, int $recipientId)
    {
        $this->setDelay();
        $notAllowed = false;
        if ($userId != Auth::user()->id) {
            $notAllowed = true;
        } else {
            $allChannelsByUserId = User::find(Auth::user()->id)->channels()->orderBy('id', 'DESC')->get();
            $chatUserIndex = ChatUser::where('user_id', $userId)
                ->where('recipient_id', $recipientId)
                ->orWhere(function ($query) use ($recipientId, $userId) {
                    $query->where('user_id', $recipientId)
                        ->where('recipient_id', $userId);
                })->get();
            $count = count($chatUserIndex);
            if ($count > 0) {
                if ($count == 2) {
                    ChatUser::where('user_id', $userId)->where('recipient_id', $recipientId)->delete();
                } else if ($count == 1) {
                    ChatUser::where('user_id', $userId)->where('recipient_id', $recipientId)->delete();
                    Chat::where('user_id', $userId)
                        ->where('recipient_id', $recipientId)
                        ->orWhere(function ($query) use ($recipientId, $userId) {
                            $query->where('user_id', $recipientId)
                                ->where('recipient_id', $userId);
                        })->delete();
                }
            } else {
                $notAllowed = true;
            }
        }
        if ($notAllowed) {
            return $this->jsonResponse([
                'message' => 'Not Acceptable',
            ], 406, false);
        } else {
            return $this->jsonResponse([
                'chat_is_deleted' => true,
                'message' => 'This conversation is successfully deleted.',
                'recipient_id' => $recipientId,
                'channels' => $allChannelsByUserId,
            ]);
        }
    }

    public function pokeByUserId(int $userId)
    {
        $this->setDelay();
        $userId = (int) $this->clean($userId);
        $allChannels = User::find(Auth::user()->id)->channels()->get();
        foreach ($allChannels as $channel) {
            foreach (Channel::find($channel->id)->users()->where('id', '<>', Auth::user()->id)->pluck('id')->toArray() as $user) {
                $users[] = $user;
            }
        }
        $users = $this->makeArrayValuesUnique($users, 'id');
        $users = array_values($users);
        if (in_array($userId, $users)) {
            $userEmail = User::find($userId)->email;
            $poke = new Poke();
            $poke->user_id = Auth::user()->id;
            $poke->recipient_id = $userId;
            $poke->email = ($userEmail ? $userEmail : 'null');
            $isSaved = $poke->save();
            if ($isSaved) {
                return $this->jsonResponse([
                    'is_poked' => true,
                    'message' => 'User poked.',
                ]);
            }
        } else {
            return $this->jsonResponse([
                'message' => 'Not Acceptable',
            ], 406, false);
        }
    }
}
