<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class SSOController extends Controller
{
    public function konektaLogin(Request $request)
    {
        $email = $request->get('email');
        $token = $request->get('sso_token');
        $timestamp = $request->get('timestamp');

        // Validate parameters
        if (!$email || !$token || !$timestamp) {
            return redirect()->route('getLogin')
                ->with('error', 'Invalid SSO request.');
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
                ->with('error', 'Email mismatch.');
        }

        // Check token expiry (5 minutes)
        if (time() - $timestamp > 300) {
            return redirect()->route('getLogin')
                ->with('error', 'SSO token expired.');
        }

        // Find user by email
        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('getLogin')
                ->with('error', 'Email not registered in SNow. Contact MIS Office.');
        }

        // Login the user
        Auth::guard('web')->login($user);

        // Redirect based on role
        if ($user->role === 'Administrator') {
            return redirect()->route('dashboard')
                ->with('success', 'Logged in via CPSU KonekTa SSO!');
        } else {
            return redirect()->route('home')
                ->with('success', 'Logged in via CPSU KonekTa SSO!');
        }
    }
}