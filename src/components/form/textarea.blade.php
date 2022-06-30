<div class="form-group">
    <label for="{{$id ?? $name}}">{{$label ?? 'TextArea'}}</label>
    <textarea class="{{$class ?? 'form-control'}}" name="{{$name}}" id="{{$id ?? $name}}" rows="{{$rows ?? 5}}" cols="{{$cols ?? 5}}">{{$value}}</textarea>
    @error($name) <span class="text-danger">{{ $message }}</span> @enderror
</div>