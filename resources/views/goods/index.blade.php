@extends('layouts.admin')

@section('title', 'WineryApp - Goods')

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
            $(".select2").select2({placeholder: "Select Unit"});
        }

        var goods = {!! $goods !!}
        $('#btn-add-goods').click(function () {
            $('#code').val("");
            $('#name').val("");
            $('#unit_id').val("");
            $('#amount').val("");
            $('#purchase_price').val("");
            $('#selling_price').val("");
            $('#goods-form').attr('action', "{{route('goods.store')}}");
            $('#goods-modal-title').html("Add Goods");
            $('#goods-modal').modal('show');
        });

        $('.btn-edit-goods').click(function() {
            var index = $(this).data('index');
            var good = goods[index]
            $('#goods-form').attr('action', '/goods/update/' + good.id);
            $('#goods-modal-title').html("Update Goods");
            $('#goods-modal').modal('show');
            $('#code').val(good.code);
            $('#name').val(good.name);
            $('#unit_id').val(good.unit_id).trigger('change');
            $('#amount').val(good.amount);
            $('#purchase_price').val(good.purchase_price);
            $('#selling_price').val(good.selling_price);
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
                            <h6 class="card-title">Goods Data</h6>
                        </div>
                        <div class="col-md-6 col-6">
                            <div class="flt-right">
                                <a class="btn btn-success btn-icon-text btn-edit-profile" href="javascript:void(0)" id="btn-add-goods" >
                                    <i data-feather="plus" class="btn-icon-prepend"></i> Add Goods
                                </a>
                            </div>  
                        </div>
                    </div>          
                    <div class="table-responsive">
                        <table id="dataTableExample" class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Amount</th>
                                    <th>Unit</th>
                                    <th>Purchase Price</th>
                                    <th>Selling Price</th>
                                    <th>Added By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($goods as $key => $good) 
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{ $good->code }}</td>
                                    <td>{{ $good->name }}</td>
                                    <td class="text-right">{{ $good->amount }}</td>
                                    <td>{{ $good->unit->name }}</td>
                                    <td class="text-right">{{ $good->showCurrency($good->purchase_price) }}</td>
                                    <td class="text-right">{{ $good->showCurrency($good->selling_price) }}</td>
                                    <td>{{ $good->employee->name }}</td>
                                    <td>
                                        <button data-index="{{$key}}" class="btn-edit-goods btn btn-primary btn-icon">
                                            <i data-feather="edit"></i>
                                        </button>
                                        <form class="frm-dlt-alert" action="{{route('goods.delete', $good)}}" method="post" style="display: inline-block;">
                                            @csrf
                                            <button type="button" class="btn-dlt-alert btn btn-danger btn-icon" data-title="Delete Goods" data-text="Are you sure you want to delete this data?">
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
<div class="modal fade" id="goods-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">        
        <form id="goods-form" class="forms-sample" action="" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="goods-modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="id">
                <div class="form-group">
                    <label for="code">Code</label>
                    <input type="text" class="form-control" id="code" name="code" required autofocus>                     
                </div>      
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" required autofocus>                     
                </div>      
                <div class="form-group">
                    <label for="unit" class="w-100">Unit</label>
                    <select id="unit_id" name="unit_id" class="select2 w-100" required>
                        <option></option>
                        @foreach($units as $unit)
                        <option value="{{$unit->id}}">{{$unit->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="number" class="form-control" id="amount" name="amount" required autofocus>                     
                </div>
                <div class="form-group">
                    <label for="purchase_price">Purchase Price</label>
                    <input type="number" class="form-control" id="purchase_price" name="purchase_price" required autofocus>                     
                </div>
                <div class="form-group">
                    <label for="selling_price">Selling Price</label>
                    <input type="number" class="form-control" id="selling_price" name="selling_price" required autofocus>                     
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