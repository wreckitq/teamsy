@props([
    'title' => config('app.name'),
])

@section('title')
    {{ $title }}
@endsection
