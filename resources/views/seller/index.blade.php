@extends('layouts.admin')

@section('title', 'WineryApp - Sellers')

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
        var sellers = {!! $sellers !!}
        $('#btn-add-seller').click(function () {
            $('#name').val("");
            $('#address').val("");
            $('#number').val("");
            $('#email').val("");
            $('#seller-form').attr('action', "{{route('seller.store')}}");
            $('#seller-modal-title').html("Add Seller");
            $('#seller-modal').modal('show');
        });

        $('.btn-edit-seller').click(function() {
            var index = $(this).data('index');
            var seller = sellers[index]
            $('#seller-form').attr('action', '/seller/update/' + seller.id);
            $('#seller-modal-title').html("Update Seller");
            $('#seller-modal').modal('show');
            $('#name').val(seller.name);
            $('#address').val(seller.address);
            $('#number').val(seller.number);
            $('#email').val(seller.email);
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
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
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
                            <h6 class="card-title">Seller Data</h6>
                        </div>
                        <div class="col-md-6 col-6">
                            <div class="flt-right">
                                <a class="btn btn-success btn-icon-text btn-edit-profile" href="javascript:void(0)" id="btn-add-seller" >
                                    <i data-feather="plus" class="btn-icon-prepend"></i> Add Seller
                                </a>
                            </div>  
                        </div>
                    </div>          
                    <div class="table-responsive">
                        <table id="dataTableExample" class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>Number</th>
                                    <th>Email</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sellers as $key => $seller) 
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{ $seller->name }}</td>
                                    <td>{{ $seller->address }}</td>
                                    <td>{{ $seller->number }}</td>
                                    <td>{{ $seller->email }}</td>
                                    <td>
                                        <button data-index="{{$key}}" class="btn-edit-seller btn btn-primary btn-icon">
                                            <i data-feather="edit"></i>
                                        </button>
                                        <form class="frm-dlt-alert" action="{{route('seller.delete', $seller)}}" method="post" style="display: inline-block;">
                                            @csrf
                                            <button type="button" class="btn-dlt-alert btn btn-danger btn-icon" data-title="Delete Seller" data-text="Are you sure you want to delete this data?">
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
<div class="modal fade" id="seller-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">        
        <form id="seller-form" class="forms-sample" action="" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="seller-modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="id">
                <div class="form-group">
                    <label for="name">Seller Name</label>
                    <input type="text" class="form-control" id="name" name="name" required autofocus>                     
                </div>      
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" class="form-control" id="address" name="address" required autofocus>                     
                </div>      
                <div class="form-group">
                    <label for="number">Number</label>
                    <input type="text" class="form-control" id="number" name="number" required autofocus>                     
                </div>      
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required autofocus>                     
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