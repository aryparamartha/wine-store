@extends('layouts.admin')

@section('title', 'Laundry Ceria - Home')

@section('plugin-css')
<link rel="stylesheet" href="{{asset('assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css')}}">
{{-- SWEETALERT --}}
<link rel="stylesheet" href="{{asset('assets/vendors/sweetalert2/sweetalert2.min.css')}}">
{{-- SELECT2 --}}
<link rel="stylesheet" href="{{asset('assets/vendors/select2/select2.min.css')}}">
@endsection

@section('plugin-js')
<script src="{{asset('assets/vendors/datatables.net/jquery.dataTables.js')}}"></script>
<script src="{{asset('assets/vendors/datatables.net-bs4/dataTables.bootstrap4.js')}}"></script>
{{-- SWEETALERT --}}
<script src="{{asset('assets/vendors/sweetalert2/sweetalert2.min.js')}}"></script>
{{-- SELECT2 --}}
<script src="{{asset('assets/vendors/select2/select2.min.js')}}"></script>
@endsection

@section('custom-js')
<script src="{{asset('assets/js/data-table.js')}}"></script>
<script>
    @if (\Session::has('success'))  
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000
        });
        
        Toast.fire({
            type: 'success',
            title: "{{ \Session::get('success') }}"
        });
    @endif   
</script>
<script>
    $(document).ready(function () {
        var layanans = {!! $layanans !!}
        $('#btn-add-layanan').click(function () {
            $('#id_layanan').val("");
            $('#jenis_layanan').val("");
            $('#harga').val("");
            $('#layanan-form').attr('action', "{{route('layanan.store')}}");
            $('#layanan-modal-title').html("Tambah Layanan");
            $('#layanan-modal').modal('show');
        });

        $('.btn-edit-layanan').click(function() {
            var index = $(this).data('index');
            var layanan = layanans[index]
            console.log(layanan)
            $('#layanan-form').attr('action', '/layanan/update/' + layanan.id_layanan);
            $('#layanan-modal-title').html("Update Layanan");
            $('#layanan-modal').modal('show');
            $('#id_layanan').val(layanan.id_layanan);
            $('#jenis_layanan').val(layanan.jenis_layanan);
            $('#harga').val(layanan.harga);
        });

        $(".btn-dlt-alert").click(function(event){
            button = $(this);
            title = button.data('title')
            text = button.data('text')
            index = $(".btn-dlt-alert").index(button);
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false,
            })
            
            swalWithBootstrapButtons.fire({
                title: title,
                text: text,
                type: 'warning',
                showCancelButton: true,
                confirmButtonClass: 'ml-2',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $(".frm-dlt-alert").eq(index).submit();
                }
            });
        });

    });        
</script>
@endsection

@section('custom-css')
<link rel="stylesheet" href="{{asset('assets/css/custom.css')}}">
@endsection


@section('content')
<div class="page-content">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-20">
                        <div class="col-md-6 col-6">
                            <h6 class="card-title">Data Layanan</h6>
                        </div>
                        <div class="col-md-6 col-6">
                            <div class="flt-right">
                                <a class="btn btn-success btn-icon-text btn-add-layanan" href="javascript:void(0)" id="btn-add-layanan" >
                                    <i data-feather="plus" class="btn-icon-prepend"></i> Tambah Layanan
                                </a>
                            </div>  
                        </div>
                    </div>          
                    <div class="table-responsive">
                        <table id="dataTableExample" class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Layanan</th>
                                    <th>Harga</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($layanans as $key => $layanan) 
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{ $layanan->jenis_layanan }}</td>
                                    <td>Rp{{ number_format($layanan->harga, 0, "", ".") }}</td>
                                    <td>
                                        <button data-index="{{$key}}" class="btn-edit-layanan btn btn-primary btn-icon">
                                            <i data-feather="edit"></i>
                                        </button>
                                        <form class="frm-dlt-alert" action="{{route('layanan.delete', $layanan)}}" method="post" style="display: inline-block;">
                                            @csrf
                                            <button type="button" class="btn-dlt-alert btn btn-danger btn-icon" data-title="Hapus Layanan" data-text="Anda yakin menghapus data layanan ini?">
                                                <i data-feather="trash"></i>
                                            </button>
                                        </form> 
                                    </td>                         
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal --}}
<div class="modal fade" id="layanan-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">        
        <form id="layanan-form" class="forms-sample" action="" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="layanan-modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id_layanan" id="id_layanan">
                <div class="form-group">
                    <label for="jenis_layanan">Jenis Layanan</label>
                    <input type="text" class="form-control" id="jenis_layanan" name="jenis_layanan" required autofocus>                     
                </div>      
                <div class="form-group">
                    <label for="harga">Harga</label>
                    <input type="text" class="form-control" id="harga" name="harga" required autofocus>                     
                </div>         
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button id="btnSave" value="create" type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
        </div>
    </div>
</div>
{{-- Modal --}}
@endsection