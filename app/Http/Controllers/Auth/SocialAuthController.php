<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to Google's OAuth consent screen.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            \Log::error('Google OAuth error: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('login')
                ->with('error', 'Unable to authenticate with Google. Please try again.');
        }

        // Check if user already exists with this Google ID
        $user = User::where('google_id', $googleUser->getId())->first();

        if ($user) {
            Auth::login($user, remember: true);
            return $user->subscribed()
                ? redirect()->intended(route('home'))
                : redirect()->route('pricing');
        }

        // Check if user exists with same email (link accounts)
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]);

            Auth::login($user, remember: true);
            return $user->subscribed()
                ? redirect()->intended(route('home'))
                : redirect()->route('pricing');
        }

        // Create new user
        $username = $this->generateUsername($googleUser->getEmail());

        $user = User::create([
            'name' => $googleUser->getName(),
            'username' => $username,
            'email' => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar(),
            'k8s_namespace' => 'gk-user-' . $username,
        ]);

        event(new Registered($user));

        Auth::login($user, remember: true);

        // New user → pick a plan
        return redirect()->route('pricing');
    }

    /**
     * Generate a unique username from an email address.
     */
    protected function generateUsername(string $email): string
    {
        $base = strtolower(preg_replace('/[^a-z0-9]/', '', explode('@', $email)[0]));
        $username = substr($base, 0, 20) ?: 'user';

        $candidate = $username;
        $suffix = 1;
        while (User::where('username', $candidate)->exists()) {
            $candidate = substr($username, 0, 17) . $suffix;
            $suffix++;
        }

        return $candidate;
    }
}
