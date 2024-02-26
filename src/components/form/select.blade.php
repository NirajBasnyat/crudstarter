@props([
  'id' => $name,
  'label' => 'Select',
  'class' => 'form-control',
  'value' => '',
  'type' => 'text',
  'message' => '',
  'col' => '12',
  'req' => false,
  'labelDisplay' => true,
  'optionDisplay' => true,
  'model' => null,
  '_key' => null,
  'name',
  'options'
])

<div class="col-md-{{$col}}">

    @if($labelDisplay === true)
        <label for="{{$id}}" class="col-form-label">{{$label}} @if($req === true)
                <span class="text-danger">*</span>
            @endif</label>
    @endif
    <select name='{{$name}}' {{ $attributes->merge(['class' => $class . ' form-control']) }}>
        @if($optionDisplay === true)
            <option value="" disabled selected>Select {{$label}}</option>
        @endif

        {{$slot}}

        @foreach($options as $key => $item)
            @if (isset($model))
                <option value='{{ $key }}'
                        {{($model == $key || $model == $item) ? 'selected' : ''}}
                >{{ $item }}</option>
            @else
                <option value='{{ $key }}' {{ isset($_key) && $key == 1 ? 'selected' : (old($name) == $key ? 'selected' : '') }}>{{ $item }}</option>
            @endif
        @endforeach
    </select>
    @error($name) <span class="text-danger small">{{$message}}</span> @enderror
</div>