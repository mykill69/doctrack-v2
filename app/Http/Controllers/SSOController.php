<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\SystemLog;
use Jenssegers\Agent\Agent;

class SSOController extends Controller
{
    private function getUserIp(Request $request)
    {
        $ip = $request->server('HTTP_CLIENT_IP')
            ?? $request->server('HTTP_X_FORWARDED_FOR')
            ?? $request->server('REMOTE_ADDR');

        if ($ip === '::1' || $ip === '127.0.0.1') {
            $ip = gethostbyname(gethostname());
        }

        return $ip;
    }

    public function konektaLogin(Request $request)
    {
        $email = $request->get('email');
        $token = $request->get('sso_token');
        $timestamp = $request->get('timestamp');

        // Validate parameters exist
        if (!$email || !$token || !$timestamp) {
            return redirect()->route('getLogin')
                ->with('error', 'Invalid SSO request. Missing parameters.');
        }

        // Decode the token
        $decoded = base64_decode($token);
        $tokenData = json_decode($decoded, true);

        if (!$tokenData) {
            return redirect()->route('getLogin')
                ->with('error', 'Invalid SSO token.');
        }

        // Verify email matches
        if ($tokenData['email'] !== $email) {
            return redirect()->route('getLogin')
                ->with('error', 'SSO token email mismatch.');
        }

        // Check token expiry (5 minutes)
        if (time() - $timestamp > 300) {
            return redirect()->route('getLogin')
                ->with('error', 'SSO token expired. Please login again.');
        }

        // Find user by email
        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('getLogin')
                ->with('error', 'Your email is not registered in DocTrack. Contact MIS Office.');
        }

        // Login the user
        Auth::login($user);

        // Log the SSO login
        $agent = new Agent();
        SystemLog::create([
            'user_id' => $user->id,
            'action' => 'SSO Login via KonekTa',
            'ip_address' => $this->getUserIp($request),
            'user_agent' => $agent->platform() . ' - ' . $agent->browser() . ' ' . $agent->version($agent->browser()),
        ]);

        // Redirect to dashboard
        return redirect()->route('dashboard')
            ->with('success', 'Logged in via CPSU KonekTa SSO! Welcome, ' . $user->fname . '!');
    }
}