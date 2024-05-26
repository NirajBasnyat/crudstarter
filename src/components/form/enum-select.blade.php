@props([
  'id' => $name,
  'label' => 'Select',
  'class' => 'form-control text-14',
  'value' => '',
  'type' => 'text',
  'message' => '',
  'col' => '12',
  'req' => false,
  'model' => null,
  'name',
  'optionDisplay' => true,
  'labelDisplay' => true,
  'options'
])

<div class="col-md-{{$col}}">

    @if($labelDisplay === true)
        <label for="{{$id}}" class="col-form-label">{{$label}}
            @if($req === true)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <select name='{{$name}}' {{ $attributes->merge(['class' => $class . ' form-control']) }}>
        @if($optionDisplay === true)
            <option value="" disabled selected>Select {{$label}}</option>
        @endif
        @foreach($options as $item)
            @if (isset($model))
                <option value='{{ $item->value }}'
                    {{$model == $item->value ? 'selected' : ''}}
                >{{ $item->name }}</option>
            @else
                <option value='{{ $item->value }}' {{old($name) == $item->value ? 'selected' : ''}}>{{ $item->name }}</option>
            @endif
        @endforeach

    </select>
    @error($name) <span class="text-danger small">{{$message}}</span> @enderror
</div>
