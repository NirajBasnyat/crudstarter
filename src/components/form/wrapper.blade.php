@props([
  'method' => 'POST',
  'action'
])

<form action="{{$action}}" method="{{$method}}" {{ $attributes }}>
    @csrf

    {{ $slot }}
</form>