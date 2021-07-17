@extends('layouts.invoice')

@section('title', 'WineryApp - Receiving')

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
    $(document).ready(function () {
        window.print()
    });        
</script>
@endsection

@section('custom-css')
<link rel="stylesheet" href="{{asset('assets/css/custom.css')}}">
<style>
    .table-invoice {
        width: 100%;
    }

    .table-invoice, .table-invoice tr, .table-invoice td,  .table-invoice th {
        border: 1px solid #000;
        padding: 5px;
        border-collapse: collapse !important;
    }
</style>
@endsection


@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="row mb-20">
                    <div class="col-md-6 col-6">
                        <h5>RECEIVING</h5>
                        <h5>{{ $receiving->invoice_id }}</h5>
                        {{ $receiving->receiving_date }}
                        <br/>
                        <br/>
                        <table style="width: 100%">
                            <thead>
                                <tr>
                                    <th width="50%">From :</th>
                                    <th>To :</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{$receiving->supplier->name}}</td>
                                    <td>{{$comp_profile->name}}</td>
                                </tr>
                                <tr>
                                    <td>{{$receiving->supplier->address}}</td>
                                    <td>{{$comp_profile->address}}</td>
                                </tr>
                                <tr>
                                    <td>{{$receiving->supplier->number}}</td>
                                    <td>{{$comp_profile->phone}}</td>
                                </tr>
                                <tr>
                                    <td>{{$receiving->supplier->email}}</td>
                                    <td>{{$comp_profile->email}}</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>{{$comp_profile->website}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6 col-6">
                        <div class="flt-right">
                            <img max-height="110px" width="110px" src="/{{$comp_profile->logo}}" />
                        </div>  
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table-invoice">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th width=50%>Item Name</th>
                                <th class="text-right">Qty</th>
                                <th>Unit</th>
                                <th class="text-right">Price</th>
                                <th class="text-right">Sub Total</th>
                            </tr>
                        </thead>
                        <tbody id="goods-cart">
                            @foreach($items as $key => $item)
                            <tr>
                                <td class="cart-no">{{ $key+1 }}</td>
                                <td>{{ $item->goods->code}} - {{ $item->goods->name}}</td>
                                <td class="text-right">{{ $item->qty }}</td>
                                <td>{{ $item->unit->name }}</td>
                                <td class="text-right">{{ $receiving->showCurrency($item->price) }}</td>
                                <td class="text-right">{{ $receiving->showCurrency($item->sub_total) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan=5 class="text-right"><b>Grand Total :</b></td>
                                <td class="cart-grand-total text-right"><b>{{ $receiving->showCurrency($receiving->grand_total) }}</b></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection