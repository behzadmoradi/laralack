<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvitationForm;
use App\Models\Channel;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class InvitationController extends Controller
{
    const INVITATION_STATUS_NOT_STARTED = 0;
    const INVITATION_STATUS_FAILURE = 2;
    const INVITATION_STATUS_SUCCESS = 1;

    public function queue(InvitationForm $request)
    {
        $validData = $request->validated();
        $channelId = (int) $this->clean($validData['id']);
        $channelById = Channel::find($channelId);
        if ($channelById->user_id == Auth::user()->id) {
            $invitationsByChannelId = Invitation::where('channel_id', $channelId)
                ->where('status', '<>', self::INVITATION_STATUS_FAILURE)
                ->where('status', '<>', self::INVITATION_STATUS_SUCCESS)
                ->pluck('email')
                ->toArray();
            $data = [];
            $user = new User();
            $emails = array_unique($validData['email'], SORT_REGULAR);
            if ($invitationsByChannelId) {
                if (count($emails) > 0) {
                    foreach ($emails as $i => $email) {
                        if (!in_array($email, $invitationsByChannelId) && $email != Auth::user()->email) {
                            $data[$i]['user_id'] = Auth::user()->id;
                            $data[$i]['channel_id'] = $channelId;
                            $data[$i]['status'] = self::INVITATION_STATUS_NOT_STARTED;
                            $data[$i]['email'] = $this->clean($email);
                            $alreadyRegistered = $user->where('email', $email)->first();
                            if ($alreadyRegistered) {
                                $channelById->users()->attach($alreadyRegistered->id);
                                $data[$i]['link'] = null;
                                $data[$i]['already_registered'] = 1;
                            } else {
                                $data[$i]['already_registered'] = 0;
                                $data[$i]['link'] = URL::temporarySignedRoute(
                                    'accept-invitation', now()->addMinutes(1440), ['id' => $channelId, 'email' => $this->clean($email)]
                                );
                            }
                        }
                    }
                }
            } else {
                if (count($emails) > 0) {
                    foreach ($emails as $i => $email) {
                        if ($email != Auth::user()->email) {
                            $data[$i]['user_id'] = Auth::user()->id;
                            $data[$i]['channel_id'] = $channelId;
                            $data[$i]['status'] = self::INVITATION_STATUS_NOT_STARTED;
                            $data[$i]['email'] = $this->clean($email);
                            $alreadyRegistered = $user->where('email', $email)->first();
                            if ($alreadyRegistered) {
                                $channelById->users()->attach($alreadyRegistered->id);
                                $data[$i]['link'] = null;
                                $data[$i]['already_registered'] = 1;
                            } else {
                                $data[$i]['already_registered'] = 0;
                                $data[$i]['link'] = URL::temporarySignedRoute(
                                    'accept-invitation', now()->addMinutes(1440), ['id' => $channelId, 'email' => $this->clean($email)]
                                );
                            }
                        }
                    }
                }
            }

            if ($data) {
                Invitation::insert($data);
                return $this->jsonResponse([
                    'invitation_sent' => true,
                    'message' => 'Invitations sent',
                ]);
            }
            return $this->jsonResponse([
                'message' => 'An invitation has already been sent to all of these emails.',
            ], 406, false);
        } else {
            return $this->jsonResponse([
                'message' => 'Not Acceptable',
            ], 406, false);
        }
    }
}
