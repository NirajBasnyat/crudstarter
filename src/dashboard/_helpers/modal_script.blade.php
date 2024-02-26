<script>
    $(document).on('show.bs.modal', '.modal', function (event) {
        let button = $(event.relatedTarget);
        let {{$name}}Id = button.data('{{$name}}-id');
        let modal = $(this);

        let url = "{{$route}}".replace(':id', {{$name}}Id);

        $.ajax({
            url: url,
            type: 'get',
            dataType: 'html',
            success: function (response) {
                modal.find('.modal-body').html(response);
            }
        });
    });
</script>