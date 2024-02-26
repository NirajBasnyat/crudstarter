<h5 class="fw-bold">
  <span class="fw-light">
    <a class="link-secondary" href="{{route('home')}}">Dashboard <i class='bx bx-chevron-right'></i></a>
  </span>
    <span {{ isset($item) ? 'class=fw-light' : ''}}>
    <a class="link-secondary" href="{{isset($listRoute) ? $listRoute : 'javascript::void(0)'}}">{{$model}} @if(request()->has('archive')) Archived @endif</a>
  </span>
    @isset($item)
        <span class="link-secondary"> <i class='bx bx-chevron-right'></i> {{$item}} {{$model}} </span>
    @endisset
</h5>
