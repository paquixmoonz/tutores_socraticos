@php($items = [
    ['route' => 'admin.dashboard', 'match' => 'admin.dashboard', 'label' => 'Resumen'],
    ['route' => 'admin.facultades.index', 'match' => 'admin.facultades.*', 'label' => 'Facultades'],
    ['route' => 'admin.carreras.index', 'match' => 'admin.carreras.*', 'label' => 'Carreras'],
    ['route' => 'admin.categorias.index', 'match' => 'admin.categorias.*', 'label' => 'Categorías'],
    ['route' => 'admin.materias.index', 'match' => 'admin.materias.*', 'label' => 'Materias'],
    ['route' => 'admin.users.index', 'match' => 'admin.users.*', 'label' => 'Usuarios'],
    ['route' => 'admin.audits.index', 'match' => 'admin.audits.*', 'label' => 'Auditoría'],
])

<nav aria-label="Secciones de administración" class="mb-8 -mx-4 overflow-x-auto px-4 sm:mx-0 sm:px-0">
    <ul class="flex w-max min-w-full border-b border-unicaes-line">
        @foreach($items as $item)
            <li>
                <a href="{{ route($item['route']) }}"
                   class="-mb-px block whitespace-nowrap border-b-2 px-[18px] py-2 text-[13px] transition-colors {{ request()->routeIs($item['match']) ? 'border-unicaes-green font-bold text-unicaes-green' : 'border-transparent font-medium text-unicaes-slate hover:text-unicaes-ink' }}">
                    {{ $item['label'] }}
                </a>
            </li>
        @endforeach
    </ul>
</nav>
