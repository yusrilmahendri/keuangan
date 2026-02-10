<a href="{{ route('transactions.edit', $model) }}"
   class="btn btn-warning" style="margin-right: 5px; margin-bottom: 5px;">
  <img class="img-fluid" src="{{ asset('images/edit.png') }}" alt="">
  Edit
</a>



<script>
function confirmDelete(event, url) {
    event.preventDefault();

    swal({
        title: 'Apakah Anda yakin?',
        text: "Data transaksi ini akan dihapus permanen!",
        icon: 'warning',
        buttons: {
            cancel: {
                text: 'Batal',
                value: null,
                visible: true,
                className: "",
                closeModal: true,
            },
            confirm: {
                text: 'Ya, Hapus!',
                value: true,
                visible: true,
                className: "btn-danger",
                closeModal: true
            }
        },
        dangerMode: true,
    }).then(function(willDelete) {
        if (willDelete) {
            document.getElementById('deleteForm').action = url;
            document.getElementById('deleteForm').submit();
        }
    });
}
</script>
