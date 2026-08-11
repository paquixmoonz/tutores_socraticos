@extends('layouts.app')

@section('title', 'Auditoría')

@section('content')
@include('admin._nav')

<div class="mb-6">
    <h1 class="text-xl font-extrabold tracking-tight sm:text-[22px]">Auditoría del sistema</h1>
    <p class="mt-1.5 text-[13px] text-unicaes-slate">
        Registro inmutable: quién, qué y cuándo. Solo lectura.
    </p>
</div>

<form method="GET" action="{{ route('admin.audits.index') }}" class="mb-4 grid gap-2 sm:grid-cols-2 lg:grid-cols-5">
    <div>
        <label for="usuario" class="sr-only">Usuario</label>
        <input id="usuario" type="search" name="usuario" value="{{ $filters['usuario'] }}" placeholder="Usuario…" class="field-input">
    </div>
    <div>
        <label for="evento" class="sr-only">Evento</label>
        <select id="evento" name="evento" class="field-input">
            <option value="">Todos los eventos</option>
            @foreach($events as $event)
                <option value="{{ $event }}" @selected($filters['evento'] === $event)>{{ $event }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="desde" class="sr-only">Desde</label>
        <input id="desde" type="date" name="desde" value="{{ $filters['desde'] }}" class="field-input">
    </div>
    <div>
        <label for="hasta" class="sr-only">Hasta</label>
        <input id="hasta" type="date" name="hasta" value="{{ $filters['hasta'] }}" class="field-input">
    </div>
    <button type="submit" class="btn-secondary">Filtrar</button>
</form>

<div class="card overflow-hidden">
    @if($audits->isEmpty())
        <p class="p-10 text-center text-sm text-unicaes-slate">No hay registros de auditoría con esos filtros.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] divide-y divide-unicaes-line">
                <thead class="bg-unicaes-mist/60">
                    <tr>
                        <th class="table-th">Fecha</th>
                        <th class="table-th">Usuario</th>
                        <th class="table-th">Evento</th>
                        <th class="table-th">Registro</th>
                        <th class="table-th">Detalle</th>
                        <th class="table-th">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-unicaes-line">
                    @foreach($audits as $audit)
                        <tr>
                            <td class="table-td whitespace-nowrap font-mono text-[11px] text-unicaes-slate">{{ $audit->created_at->format('Y-m-d H:i') }}</td>
                            <td class="table-td">
                                <p class="font-medium">{{ $audit->user?->name ?? 'Sistema' }}</p>
                                <p class="text-xs text-unicaes-slate">{{ $audit->user?->email }}</p>
                            </td>
                            <td class="table-td">
                                @php($eventColors = [
                                    'created' => 'bg-unicaes-green/15 text-unicaes-green-dark',
                                    'updated' => 'bg-unicaes-blue/10 text-unicaes-blue',
                                    'deleted' => 'bg-red-100 text-red-700',
                                ])
                                <span class="badge {{ $eventColors[$audit->event] ?? 'bg-unicaes-gold/10 text-[#92710A]' }}">
                                    {{ $audit->event }}
                                </span>
                            </td>
                            <td class="table-td text-unicaes-slate">
                                {{ class_basename($audit->auditable_type) }} #{{ $audit->auditable_id }}
                            </td>
                            <td class="table-td max-w-72">
                                @php($changes = collect($audit->new_values)->except(['password']))
                                @if($changes->isNotEmpty())
                                    <details class="text-xs">
                                        <summary class="cursor-pointer font-medium text-unicaes-blue">{{ $changes->count() }} {{ Str::plural('campo', $changes->count()) }}</summary>
                                        <dl class="mt-1.5 space-y-0.5 text-unicaes-slate">
                                            @foreach($changes as $field => $value)
                                                <div class="flex gap-1.5">
                                                    <dt class="font-medium shrink-0">{{ $field }}:</dt>
                                                    <dd class="truncate">{{ is_scalar($value) ? Str::limit((string) $value, 80) : json_encode($value) }}</dd>
                                                </div>
                                            @endforeach
                                        </dl>
                                    </details>
                                @else
                                    <span class="text-xs text-unicaes-slate">—</span>
                                @endif
                            </td>
                            <td class="table-td text-xs text-unicaes-slate">{{ $audit->ip_address }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="border-t border-unicaes-line px-4 py-3">{{ $audits->links() }}</div>
    @endif
</div>
@endsection
