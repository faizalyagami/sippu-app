<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Override method username untuk menggunakan field 'email' 
     * tapi kita akan memodifikasi credentials untuk menerima username
     */
    public function username()
    {
        return 'email'; // Tetap menggunakan email sebagai field utama
    }

    /**
     * Override credentials untuk menerima email atau username
     */
    protected function credentials(Request $request)
    {
        $login = $request->input('email');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        return [
            $field => $login,
            'password' => $request->input('password'),
            'is_active' => true // Hanya user aktif yang bisa login
        ];
    }

    /**
     * Override method sendFailedLoginResponse untuk pesan error yang lebih baik
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $errors = [$this->username() => trans('auth.failed')];
        
        // Check if user exists but not active
        $login = $request->input('email');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        $user = \App\Models\User::where($field, $login)->first();
        if ($user && !$user->is_active) {
            $errors = ['email' => 'Akun Anda tidak aktif. Silakan hubungi administrator.'];
        }
        
        return redirect()->back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors($errors);
    }

    /**
     * The user has been authenticated.
     */
    protected function authenticated(Request $request, $user)
    {
        // Logika redirect berdasarkan role
        if ($user->role_id == 1) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role_id == 2) {
            return redirect()->route('kaprodi.dashboard');
        } elseif ($user->role_id == 3) {
            return redirect()->route('supplier.dashboard');
        }
        
        return redirect()->intended($this->redirectTo);
    }
}