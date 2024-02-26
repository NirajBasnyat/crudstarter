@props([
  'id' => $id,
  'model' => $model,
  'name' => $name
])

@if(isset($routeView))
    <a href="{{$routeView}}" class="btn btn-sm btn-default" data-bs-toggle="modal" title="View {{$model}}" data-bs-target="#view{{$model}}{{$id}}" data-{{$name}}-id="{{$id}}" {{$attributes}}>
        <i class='bx bx-show-alt'></i>
    </a>
@endif
