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
  'options'
])

<div class="col-md-{{$col}}">

    <label for="{{$id}}" class="col-form-label">{{$label}} @if($req === true)
            <span class="text-danger">*</span>
        @endif</label>

    <select name='{{$name}}' class="form-control" id="{{$id}}">
        <option value="" disabled selected> -- select from given options --</option>
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