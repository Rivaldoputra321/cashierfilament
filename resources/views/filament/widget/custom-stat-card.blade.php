@php
    $colorClasses = match ($color) {
        'success' => 'bg-green-100 text-green-600',
        'danger' => 'bg-red-100 text-red-600',
        'warning' => 'bg-yellow-100 text-yellow-600',
        default => 'bg-gray-100 text-gray-600',
    };
@endphp

<a href="{{ $url }}" class="block p-4 rounded-lg shadow bg-white hover:bg-gray-50 transition">
    <div class="flex items-center space-x-4">
        <div class="p-4 rounded-full {{ $colorClasses }}">
            <x-dynamic-component :component="$icon" class="w-10 h-10" />
        </div>
        <div>
            <div class="text-sm font-medium text-gray-500">{{ $title }}</div>
            <div class="text-2xl font-semibold text-gray-900">{{ $count }}</div>
            <div class="text-sm text-gray-400">{{ $description }}</div>
        </div>
    </div>
</a>
