<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Data Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: lightgray">

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div>
                    <h3 class=text-center my-4>Test</h3>
                    <form action="{{ route('logout') }}" method="GET" class="mb-3">
                        @csrf
                        <button type="submit" class="btn btn-danger">Logout</button>
                    </form>
                </div>
                <div class="card border-0 shadow-sm rounded">
                    <div class="card-body">
                        <a href="{{ route('products.create') }}"class="btn btn-md btn-success mb-3">Add Product</a>
                        <table id="product-table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th style="width: 20%">Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Show Produk -->
            <div class="modal fade" id="showModal" tabindex="-1" aria-labelledby="showModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="showModalLabel">Detail Produk</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body" id="modal-content">
                    <!-- Isi akan dimasukkan via jQuery -->
                    <div class="text-center">Loading...</div>
                </div>
                </div>
            </div>
            </div>

    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            function loadData() {
                $.get("/products/json", function (data) {
                    let rows = "";
                    data.forEach(function (item){
                        rows += `
                    <tr>
                        <td class="text-center">
                            <img src="/storage/products/${item.image}" style="width: 150px" class="rounded">
                        </td>
                        <td>${item.title}</td>
                        <td>Rp ${parseInt(item.price).toLocaleString('id-ID')}</td>
                        <td>${item.stock}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-dark btn-show" data-id="${item.id}">Show</button>
                            <a href="/products/${item.id}/edit" class="btn btn-sm btn-primary">Edit</a>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="${item.id}">Hapus</button>
                        </td>
                    </tr>`;
                    });
                    $("#product-table tbody").html(rows);
                    $('#product-table').DataTable({
                        'language': {
                            'lengthMenu': 'Tampilkan _MENU_ data per halaman',
                            'zeroRecords': 'Data tidak ditemukan',
                            'info': 'Menampilkan halaman _PAGE_ dari _PAGES_',
                            'infoEmpty': 'Tixdak ada data',
                            'infoFiltered': '(filtered from _MAX_ total records)',
                            'search': 'Cari:',
                            'paginate': {
                                'first': 'Awal',
                                'last': 'Akhir',
                                'next': 'Selanjutnya',
                                'previous': 'Sebelumnya'
                            },
                        }
                    });
                });
            }

            loadData();

            $(document).on('click', '.btn-delete', function () {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/products/${id}`,
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: '{{ csrf_token() }}'
                            },
                            success: function () {
                                Swal.fire('Terhapus!', 'Data berhasil dihapus.', 'success');
                                $('#product-table').DataTable().destroy();
                                loadData();
                            },
                            error: function (xhr) {
                                console.log(xhr);
                                Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus.', 'error');
                            }
                        });
                    }
                })
            })

            $(document).on('click', '.btn-show', function () {
                let id = $(this).data('id');
                $('#modal-content').html('<div class="text-center">Loading...</div>');
                $('#showModal').modal('show');
                $.get(`/products/${id}`, function (data) {
                    let modalContent = `
                    <div class="row">
                        <div class="col-md-4">
                            <img src="/storage/products/${data.image}" class="rounded" style="width: 100%">
                        </div>
                        <div class="col-md-8">
                            <h3>${data.title}</h3>
                            <hr/>
                            <p>Rp ${parseInt(data.price).toLocaleString('id-ID')}</p>
                            <hr/>
                            <p>${data.description}</p>
                            <hr/>
                            <p>Stock : ${data.stock}</p>
                        </div>
                    </div>
                    `;
                    $('#modal-content').html(modalContent);
        });
    });
});
    </script>
</body>
</html>
