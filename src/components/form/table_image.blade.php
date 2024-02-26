@props([
'url' => null,
'height' => '40px',
'width' => '100px',
'default_url' => 'https://upload.wikimedia.org/wikipedia/commons/a/ac/No_image_available.svg'
])
<img src="@if(isset($url) && $url != ""){{$url}}@else{{$default_url}}@endif" alt="title" height="{{$height}}" width="{{$width}}" {{$attributes}}>