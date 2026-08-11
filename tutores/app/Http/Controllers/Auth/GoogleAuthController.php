<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable) {
            return redirect()->route('login')
                ->withErrors(['auth' => 'No se pudo completar el inicio de sesión con Google. Intenta de nuevo.']);
        }

        $email = strtolower((string) $googleUser->getEmail());
        $domain = config('tutores.allowed_domain');

        // Regla dura: solo cuentas institucionales. Se valida ANTES de crear el usuario.
        if (! str_ends_with($email, '@'.$domain)) {
            return redirect()->route('login')
                ->withErrors(['auth' => "Solo se permite el acceso con cuentas institucionales @{$domain}."]);
        }

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $email)
            ->first();

        if (! $user) {
            $user = User::create([
                'name' => $googleUser->getName() ?: Str::before($email, '@'),
                'email' => $email,
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]);
        } else {
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar() ?: $user->avatar,
                'name' => $googleUser->getName() ?: $user->name,
            ]);
        }

        if ($user->roles->isEmpty()) {
            $user->assignRole('estudiante');
        }

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return redirect()->intended(route('catalog.index'));
    }
}
