@props([
'url' => null,
'name' => $name,
'height' => '40px',
'width' => '100px',
'default_url' => config('crudstarter.default_image_path')
])
<td>
    <img src="@if(isset($name, $url) && $url !== "" && $name !== ""){{$url}}@else{{$default_url}}@endif" alt="title"
         height="{{$height}}" width="{{$width}}" {{$attributes}}>
</td>
