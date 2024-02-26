@props([
  'id' => $name,
  'label' => 'CheckBox',
  'class' => 'form-check-input',
  'value' => 1,
  'message' => '',
  'isChecked' => '',
  'isEditMode' => '',
  'col' => '12',
  'name'
])

<div class="form-check col-md-{{$col}}">
    @if(isset($isEditMode))
        <input type="hidden" name="{{$name}}" value="0">
    @endif
    <input type="checkbox" class="{{$class}}" name="{{$name}}" id="{{$id}}" value="{{$value}}" {{$attributes}}
    @if(isset($isEditMode))
        {{$isChecked}}
            @endif
    >
    <label class="form-check-label" for="{{$id}}">{{$label}}</label>
</div>