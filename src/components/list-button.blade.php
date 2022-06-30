@if(isset($routeShow))
    <td>
        <a href="{{$routeShow}}" class="btn btn-sm btn-secondary">show</a>
    </td>
@endif

<td class="d-flex">
    <div class="float-right px-2">
        @if(isset($routeDestroy))

            <form action="{{$routeDestroy}}" method="POST">
                @csrf
                @method('Delete')

                <button class="btn btn-danger btn-sm"
                        onclick="return confirm('Are you sure you want to delete this item?');"
                        type="submit" title="Delete">
                    delete
                </button>
            </form>
        @endif
    </div>
    <div>
        @if(isset($routeEdit))
            <a href="{{$routeEdit}}" class="btn btn-sm btn-info">edit</a>
        @endif
    </div>
</td>