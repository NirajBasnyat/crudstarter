<td class="d-flex">
    <div class="float-right px-2">

        @if(isset($routeRestore))
        <form action="{{$routeRestore}}" method="POST">
            @csrf

            <button class="btn btn-info btn-sm" onclick="return confirm('Are you sure you want to restore this item?');"
                type="submit">
                Restore
            </button>
        </form>
        @endif
    </div>

    <div>
        @if(isset($routeForceDelete))
        <form action="{{$routeForceDelete}}" method="POST">
            @csrf

            <button class="btn btn-danger btn-sm"
                onclick="return confirm('Are you sure you want to permanently delete this item?');" type="submit">
                Delete Permanently
            </button>
        </form>
        @endif
    </div>
</td>