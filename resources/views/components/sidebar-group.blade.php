@props(['label', 'active' => false, 'pending' => false])

<details class="group" @if ($active) open @endif>
    <summary class="menu-item cursor-pointer {{ $active ? 'menu-item-active' : 'menu-item-inactive' }}">
        {{ $label }}
        @if ($pending)<x-pendiente-dot />@endif
        <svg class="h-4 w-4 shrink-0 transition-transform group-open:rotate-180 {{ $pending ? '' : 'ml-auto' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" /></svg>
    </summary>
    <div class="ml-3 space-y-1 border-l border-gray-200 pl-2 dark:border-gray-700">{{ $slot }}</div>
</details>