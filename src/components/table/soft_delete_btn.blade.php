<span>
    @if(isset($routeRestore))
        <form action="{{$routeRestore}}" method="POST">
        @csrf
        <button class="btn p-0 me-2 mt-1" title="Restore" type="submit" {{$attributes}}>
            <i class='bx bx-revision'></i>
        </button>
    </form>
    @endif
</span>

@if(isset($routeForceDelete))
    <form action="{{$routeForceDelete}}" method="POST">
        @csrf

        <button class="btn p-0 me-2 mt-1 btn-force-delete" title="Delete Permanently" type="submit" {{$attributes}}>
            <i class='bx bx-x-circle'></i>
        </button>
    </form>
@endif