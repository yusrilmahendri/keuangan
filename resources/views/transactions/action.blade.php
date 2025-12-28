<a href="{{ route('transactions.edit', $model) }}" 
   class="btn btn-warning" style="margin-right: 5px; margin-bottom: 5px;">
  <img class="img-fluid" src="{{ asset('images/edit.png') }}" alt="">
  Edit
</a>

<a href="#" 
   class="btn btn-danger delete-btn-{{ $model->id }}"
   data-url="{{ route('transactions.destroy', $model) }}"
   onclick="confirmDelete(event, '{{ route('transactions.destroy', $model) }}')"
   style="margin-bottom: 5px;">
  <i class="fa fa-trash"></i>
  Hapus
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