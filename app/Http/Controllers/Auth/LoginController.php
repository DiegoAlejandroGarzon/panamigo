<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(\Illuminate\Http\Request $request, $user)
    {
        \Log::info('User Logged In: ' . $user->email . ' with roles: ' . implode(', ', $user->getRoleNames()->toArray()));

        if ($user->hasRole('Atención al Cliente')) {
            return redirect('/pos/order');
        }
        
        if ($user->hasRole('Cajera')) {
            return redirect('/pos/cashier');
        }
        
        if ($user->hasRole('Admin') || $user->hasRole('super-admin')) {
            return redirect('/admin/dashboard');
        }

        return redirect('/home');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
