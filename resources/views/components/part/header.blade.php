@props([
    'title' => null,
])

<h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
    {{ $title }}
</h2>

<x-part.title title="{{ $title }}" />
