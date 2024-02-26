<span>
@if(isset($routeDestroy))
        <form action="{{$routeDestroy}}" method="POST">
        @csrf
        @method('Delete')

        <button class="btn p-0 me-2 mt-1 btn-delete" title="Delete">
            <i class="bx bx-trash"></i>
        </button>
    </form>
    @endif
</span>