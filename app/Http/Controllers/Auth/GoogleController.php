<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Teams\CreateTeam;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function __construct(private CreateTeam $createTeam) {}

    /**
     * Redirect the user to the Google OAuth page.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google after authentication.
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Falha ao autenticar com Google. Tente novamente.']);
        }

        // Atomic findOrCreate — prevents duplicate accounts under concurrent callbacks.
        $user = DB::transaction(function () use ($googleUser) {
            // Single query: prefer match by google_id, fall back to email.
            $user = User::where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->lockForUpdate()
                ->first();

            if ($user) {
                $user->forceFill([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $user->avatar ?? $this->trustedAvatar($googleUser->getAvatar()),
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ])->save();

                return $user;
            }

            $user = User::create([
                'name' => $googleUser->getName() ?? $googleUser->getEmail(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $this->trustedAvatar($googleUser->getAvatar()),
                'password' => bcrypt(Str::random(32)),
                'email_verified_at' => now(),
                'is_active' => true,
            ]);

            $this->createTeam->handle($user, $user->name."'s Team", isPersonal: true);

            return $user;
        });

        Auth::login($user, remember: true);

        // Safe intended redirect: only follow the stored URL if it is on the same origin.
        $intended = session()->pull('url.intended', null);
        $appUrl = rtrim(config('app.url'), '/');

        if ($intended && (Str::startsWith($intended, $appUrl.'/') || $intended === $appUrl)) {
            return redirect($intended);
        }

        return redirect('/');
    }

    /**
     * Return the avatar URL only when it comes from a trusted Google CDN.
     * Rejects nulls and any host outside *.googleusercontent.com.
     */
    private function trustedAvatar(?string $url): ?string
    {
        if ($url === null) {
            return null;
        }

        $host = parse_url($url, PHP_URL_HOST) ?? '';

        return str_ends_with($host, '.googleusercontent.com') ? $url : null;
    }
}
