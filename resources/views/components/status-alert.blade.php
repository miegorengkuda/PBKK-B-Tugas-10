@props(['type'])

@php
    $config = match($type) {
        'success' => [
            'classes' => 'bg-green-50 border-green-200 text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-300',
            'label'   => 'Success',
        ],
        'info' => [
            'classes' => 'bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-300',
            'label'   => 'Info',
        ],
        'warning' => [
            'classes' => 'bg-yellow-50 border-yellow-200 text-yellow-800 dark:bg-yellow-900/20 dark:border-yellow-800 dark:text-yellow-300',
            'label'   => 'Warning',
        ],
        'error', 'danger' => [
            'classes' => 'bg-red-50 border-red-200 text-red-800 dark:bg-red-900/20 dark:border-red-800 dark:text-red-300',
            'label'   => 'Error',
        ],
        default => null,
    };
@endphp

@if ($config)
    <div {{ $attributes->merge(['class' => 'border px-4 py-3 text-sm mb-6 ' . $config['classes']]) }}>
        <span class="font-mono font-semibold text-xs uppercase tracking-wider">{{ $config['label'] }}</span>
        <span class="mx-1.5">—</span>
        {{ $slot }}
    </div>
@endif
