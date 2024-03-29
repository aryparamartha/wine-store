@extends('layouts.admin')

@section('title', 'WineryApp - Surat Jalan')

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
        $(".btn-dlt-alert").click(function(event){
            button = $(this);
            console.log(button);
            index = $(".btn-dlt-alert").index(button);
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false,
            })
            
            swalWithBootstrapButtons.fire({
                title: button.data("title"),
                text: button.data("text"),
                type: 'warning',
                showCancelButton: true,
                confirmButtonClass: 'ml-2',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
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
                            <h6 class="card-title">Surat Jalan Data</h6>
                        </div>
                        <div class="col-md-6 col-6">
                            <div class="flt-right">
                                <a class="btn btn-success btn-icon-text btn-edit-profile" href="{{route('tx.compliment.new')}}" id="btn-add-breakage" >
                                    <i data-feather="plus" class="btn-icon-prepend"></i> Add Surat Jalan
                                </a>
                            </div>  
                        </div>
                    </div>          
                    <div class="table-responsive">
                        <table id="dataTableExample" class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>PIC</th>
                                    <th>Date</th>
                                    <th>Payment Date</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $key => $tx) 
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{ $tx->customer->name }}</td>
                                    <td class="text-right">{{ $tx->showCurrency($tx->total) }}</td>
                                    <td>{{ $tx->employee->name }}</td>
                                    <td>{{ $tx->localTz($tx->created_at) }}</td>
                                    <td>{{ $tx->localTz($tx->payment_date) }}</td>
                                    <td>
                                        @if($tx->status=="paid")
                                        <span class="badge badge-success">{{$tx->status}}</span>
                                        @else
                                        <span class="badge badge-info">{{$tx->status}}</span>
                                        @endif
                                    </td>
                                    <td>{{ $tx->payment_type }}</td>
                                    <td>
                                        <a href="{{route('tx.compliment.invoice', $tx)}}" target="_blank" class="btn btn-light btn-icon">
                                            <i data-feather="printer"></i>
                                        </a>
                                        {{-- @if($tx->status!="paid") --}}
                                        <a href="{{route('tx.compliment.draft', $tx)}}" class="btn btn-primary btn-icon">
                                            <i data-feather="edit"></i>
                                        </a>
                                        {{-- @endif --}}
                                        @if($tx->status=="unpaid")
                                        <form class="frm-dlt-alert" action="{{route('tx.compliment.delete', $tx)}}" method="post" style="display: inline-block;">
                                            @csrf
                                            <button type="button" class="btn-dlt-alert btn btn-danger btn-icon" data-title="Delete Draft Invoice" data-text="Are you sure you want to delete this draft invoice?">
                                                <i data-feather="trash"></i>
                                            </button>
                                        </form> 
                                        @endif
                                        
                                        @if($tx->payment_type=="transfer")
                                        <button data-src="{{$tx->getTransferProof()}}" target="_blank" class="btn btn-proof btn-light btn-icon">
                                            <i data-feather="file"></i>
                                        </button>
                                        @endif
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
@endsection