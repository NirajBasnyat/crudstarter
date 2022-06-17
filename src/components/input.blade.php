<div class="form-group">
    <label for="{{$id ?? ''}}">{{$label ?? 'Text'}}</label>
    <input type="{{$type ?? 'text'}}" class="{{$class ?? 'form-control'}}" name="{{$name}}" id="{{$id ?? ''}}"
    value="{{$value ?? ''}}">
</div>