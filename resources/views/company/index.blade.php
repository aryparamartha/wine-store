@extends('layouts.admin')

@section('title', 'WineryApp - Company Profile')

@section('plugin-css')
<link rel="stylesheet" href="{{asset('assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css')}}">
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
<script src="{{asset('assets/js/file-upload.js')}}"></script>
@endsection

@section('custom-js')
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

    $(function(){
        $(".file-upload-default").change(function(event){
            $("#preview").attr("src",URL.createObjectURL(event.target.files[0]));
        })
    })
</script>
@endsection

@section('content')
<form name="memberForm" class="forms-sample" action="{{route('profile.update', $profile)}}" method="POST" enctype='multipart/form-data'>
    @csrf
<div class="page-content">
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Company Profile</h4>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name" class="w-100">Name</label>
                                <input value="{{$profile->name}}" tupe="text" id="name" name="name" class="w-100 form-control" required />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="address" class="w-100">Address</label>
                                <input value="{{$profile->address}}" tupe="text" id="address" name="address" class="w-100 form-control" required />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="phone" class="w-100">Phone</label>
                                <input value="{{$profile->phone}}" tupe="text" id="phone" name="phone" class="w-100 form-control" required />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="email" class="w-100">Email</label>
                                <input value="{{$profile->email}}" tupe="text" id="name" name="email" class="w-100 form-control" required />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="website" class="w-100">Website</label>
                                <input value="{{$profile->website}}" tupe="text" id="website" name="website" class="w-100 form-control" required />
                            </div>
                        </div>
                        <div id="transfer-proof-body" class="col-md-12">
                            <div class="form-group">
                                <label>Logo</label>
                                <input type="file" name="logo_file" class="file-upload-default">
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control file-upload-info" disabled="" placeholder="Upload Image">
                                    <span class="input-group-append">
                                        <button class="file-upload-browse btn btn-primary" type="button">Select</button>
                                    </span>
                                </div>
                                
                                <img id="preview" src="{{$profile->getLogo()}}" width="100px" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-success mr-2">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
@endsection