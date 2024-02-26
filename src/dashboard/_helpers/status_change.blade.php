<script>
    $('#datatable').on('click', '.updateStatusBtn', function (e) {
        let status = $(this).prop('checked') == true ? 1 : 0;
        let id = $(this).data('id');
        $.ajax({
            type: 'GET',
            datatype: "json",
            url: "{{ $url }}",
            data: {'status': status, 'id': id},
            success: function (data) {
                Swal.fire({
                    toast: true,
                    timer: 1500,
                    timerProgressBar: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Status Changed Successfully',
                    showConfirmButton: false,
                })
            },
            error: function (xhr) {
                console.log(xhr.responseText)
            }
        })
    });
</script>
