@props([
  'id' => $name,
  'label' => 'Label',
  'class' => '',
  'value' => '',
  'type' => 'text',
  'message' => '',
  'col' => '12',
  'req' => false,
  'name'
])

<div class="col-md-{{$col}}">
    <label for="{{$id}}" class="col-form-label">
        {{$label}}
        @if($req === true)
            <span class="text-danger">*</span>
        @endif
    </label>

    <textarea {{ $attributes->merge(['class' => $class . ' form-control text-14']) }} name="{{$name}}" id="{{$id}}" {{$attributes}}>{{$value}}</textarea>
    @error($name) <span class="text-danger small">{{ $message }}</span> @enderror
</div>
