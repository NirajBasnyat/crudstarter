<script>
    $(function () {
        $('#datatable').on('click', '.btn-delete', function (e) {
            e.preventDefault();
            let form = $(this).closest("form")

            Swal.fire({
                title: 'Are you sure?',
                text: "This item will be deleted" +
                    "!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
        });


        $('#datatable').on('click', '.btn-force-delete', function (e) {
            e.preventDefault();
            let form = $(this).closest("form")

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Eliminate it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
        });
    })
</script>