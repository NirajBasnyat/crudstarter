@props([
  'uri' => 'home',
  'name' => 'Sidebar Item',
  'route'
])
{{--
<li class="menu-item {{ request()->route()->uri === $uri ? 'active' : '' }}">
    <a href="{{$route}}" class="menu-link">
        {{$slot}}
        <div data-i18n="Analytics">{{$name}}</div>
    </a>
</li>--}}


<li class="menu-item {{ (request()->is($uri) || request()->is($uri . '/*')) ? 'active' : ''  }}">
    <a href="{{$route}}" class="menu-link">
        {{$slot}}
        <div data-i18n="Analytics">{{$name}}</div>
    </a>
</li>