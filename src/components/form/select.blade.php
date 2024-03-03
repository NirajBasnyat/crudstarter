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

<!-- 
Usage of this component
:options : array  -- you can put your model request here  option might be a simple array also 
value : string  -- The column you want to display 
_key : string  -- The id you want to use as value in the select option  

<x-form.select col="6" name="category_id" label="Category" :options="$categories" _key="id" value="name" />        

-->

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
        <option value="{{ $item[$_key]??$item }}" {{ $item[$_key]??$item==$model ? 'selected' : '' }}>
            {{ $item[$value]??$item }}
        </option>
        @endforeach
    </select>
    @error($name) <span class="text-danger small">{{$message}}</span> @enderror
</div>