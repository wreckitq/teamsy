@props([
    'icon' => null,
    'name' => null,
    'routeName' => null,
])

<li class="relative px-6 py-3">
    @php
        $isActivate = false;

        if ($routeName) {
            $isActivate = request()->is($routeName);
        }
    @endphp

    {!!
        $isActivate
            ? '<span class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>'
            : ''
    !!}

    <a
        href="{{ empty($routeName) ? '#' : route($routeName) }}"
        data-turbolinks-action="replace"
        class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 {{ $isActivate ? 'text-gray-800 dark:text-gray-100' : '' }}"
    >
        {{ $icon }}

        <span class="ml-4">{{ $name }}</span>
    </a>
</li>
