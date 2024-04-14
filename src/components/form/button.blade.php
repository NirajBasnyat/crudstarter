@props([
  'id' => 'id',
  'class' => '',
  'type' => 'submit'
])

<button id="{{$id}}" {{ $attributes->merge(['class' => $class . ' btn btn-sm mt-3']) }} type="{{$type}}">{{$slot}}</button>
