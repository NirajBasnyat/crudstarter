<div class="form-check">
    @if(isset($isEditMode))
        <input type="hidden" name="{{$name}}" value="0">
    @endif
    <input type="checkbox" class="{{$class ?? 'form-check-input'}}" name="{{$name}}" id="{{$id ?? $name}}" value="{{$value ?? 1}}"
    @if(isset($isEditMode)) {{$isChecked}} @endif
    >
    <label class="form-check-label" for="{{$id ?? $name}}">{{$label ?? 'CheckBox'}}</label>
    @error($name) <span class="text-danger">{{ $message }}</span> @enderror

</div>