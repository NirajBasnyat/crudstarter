@props([
'id',
'for',
'url' => null,
'default_url' => config('crudstarter.default_image_path')
])
<div class="image-thumb mt-3"
     style="@isset($url)display:block;@else display: none;@endisset width:150px;aspect-ratio:4/3;position: relative;"
     id="{{$id}}">
    <img src="@if(isset($url) && $url !== ""){{$url}} @else {{$default_url}} @endif" alt="title" class="w-100 card-img">
    <button type="button" onclick="resetImage('{{ $for }}', '{{ $id }}')"
            class="btn btn-xs btn-danger position-absolute top-0 end-0 position-relative">
        <i class='bx bx-x bx-xs bx-tada-hover'></i>
    </button>
</div>
