<?php
namespace App\Http\Controllers\Api;

use App\Events\ChannelDeletionEvent;
use App\Events\ChannelMessageEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChannelForm;
use App\Http\Requests\MessageForm;
use App\Models\Channel;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
// https://github.com/erusev/parsedown
use Parsedown;

class ChannelController extends Controller
{
    public function showChannelsByUserId(int $userId)
    {
        if ($userId != Auth::user()->id) {
            return $this->jsonResponse([
                'message' => 'Not Acceptable',
            ], 406, false);
        } else {
            $allChannels = User::find($userId)->channels()->orderBy('id', 'DESC')->get();
            return $this->jsonResponse([
                'channels' => $allChannels,
            ]);
        }
    }

    public function showUsersByChannelId(int $channelId)
    {
        $notAllowed = null;
        $channelById = Channel::find($this->clean($channelId));
        if ($channelById) {
            if ($channelById->user_id == Auth::user()->id) {
                $usersByChannelId = $channelById->users()
                    ->where('id', '<>', Auth::user()->id)
                    ->orderBy('id', 'DESC')
                    ->get();
                return $this->jsonResponse([
                    'channel_id' => $channelId,
                    'users' => $usersByChannelId,
                ]);
            } else {
                $notAllowed = true;
            }
        } else {
            $notAllowed = true;
        }
        if ($notAllowed) {
            return $this->jsonResponse([
                'message' => 'Forbidden',
            ], 403, false);
        }
    }

    public function deleteChannelMember(int $channelId, int $userId)
    {
        $this->setDelay();
        $notAllowed = null;
        $channelById = Channel::find($this->clean($channelId));
        if ($channelById) {
            if ($channelById->user_id == Auth::user()->id) {
                $isDeleted = $channelById->users()->detach($this->clean($userId));
                if ($isDeleted) {
                    $usersByChannelId = $channelById->users()
                        ->where('id', '<>', Auth::user()->id)
                        ->orderBy('id', 'DESC')
                        ->get();
                    return $this->jsonResponse([
                        'deleted_user_id' => $userId,
                        'users' => $usersByChannelId,
                    ]);
                } else {
                    $notAllowed = true;
                }
            } else {
                $notAllowed = true;
            }
        } else {
            $notAllowed = true;
        }
        if ($notAllowed) {
            return $this->jsonResponse([
                'message' => 'Forbidden',
            ], 403, false);
        }
    }

    public function storeChannel(ChannelForm $request)
    {
        $this->setDelay();
        $validData = $request->validated();
        $channelByName = Channel::where('name', $this->clean($validData['name']))
            ->where('user_id', Auth::user()->id)
            ->get();
        if (count($channelByName) > 0) {
            return $this->jsonResponse([
                'channel_name_exists' => true,
                'message' => 'Another channel with this name already exists!',
            ], 406, false);
        } else {
            $channel = new Channel();
            if ($validData['description']) {
                $channel->description = $this->clean($validData['description']);
            }
            $channel->name = $this->clean($validData['name']);
            $channel->user_id = Auth::user()->id;
            $channel->save();
            $newlyAddedChannel = $channel->refresh();
            if ($newlyAddedChannel) {
                $channel->users()->attach(Auth::user()->id);
                return $this->jsonResponse([
                    'new_channel_is_added' => true,
                    'message' => 'Your new channel is successfully created.',
                    'channel_info' => $newlyAddedChannel,
                ]);
            } else {
                return $this->jsonResponse([
                    'new_channel_is_added' => false,
                    'message' => 'Request not successful',
                ], 501, false);
            }
        }
    }

    public function updateChannel(ChannelForm $request, int $channelId)
    {
        $this->setDelay();
        $validData = $request->validated();
        $channelByName = Channel::where('name', $this->clean($validData['name']))
            ->where('user_id', Auth::user()->id)
            ->where('id', '<>', $channelId)
            ->get();
        if (count($channelByName) > 0) {
            return $this->jsonResponse([
                'channel_name_exists' => true,
                'message' => 'Another channel with this name already exists!',
            ], 406, false);
        } else {
            $channelNotBelongToCurrentUser = Channel::where('id', $channelId)
                ->where('user_id', Auth::user()->id)
                ->get();
            if (count($channelNotBelongToCurrentUser) > 0) {
                $channel = Channel::find($channelId);
                $channel->name = $this->clean($validData['name']);
                $channel->description = $this->clean($validData['description']);
                $isUpdated = $channel->save();
                if ($isUpdated) {
                    $validData['id'] = $channelId;
                    return $this->jsonResponse([
                        'channel_is_updated' => true,
                        'channel_info' => $validData,
                        'message' => 'Your channel is successfully updated.',
                    ]);
                } else {
                    return $this->jsonResponse([
                        'channel_is_updated' => false,
                        'message' => 'Request not successful',
                    ], 501, false);
                }
            } else {
                return $this->jsonResponse([
                    'not_belongs' => true,
                    'message' => 'This channel does not belong to you!',
                ], 403, false);
            }
        }
    }

