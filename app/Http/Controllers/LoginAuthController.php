<?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Models\SystemLog;
// use Illuminate\Support\Facades\Auth;
// use Jenssegers\Agent\Agent;


// class LoginAuthController extends Controller

// {
//     public function getLogin()
//     {
//         return view('login.login');
//     }
    
//     public function postLogin(Request $request)
// {
//     $request->validate([
//         'email' => 'required',
//         'password' => 'required',
//     ]);

//     if (auth()->attempt([
//         'email' => $request->email,
//         'password' => $request->password,
//     ])) {
//         $agent = new Agent();
//         SystemLog::create([
//             'user_id' => auth()->id(),
//             'action' => 'Login',
//             'ip_address' => $request->ip(),
//             'user_agent' => $agent->platform() . ' - ' . $agent->browser() . ' ' . $agent->version($agent->browser()),
//         ]);

//         $route = (auth()->user()->role == "Administrator" || auth()->user()->role == "staff") 
//             ? 'dashboard' 
//             : 'dashboard';

//         return redirect()->route($route)->with('success', 'Login Successfully');
//     }

//     return redirect()->back()->with('error', 'Invalid Credentials');
// }

// public function logout(Request $request)
// {
//     if (auth()->check()) {
//         $agent = new Agent();
//         SystemLog::create([
//             'user_id' => auth()->id(),
//             'action' => 'Logout',
//             'ip_address' => $request->ip(),
//             'user_agent' => $agent->platform() . ' - ' . $agent->browser() . ' ' . $agent->version($agent->browser()),
//         ]);

//         auth()->logout();
//         return redirect()->route('getLogin')->with('success', 'You have been Successfully Logged Out');
//     }

//     return redirect()->route('dashboard')->with('error', 'No authenticated user to log out');
// }

// }

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemLog;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;

class LoginAuthController extends Controller
{
    public function getLogin()
    {
        return view('login.login');
    }

    /**
     * Helper function to get user's real IP
     */
    private function getUserIp(Request $request)
    {
        // Check common headers for real IP behind proxies
        $ip = $request->server('HTTP_CLIENT_IP')
            ?? $request->server('HTTP_X_FORWARDED_FOR')
            ?? $request->server('REMOTE_ADDR');

        // If local IPv6 loopback or localhost, get LAN IP
        if ($ip === '::1' || $ip === '127.0.0.1') {
            $ip = gethostbyname(gethostname()); // e.g., 172.16.126.226
        }

        return $ip;
    }

    public function postLogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        if (auth()->attempt([
            'email' => $request->email,
            'password' => $request->password,
        ])) {
            $agent = new Agent();

            SystemLog::create([
                'user_id' => auth()->id(),
                'action' => 'Login',
                'ip_address' => $this->getUserIp($request),
                'user_agent' => $agent->platform() . ' - ' . $agent->browser() . ' ' . $agent->version($agent->browser()),
            ]);

            $route = (auth()->user()->role == "Administrator" || auth()->user()->role == "staff") 
                ? 'dashboard' 
                : 'dashboard';

            return redirect()->route($route)->with('success', 'Login Successfully');
        }

        return redirect()->back()->with('error', 'Invalid Credentials');
    }

    public function logout(Request $request)
    {
        if (auth()->check()) {
            $agent = new Agent();

            SystemLog::create([
                'user_id' => auth()->id(),
                'action' => 'Logout',
                'ip_address' => $this->getUserIp($request),
                'user_agent' => $agent->platform() . ' - ' . $agent->browser() . ' ' . $agent->version($agent->browser()),
            ]);

            auth()->logout();
            return redirect()->route('getLogin')->with('success', 'You have been Successfully Logged Out');
        }

        return redirect()->route('dashboard')->with('error', 'No authenticated user to log out');
    }
}
