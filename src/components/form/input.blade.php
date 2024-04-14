@props([
  'id' => $name,
  'label' => 'Label',
  'class' => '',
  'value' => '',
  'type' => 'text',
  'message' => '',
  'col' => '12',
  'req' => false,
  'labelDisplay' => true,
  'name',
  'placeholder' => '',
])
<div class="col-md-{{$col}}">

    @if($labelDisplay === true)
        <label for="{{$id}}" class="col-form-label">
            {{$label}}
            @if($req === true)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <input type="{{$type}}" {{ $attributes->merge(['class' => $class . ' form-control text-14']) }} name="{{$name}}" id="{{$id}}" value="{{$value}}" placeholder="{{$placeholder}}">

    @error($name) <span class="text-danger small">{{ $message }}</span> @enderror
</div>
