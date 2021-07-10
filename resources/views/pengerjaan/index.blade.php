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
        var pengerjaans = {!! $pengerjaans !!}
        console.log(pengerjaans)

        function hitung_total(){
            var jumlah = parseInt($("#jumlah").val())
            var harga_layanan = parseInt($("#harga").val())
            var total = jumlah * harga_layanan
            $("#total_harga").val(total)
        }

        //ubah harga layanan
        $("#id_layanan").change(function(){
            var index = $(this).prop('selectedIndex')
            var harga_layanan = layanans[index].harga
            $("#harga").val(harga_layanan)
            hitung_total()
        })

        $("#jumlah").keyup(function(){
            hitung_total()
        })

        //tampilkan modal tambah pengerjaan
        $('#btn-add-pengerjaan').click(function () {
            $('#id_konsumen').val("");
            $('#id_layanan').val("");
            $('#harga').val("");
            $('#jumlah').val("");
            $('#total_harga').val("");
            $('#pengerjaan-form').attr('action', "{{route('pengerjaan.store')}}");
            $('#pengerjaan-modal-title').html("Tambah Pengerjaan");
            $('#status_select').val('dalam antrean')
            $('#status_select').hide()
            $('#pengerjaan-modal').modal('show');
        });

        //tampilkan modal edit pengerjaan
        $('.btn-edit-pengerjaan').click(function() {
            var index = $(this).data('index');
            var pengerjaan = pengerjaans[index]

            //url update data pengerjaan
            $('#pengerjaan-form').attr('action', '/pengerjaan/update/' + pengerjaan.id_pengerjaan); // /pengerjaan/update/{id}

            $('#pengerjaan-modal-title').html("Update Pengerjaan");
            $('#pengerjaan-modal').modal('show');
            $('#id_konsumen').val(pengerjaan.id_konsumen);
            $('#id_layanan').val(pengerjaan.id_layanan);
            $('#harga').val(pengerjaan.harga);
            $('#jumlah').val(pengerjaan.jumlah);
            $('#total_harga').val(pengerjaan.total_harga);
            $('#status').val(pengerjaan.status)
            $('#status_select').show()
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
                            <h6 class="card-title">Data Pengerjaan</h6>
                        </div>
                        <div class="col-md-6 col-6">
                            <div class="flt-right">
                                <a class="btn btn-success btn-icon-text btn-edit-profile" href="javascript:void(0)" id="btn-add-pengerjaan" >
                                    <i data-feather="plus" class="btn-icon-prepend"></i> Tambah Pengerjaan
                                </a>
                            </div>  
                        </div>
                    </div>          
                    <div class="table-responsive">
                        <table id="dataTableExample" class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Konsumen</th>
                                    <th>Jenis Layanan</th>
                                    <th>Jumlah</th>
                                    <th>Harga</th>
                                    <th>Total Harga</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pengerjaans as $key => $pengerjaan) 
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{ $pengerjaan->konsumen->nama }}</td>
                                    <td>{{ $pengerjaan->layanan->jenis_layanan }}</td>
                                    <td>{{ $pengerjaan->jumlah }}Kg</td>
                                    <td>Rp{{ number_format($pengerjaan->harga, 0, "", ".") }}</td>
                                    <td>Rp{{ number_format($pengerjaan->total_harga, 0, "", ".") }}</td>
                                    <td>
                                        @switch($pengerjaan->status)
                                            @case('dalam antrean')
                                            <span class="badge badge-light">dalam antrean</span>
                                            @break
                                            @case('sedang dicuci')
                                            <span class="badge badge-primary">sedang dicuci</span>
                                            @break
                                            @case('sedang disetrika')
                                            <span class="badge badge-primary">sedang disetrika</span>
                                            @break
                                            @case('selesai')
                                            <span class="badge badge-success">selesai</span>
                                            @break
                                        @endswitch
                                    </td>
                                    <td>{{ $pengerjaan->tanggal }}</td>
                                    <td>
                                        <button data-index="{{$key}}" class="btn-edit-pengerjaan btn btn-primary btn-icon">
                                            <i data-feather="edit"></i>
                                        </button>
                                        <a href="{{ route('pengerjaan.print', $pengerjaan) }}" target="_blank" class="btn-edit-pengerjaan btn btn-info btn-icon">
                                            <i data-feather="printer"></i>
                                        </a>
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
<div class="modal fade" id="pengerjaan-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">        
        <form id="pengerjaan-form" class="forms-sample" action="" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="pengerjaan-modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="nama_pengerjaan">Nama Konsumen</label>
                    <select id="id_konsumen" name="id_konsumen">
                        @foreach($konsumens as $konsumen)
                        <option value="{{$konsumen->id_konsumen}}">{{$konsumen->nama}}</option>
                        @endforeach
                    </select>
                </div>      
                <div class="form-group">
                    <label for="nohp">Jenis Layanan</label>
                    <select id="id_layanan" name="id_layanan">
                        @foreach($layanans as $layanan)
                        <option value="{{$layanan->id_layanan}}">{{$layanan->jenis_layanan}}</option>
                        @endforeach
                    </select>
                </div>      
                <div class="form-group">
                    <label for="harga">Harga Layanan</label>
                    <input type="number" class="form-control" id="harga" name="harga" required readonly>                     
                </div>      
                <div class="form-group">
                    <label for="jumlah">Jumlah(Kg)</label>
                    <input type="number" class="form-control" id="jumlah" name="jumlah" required autofocus min=1>                     
                </div>      
                <div class="form-group">
                    <label for="total_harga">Total Harga</label>
                    <input type="number" class="form-control" id="total_harga" name="total_harga" required readonly>                     
                </div>      
                <div class="form-group" id="status_select">
                    <label for="status">Status Pengerjaan</label>
                    <select id="status" name="status">
                        <option value="dalam antrean">dalam antrean</option>
                        <option value="sedang dicuci">sedang dicuci</option>
                        <option value="sedang disetrika">sedang disetrika</option>
                        <option value="selesai">selesai</option>
                    </select>
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