    public function deleteChannel(int $channelId)
    {
        $this->setDelay();
        $channelById = Channel::find($channelId);
        if ($channelById->user_id == Auth::user()->id) {
            $isDeleted = $channelById->delete();
            if ($isDeleted) {
                broadcast(new ChannelDeletionEvent($channelId))->toOthers();
                $allChannelsByUserId = User::find(Auth::user()->id)->channels()->orderBy('id', 'DESC')->get();
                return $this->jsonResponse([
                    'channel_is_deleted' => true,
                    'channel_id' => $channelId,
                    'channels' => $allChannelsByUserId,
                    'message' => 'Your channel is successfully deleted.',
                ]);
            }
        } else {
            return $this->jsonResponse([
                'not_belongs' => true,
                'message' => 'This channel does not belong to you!',
            ], 403, false);
        }
    }

    public function showChannelMessages(int $userId, int $channelId)
    {
        if ($userId != Auth::user()->id) {
            return $this->jsonResponse([
                'message' => 'Not Acceptable',
            ], 406, false);
        } else {
            $messages = [];
            $channelById = Channel::find($channelId);
            if ($channelById->user_id == Auth::user()->id) {
                $channelById->is_owner = true;
            } else {
                $channelById->is_owner = false;
            }
            $messagesByChannelId = $channelById->messages()
                ->orderBy('id', 'ASC')
                ->get();
            if ($messagesByChannelId) {
                $imgRoute = '/img/avatars/';
                foreach ($messagesByChannelId as $index => $msg) {
                    $messages[$index]['id'] = $msg->id;
                    $messages[$index]['user_id'] = $msg->user_id;
                    $messages[$index]['user_name'] = $msg->user->name;
                    $messages[$index]['user_avatar'] = ($msg->user->avatar ? $msg->user->avatar : $imgRoute . 'default-avatar.png');
                    $messages[$index]['message'] = $msg->message;
                    $messages[$index]['updated_at'] = $msg->updated_at->format('F j, Y, g:i a');
                }
            }
            return $this->jsonResponse([
                'channel_info' => [
                    'id' => $channelById->id,
                    'name' => $channelById->name,
                    'description' => $channelById->description,
                    'created_at' => $channelById->created_at->diffForHumans(),
                    'creator' => $channelById->user->name,
                    'is_owner' => $channelById->is_owner,
                ],
                'messages' => $messages,
            ]);
        }
    }

    public function storeMessage(MessageForm $request, int $userId, int $channelId)
    {
        $validData = $request->validated();
        $notAllowed = false;
        if ($userId != Auth::user()->id) {
            $notAllowed = true;
        } else {
            $channelById = Channel::find($channelId);
            if ($channelById) {
                $userIsIncluded = $channelById->users->contains('id', $userId);
                if ($userIsIncluded) {
                    if ($validData['type'] == 'channel') {
                        $imgRoute = '/img/avatars/';
                        $message = new Message();
                        $parsedown = new Parsedown();
                        $parsedown->setSafeMode(true);
                        $message->message = $parsedown->line($validData['message']);
                        $message->user_id = $userId;
                        $message->channel_id = $channelId;
                        if ($request->input('parent_id')) {
                            $message->parent_id = (int) $this->clean($validData['parent_id']);
                        }
                        $message->save();
                        $newlyAddedMessage = $message->refresh();
                        $messageInfo = [
                            'id' => $newlyAddedMessage->id,
                            'channel_id' => $channelId,
                            'user_id' => $newlyAddedMessage->user_id,
                            'user_name' => $newlyAddedMessage->user->name,
                            'user_avatar' => ($newlyAddedMessage->user->avatar ? $newlyAddedMessage->user->avatar : $imgRoute . 'default-avatar.png'),
                            'message' => $newlyAddedMessage->message,
                            'updated_at' => $newlyAddedMessage->updated_at->format('F j, Y, g:i a'),
                        ];
                        broadcast(new ChannelMessageEvent($channelId, $messageInfo))->toOthers();
                        return $this->jsonResponse([
                            'new_message_is_added' => true,
                            'message' => 'Your new message is successfully created.',
                            'message_info' => $messageInfo,
                        ]);
                    } else {
                        $notAllowed = true;
                    }
                } else {
                    $notAllowed = true;
                }
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
}
