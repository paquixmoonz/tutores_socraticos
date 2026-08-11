@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
@include('admin._nav')

<div class="mb-6">
    <h1 class="text-xl font-extrabold tracking-tight sm:text-[22px]">Usuarios y roles</h1>
    <p class="mt-1.5 text-[13px] text-unicaes-slate">
        Los usuarios se crean automáticamente en su primer inicio de sesión con Google institucional.
    </p>
</div>

<form method="GET" action="{{ route('admin.users.index') }}" class="mb-4 flex flex-wrap gap-2">
    <label for="q" class="sr-only">Buscar usuario</label>
    <input id="q" type="search" name="q" value="{{ $search }}" placeholder="Buscar por nombre o correo…"
           class="field-input max-w-xs">
    <label for="rol" class="sr-only">Filtrar por rol</label>
    <select id="rol" name="rol" class="field-input max-w-44" onchange="this.form.submit()">
        <option value="">Todos los roles</option>
        @foreach(['estudiante', 'docente', 'admin'] as $roleOption)
            <option value="{{ $roleOption }}" @selected($role === $roleOption)>{{ ucfirst($roleOption) }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn-secondary">Buscar</button>
</form>

<div class="card overflow-hidden">
    @if($users->isEmpty())
        <p class="p-10 text-center text-sm text-unicaes-slate">No se encontraron usuarios.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] divide-y divide-unicaes-line">
                <thead class="bg-unicaes-mist/60">
                    <tr>
                        <th class="table-th">Usuario</th>
                        <th class="table-th">Rol actual</th>
                        <th class="table-th">Registrado</th>
                        <th class="table-th">Cambiar rol</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-unicaes-line">
                    @foreach($users as $user)
                        <tr>
                            <td class="table-td">
                                <div class="flex items-center gap-3">
                                    @if($user->avatar)
                                        <img src="{{ $user->avatar }}" alt="" class="size-8 rounded-full border border-unicaes-line">
                                    @else
                                        <span class="flex size-8 items-center justify-center rounded-full bg-unicaes-maroon text-xs font-bold text-white">{{ mb_substr($user->name, 0, 1) }}</span>
                                    @endif
                                    <div>
                                        <p class="font-medium">{{ $user->name }}</p>
                                        <p class="text-xs text-unicaes-slate">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="table-td">
                                @php($roleName = $user->roles->pluck('name')->first() ?? 'sin rol')
                                <span class="badge capitalize
                                    {{ $roleName === 'admin' ? 'bg-unicaes-maroon/10 text-unicaes-maroon' : ($roleName === 'docente' ? 'bg-unicaes-blue/10 text-unicaes-blue' : 'bg-unicaes-green/10 text-unicaes-green-dark') }}">
                                    {{ $roleName }}
                                </span>
                            </td>
                            <td class="table-td text-unicaes-slate">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="table-td">
                                @if($user->is(auth()->user()))
                                    <span class="text-xs text-unicaes-slate">No puedes cambiar tu propio rol</span>
                                @else
                                    <form method="POST" action="{{ route('admin.users.role', $user) }}" class="flex items-center gap-2">
                                        @csrf @method('PATCH')
                                        <label for="role-{{ $user->id }}" class="sr-only">Nuevo rol para {{ $user->name }}</label>
                                        <select id="role-{{ $user->id }}" name="role" class="field-input !w-36 !py-1.5 text-xs">
                                            @foreach(['estudiante', 'docente', 'admin'] as $roleOption)
                                                <option value="{{ $roleOption }}" @selected($roleName === $roleOption)>{{ ucfirst($roleOption) }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="rounded-lg bg-unicaes-green/10 px-3 py-1.5 text-xs font-semibold text-unicaes-green-dark hover:bg-unicaes-green/20">
                                            Aplicar
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="border-t border-unicaes-line px-4 py-3">{{ $users->links() }}</div>
    @endif
</div>
@endsection
