<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Role;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GoogleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function auth()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function callback()
    {
        $user = Socialite::driver('google')->stateless()->user();
        $this->validateUser($user);

        $model = $this->getUser($user);
        Auth::loginUsingId($model->id, true);

        return redirect()->intended('/');
    }

    /**
     * Validates the callback request.
     *
     * @param  mixed $user
     */
    private function validateUser($user)
    {
        if ($user && isset($user->user['hd']) && $user->user['hd'] != env('AUTH_DOMAIN')) {
            abort(403, 'Only ' . env('AUTH_DOMAIN') . ' domains are allowed!');
        }
    }

    /**
     * Find (or create) the related user.
     *
     * @param  mixed $user
     * @return \App\Models\User
     */
    private function getUser($user)
    {
        $model = User::firstOrNew(['email' => $user->getEmail()]);
        $model->role()->associate(Role::user()->first());
        $model->email_verified_at = Carbon::now();
        $model->google_id = $user->getId();
        $model->avatar_url = $user->getAvatar();
        $model->name = $user->getName();
        $model->save();

        return $model;
    }
}
