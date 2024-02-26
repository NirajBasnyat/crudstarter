@props([
'url' => null,
'name' => $name,
'height' => '40px',
'width' => '100px',
'default_url' => 'https://upload.wikimedia.org/wikipedia/commons/a/ac/No_image_available.svg'
])
<td>
    <img src="@if(isset($name, $url) && $url !== "" && $name !== ""){{$url}}@else{{$default_url}}@endif" alt="title"
        height="{{$height}}" width="{{$width}}" {{$attributes}}>
</td>
{{-- changed here --}}