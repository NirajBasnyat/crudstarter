@props([
  'icon' => 'bx bx-home-circle',
  'name' => 'Multi Sidebar',
  'routes' => []
])

@php
    $hasRoute = false;
    foreach ($routes as $uri) {
        if (request()->is($uri . '*')) {
            $hasRoute = true;
            break;
        }
    }
@endphp

<li class="menu-item {{ $hasRoute ? 'active open' : '' }}" style="">
    <a href="javascript:void(0);" class="menu-link menu-toggle " >
        <i class="menu-icon tf-icons {{$icon}}"></i>
        <div class="text-truncate" data-i18n="{{$name}}">{{$name}}</div>
    </a>
    <ul class="menu-sub">
        {{$slot}}
    </ul>
</li>
