@if(isset($routeShow))
    <td>
        <a href="{{$routeShow}}" class="btn btn-sm btn-secondary">show</a>
    </td>
@endif


@if(isset($routeDestroy))
    <td>
        <form action="{{$routeDestroy}}" method="POST">
            @csrf
            @method('Delete')

            <button class="btn btn-danger btn-sm"
                    onclick="return confirm('Are you sure you want to delete this item?');"
                    type="submit" title="Delete">
                delete
            </button>
        </form>
    </td>
@endif

@if(isset($routeEdit))
    <td>
        <a href="{{$routeEdit}}" class="btn btn-sm btn-info">edit</a>
    </td>
@endif