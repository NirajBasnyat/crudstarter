<div class="form-check">
    @if(isset($isEditMode))
        <input type="hidden" name="{{$name}}" value="0">
    @endif
    <input type="checkbox" class="{{$class ?? 'form-check-input'}}" name="{{$name}}" id="{{$id ?? ''}}" value="{{$value ?? 1}}"
       @if(isset($isEditMode)) {{$isChecked}} @endif
    >
    <label class="form-check-label" for="{{$id ?? ''}}">{{$label ?? 'CheckBox'}}</label>
</div>