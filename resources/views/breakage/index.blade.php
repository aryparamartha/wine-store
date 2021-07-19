@extends('layouts.admin')

@section('title', 'WineryApp - Update Stock')

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
        if ($(".select2").length) {
            $(".select2").select2({placeholder: "Select Product"});
        }

        var breakages = {!! $breakages !!}
        $('#btn-add-breakage').click(function () {
            $('#id').val("");
            $('#goods_id').val("").trigger('change');
            $('#qty').val("");
            $('#reason').val("");
            $('#breakage-form').attr('action', "{{route('breakage.store')}}");
            $('#breakage-modal-title').html("Add Stock Adjustment");
            $('#breakage-modal').modal('show');
        });

        $('.btn-edit-breakage').click(function() {
            var index = $(this).data('index');
            var breakage = breakages[index]
            $('#breakage-form').attr('action', '/breakage/update/' + breakage.id);
            $('#breakage-modal-title').html("Update Stock Adjustment");
            $('#breakage-modal').modal('show');
            $('#id').val(breakage.id);
            $('#goods_id').val(breakage.goods_id).trigger('change');
            $('#qty').val(breakage.qty);
            $('#reason').val(breakage.reason);
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
                            <h6 class="card-title">Stock Adjustment Data</h6>
                        </div>
                        <div class="col-md-6 col-6">
                            <div class="flt-right">
                                <a class="btn btn-success btn-icon-text btn-edit-profile" href="javascript:void(0)" id="btn-add-breakage" >
                                    <i data-feather="plus" class="btn-icon-prepend"></i> Add Adjustment
                                </a>
                            </div>  
                        </div>
                    </div>          
                    <div class="table-responsive">
                        <table id="dataTableExample" class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Created by</th>
                                    <th>Date</th>
                                    <th>Reason</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($breakages as $key => $breakage) 
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{ $breakage->goods->code }} - {{ $breakage->goods->name }}</td>
                                    <td>{{ $breakage->qty }}</td>
                                    <td>{{ $breakage->employee->name }}</td>
                                    <td>{{ $breakage->created_at }}</td>
                                    <td>{{ $breakage->reason }}</td>
                                    <td>
                                        <button data-index="{{$key}}" class="btn-edit-breakage btn btn-primary btn-icon">
                                            <i data-feather="edit"></i>
                                        </button>
                                        <form class="frm-dlt-alert" action="{{route('breakage.delete', $breakage)}}" method="post" style="display: inline-block;">
                                            @csrf
                                            <button type="button" class="btn-dlt-alert btn btn-danger btn-icon" data-title="Delete Adjustment" data-text="Are you sure you want to delete this data?">
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
<div class="modal fade" id="breakage-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">        
        <form id="breakage-form" class="forms-sample" action="" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="breakage-modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="id">
                <div class="form-group">
                    <label for="goods_id" class="w-100">Product</label>
                    <select id="goods_id" name="goods_id" class="select2 form-control" required>
                        <option></option>
                        @foreach($goods as $good)
                        <option value="{{$good->id}}">{{$good->name}}</option>
                        @endforeach
                    </select>                    
                </div>  
                <div class="form-group">
                    <label for="qty">Qty</label>
                    <input type="number" class="form-control" id="qty" name="qty" required autofocus>                     
                </div>      
                <div class="form-group">
                    <label for="reason">Reason</label>
                    <input type="text" class="form-control" id="reason" name="reason" required autofocus>                     
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