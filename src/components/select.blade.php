{{-- @foreach(is_array($options)? $options: explode(',', $options) as $key => $item)
@dd($key)
@endforeach
 --}}
{{--
@if(is_array($options))
    @dd($options)
@endif--}}
<div class="form-group">
    <label for="{{$id ?? ''}}">{{$label ?? 'Select'}}</label>
    <select name='{{$name}}' class="form-control" id="{{$id ?? ''}}">
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