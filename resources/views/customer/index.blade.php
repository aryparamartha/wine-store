@extends('layouts.admin')

@section('title', 'WineryApp - Customers')

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
            $(".select2").select2({placeholder: "Select PIC"});
        }

        var customers = {!! $customers !!}
        $('#btn-add-customer').click(function () {
            $('#name').val("");
            $('#address').val("");
            $('#number').val("");
            $('#email').val("");
            $('#pic').val("").trigger('change');
            $('#customer-form').attr('action', "{{route('customer.store')}}");
            $('#customer-modal-title').html("Add Customer");
            $('#customer-modal').modal('show');
        });

        $('.btn-edit-customer').click(function() {
            var index = $(this).data('index');
            var customer = customers[index]
            $('#customer-form').attr('action', '/customer/update/' + customer.id);
            $('#customer-modal-title').html("Update Customer");
            $('#customer-modal').modal('show');
            $('#name').val(customer.name);
            $('#address').val(customer.address);
            $('#number').val(customer.number);
            $('#email').val(customer.email);
            $('#pic').val(customer.pic).trigger('change');
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
                            <h6 class="card-title">Customer Data</h6>
                        </div>
                        <div class="col-md-6 col-6">
                            <div class="flt-right">
                                <a class="btn btn-success btn-icon-text btn-edit-profile" href="javascript:void(0)" id="btn-add-customer" >
                                    <i data-feather="plus" class="btn-icon-prepend"></i> Add Customer
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
                                    <th>PIC</th>
                                    <th>Type</th>
                                    <th>Note</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customers as $key => $customer)
                                     
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{ $customer->name }}</td>
                                    @if (is_null($customer->address))
                                    <td> </td>
                                    @else
                                    <td>{{ $customer->address }}</td>
                                    @endif

                                    @if (is_null($customer->number))
                                    <td> </td>
                                    @else
                                    <td>{{ $customer->number }}</td>
                                    @endif

                                    @if (is_null($customer->email))
                                    <td> </td>
                                    @else
                                    <td>{{ $customer->email }}</td>
                                    @endif
                                    
                                    @if (empty($customer->employee->name))
                                    <td> </td>
                                    @else
                                    <td>{{ $customer->employee->name }}</td>
                                    @endif
                                    {{-- <td>{{ $customer->employee->name }}</td> --}}
                                    <td>{{ $customer->type }}</td>
                                    @if (is_null($customer->note))
                                    <td></td>
                                    @else
                                    <td>{{ $customer->note }}</td>
                                    @endif
                                    {{-- <td>{{ $customer->note }}</td> --}}
                                    <td>
                                        <button data-index="{{$key}}" class="btn-edit-customer btn btn-primary btn-icon">
                                            <i data-feather="edit"></i>
                                        </button>
                                        <form class="frm-dlt-alert" action="{{route('customer.delete', $customer)}}" method="post" style="display: inline-block;">
                                            @csrf
                                            <button type="button" class="btn-dlt-alert btn btn-danger btn-icon" data-title="Delete Customer" data-text="Are you sure you want to delete this data?">
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
<div class="modal fade" id="customer-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">        
        <form id="customer-form" class="forms-sample" action="" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="customer-modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="id">
                <div class="form-group">
                    <label for="name">Customer Name</label>
                    <input type="text" class="form-control" id="name" name="name" required autofocus>                     
                </div>      
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea class="form-control" name="address" id="address" form="customer-form"></textarea>
                    {{-- <input type="text" class="form-control" id="address" name="address" autofocus>                      --}}
                </div>      
                <div class="form-group">
                    <label for="number">Number</label>
                    <input type="text" class="form-control" id="number" name="number" autofocus>                     
                </div>      
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" autofocus>                     
                </div>      
                <div class="form-group">
                    <label for="pic" class="w-100">PIC</label>
                    <select id="pic" name="pic" class="select2 form-control">
                        <option></option>
                        @foreach($employees as $employee)
                        <option value="{{$employee->id}}">{{$employee->name}}</option>
                        @endforeach
                    </select>                    
                </div>  
                <div class="form-group">
                    <label for="type" class="w-100">Type</label>
                    <select id="type" name="type" class="select2 form-control" required>
                        <option value="company">company</option>
                        <option value="person">person</option>
                    </select>                    
                </div>  
                <div class="form-group">
                    <label for="note">Note</label>
                    <input type="text" class="form-control" id="note" name="note" autofocus>                     
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