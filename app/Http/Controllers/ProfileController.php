<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function promote(Request $request){
        $newAdmin=User::find($request->input('id'));
        $newAdmin->role='admin';

        $newAdmin->save();
        return Redirect::route('profile.show',$newAdmin->id)->with('status', 'profile-updated');
    }

    public function demote(Request $request){
        $newAdmin=User::find($request->input('id'));
        $newAdmin->role='user';

        $newAdmin->save();
        return Redirect::route('profile.show',$newAdmin->id)->with('status', 'profile-updated');
    }
    public function Bookmarks(){
        return view('profile.bookmarks');
    }

    public function showProfile(int $id)
    {

        $user=User::findOrFail($id);
        return view('profile.show',['user'=>$user]);
    }



    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if($request->has('avatar')){
            $avataruploaded= $request->file('avatar');
            $avatarname=$request->user()->id.'.'.$avataruploaded->getClientOriginalExtension();
            $avatarpath=public_path(('\\images\\avatars\\'));
            if($avatarpath.$avatarname!=null){
                File::delete($avatarpath.$avatarname);
            }

            $request->user()->avatar='/images/avatars/'.$avatarname;
            $avataruploaded->move($avatarpath,$avatarname);
        }

        //doesn't work because VSC doesn't see the url method, no idea how to fix it.
        // Storage::disk('avatars')->url($request->user()->avatar);


        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
