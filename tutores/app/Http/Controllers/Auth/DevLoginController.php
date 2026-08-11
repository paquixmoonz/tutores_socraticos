<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Acceso de desarrollo SIN Google, disponible únicamente en APP_ENV=local.
 * En producción estas rutas ni siquiera se registran (ver routes/web.php)
 * y este controlador aborta como defensa en profundidad.
 */
class DevLoginController extends Controller
{
    public function __invoke(Request $request, User $user): RedirectResponse
    {
        abort_unless(app()->environment('local'), 404);

        if ($user->roles->isEmpty()) {
            $user->assignRole('estudiante');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('catalog.index');
    }
}
