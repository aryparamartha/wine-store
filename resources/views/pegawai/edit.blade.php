@extends('layouts.admin')
@section('title', 'Laundry Ceria - Profil')

@section('plugin-css')
{{-- SWEETALERT --}}
<link rel="stylesheet" href="{{asset('assets/vendors/sweetalert2/sweetalert2.min.css')}}">
{{-- SELECT2 --}}
<link rel="stylesheet" href="{{asset('assets/vendors/select2/select2.min.css')}}">
@endsection

@section('plugin-js')
{{-- SWEETALERT --}}
<script src="{{asset('assets/vendors/sweetalert2/sweetalert2.min.js')}}"></script>
{{-- SELECT2 --}}
<script src="{{asset('assets/vendors/select2/select2.min.js')}}"></script>
@endsection

@section('custom-js')
<script>
    
    $(document).ready(function () {
        if ($(".select2").length) {
            $(".select2").select2({placeholder: "Pilih Pendidikan"});

        }

        $("#pendidikan").val("{{$pegawai->pendidikan}}").trigger('change');;
    });
   
    function randomPassword(length) {
        var chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRSTUVWXYZ1234567890";
        var pass = "";
        for (var x = 0; x < length; x++) {
            var i = Math.floor(Math.random() * chars.length);
            pass += chars.charAt(i);
        }
        return pass;
    }

    function generate() {
        memberForm.password.value = randomPassword(9);
    }
</script>
<script>
     $(document).ready(function () {
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

        $
    });   
</script>
@endsection

@section('content')
<div class="page-content">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Form Edit Profil</h4>
                    <form name="memberForm" class="forms-sample" action="{{route('pegawai.update', $pegawai)}}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">Nama</label>
                                    <input type="text" class="form-control" id="nama_pegawai" name="nama_pegawai" placeholder="Putra Dinata" value="{{ $pegawai->nama_pegawai }}" required autofocus>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="email">Username/Email</label>
                                    <input type="text" class="form-control" id="email" name="email" placeholder="Username/Email" value="{{ $pegawai->email }}" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="tgl_lahir">No Hp</label>
                                    <input class="form-control" name="nohp" type="text" value="{{ $pegawai->nohp }}" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="tgl_lahir">Alamat</label>
                                    <input class="form-control" name="alamat" type="text" value="{{ $pegawai->alamat }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="text" class="form-control" id="password" name="password" placeholder="kosongkan jika tidak ingin mengubah password">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="button" style="margin-top: 30px;" onClick="generate();" class="btn btn-warning mr-2">Generate</button>
                            </div>
                        </div>                         
                        <button type="submit" class="btn btn-primary mr-2">Submit</button>
                        <a href="{{route('pegawai.index')}}" class="btn btn-light">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection