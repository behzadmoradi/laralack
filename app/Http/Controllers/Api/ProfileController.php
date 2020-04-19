<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileForm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function update(UpdateProfileForm $request)
    {
        $this->setDelay();
        $validData = $request->validated();
        if ($request->input('password') != null) {
            $data = [
                'name' => $this->clean($validData['name']),
                'username' => $this->clean($validData['username']),
                'email' => $this->clean($validData['email']),
                'password' => bcrypt($validData['password']),
            ];
        } else {
            $data = [
                'name' => $this->clean($validData['name']),
                'username' => strtolower($this->clean($validData['username'])),
                'email' => strtolower($this->clean($validData['email'])),
            ];
        }
        $isUpdated = User::where('id', Auth::user()->id)->update($data);
        if ($isUpdated) {
            return $this->jsonResponse([
                'profile_is_updated' => true,
                'message' => 'Your profile is successfully updated.',
                'user_info' => User::select('name', 'email')->where('id', Auth::user()->id)->get()[0],
            ]);
        } else {
            return $this->jsonResponse([
                'profile_is_updated' => false,
                'message' => 'Request not successful',
            ], 501, false);
        }
    }
    public function updateAvatar(Request $request)
    {
        $this->mkDir('avatars');
        $userById = User::find(Auth::user()->id);
        $validatedRequest = $request->validate([
            'avatar' => 'required|file|image|mimes:jpeg,jpg,png|max:2048', // max 2 kilobytes
        ]);
        $file = $validatedRequest['avatar'];
        $extension = $file->getClientOriginalExtension();
        $filename = 'avatar-' . time() . '.' . $extension;
        $path = 'img/avatars/' . date('Y') . '/' . date('m');
        $fullPath = $path . '/' . $filename;
        $file->move($path, $filename);
        if ($userById->avatar != null) {
            unlink($userById->avatar);
        }
        $userById->avatar = $fullPath;
        $userById->save();
        return $this->jsonResponse([
            'profile_is_updated' => true,
            'message' => 'Successful avatar update',
            'avatar_path' => $userById->avatar,
        ]);
    }
}
