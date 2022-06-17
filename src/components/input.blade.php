<div class="form-group">
    <label for="{{$id ?? $name}}">{{$label ?? 'Text'}}</label>
    <input type="{{$type ?? 'text'}}" class="{{$class ?? 'form-control'}}" name="{{$name}}" id="{{$id ?? $name}}"
    value="{{$value ?? ''}}">
</div>