<div class="form-group">
    <label for="{{$id ?? $name}}">{{$label ?? 'Select'}}</label>
    <select name='{{$name}}' class="form-control" id="{{$id ?? $name}}">
        <option value="" disabled selected>Select From Given Options</option>
        @foreach($options as $key => $item)
            @if (isset($model))
                <option value='{{ $key }}'
                        {{$model == $key ? 'selected' : ''}}
                >{{ $item }}</option>
            @else
                <option value='{{ $key }}'>{{ $item }}</option>
            @endif
        @endforeach
    </select>
</div>