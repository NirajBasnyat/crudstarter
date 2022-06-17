<form action="{{$action}}" method="{{$method ?? 'POST'}}"
      @isset($enctype) enctype="multipart/form-data" @endisset
>
    @csrf

    {{ $slot }}
</form>