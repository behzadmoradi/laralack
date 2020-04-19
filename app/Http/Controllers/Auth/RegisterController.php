<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\Invitation;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
     */

    const INVITATION_STATUS_SUCCESS = 1;
    const INVITATION_STATUS_FAILURE = 2;

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'api_token' => Str::random(80),
        ]);
    }

    protected function registered(Request $request, $user)
    {
        $channelId = $this->clean($request->input('channel_id'));
        if (@$channelId) {
            $email = $this->clean($request->input('email'));
            $invitationByChannelId = Invitation::where([
                'channel_id' => $channelId,
                'email' => $email,
                'already_registered' => 0,
            ])->first();
            if ($invitationByChannelId) {
                $invitationByChannelId->status = self::INVITATION_STATUS_SUCCESS;
                $invitationByChannelId->save();
                $channelById = Channel::find($channelId);
                $channelById->users()->attach($user->id);
            } else {
                $invitationByChannelId->status = self::INVITATION_STATUS_FAILURE;
                $invitationByChannelId->save();
            }
        }
    }
}
