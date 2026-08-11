<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Models\User;
use App\Services\DocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(private readonly DocumentService $documents) {}

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));
        $role = $request->query('rol');

        $users = User::with('roles')
            ->when($search !== '', fn ($q) => $q->where(fn ($sub) => $sub
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")))
            ->when(in_array($role, ['estudiante', 'docente', 'admin']), fn ($q) => $q->role($role))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search', 'role'));
    }

    public function updateRole(UpdateUserRoleRequest $request, User $user): RedirectResponse
    {
        $newRole = $request->validated('role');
        $previous = $user->roles->pluck('name')->implode(', ') ?: 'sin rol';

        $user->syncRoles([$newRole]);

        $this->documents->recordEvent('role.assigned', $user, $request->user(), [
            'anterior' => $previous,
            'nuevo' => $newRole,
        ]);

        return back()->with('status', "Rol de {$user->name} cambiado a {$newRole}.");
    }
}
