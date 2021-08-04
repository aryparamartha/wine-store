@extends('layouts.invoice')

@section('title', 'WineryApp - Invoice')

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
                        @if ($type=="regular")
                        <h5>INVOICE</h5>
                        @else
                        <h5>SURAT JALAN</h5>
                        @endif
                        
                        <h5>{{ $tx->invoice_id }}</h5>
                        {{ $tx->payment_date }}
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
                                    <td>{{$comp_profile->name}}</td>
                                    <td>{{$tx->customer->name}}</td>
                                </tr>
                                <tr>
                                    <td>{{$comp_profile->pt_name}}</td>
                                    <td>{{$tx->customer->address}}</td>
                                </tr>
                                <tr>
                                    <td>{{$comp_profile->address}}</td>
                                    <td>{{$tx->customer->number}}</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>{{$tx->customer->email}}</td>
                                </tr>
                                {{-- website --}}
                                <tr>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6 col-6">
                        {{-- <div class="flt-right">
                            <img max-height="110px" width="110px" src="/{{$comp_profile->logo}}" />
                        </div>   --}}
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table-invoice">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th width=50%>Item Name</th>
                                <th>Qty</th>
                                <th>Unit</th>
                                <th class="text-right">Price</th>
                                <th class="text-right">Discount</th>
                                <th class="text-right">Sub Total</th>
                            </tr>
                        </thead>
                        <tbody id="goods-cart">
                            @foreach($items as $key => $item)
                            <tr>
                                <td class="cart-no">{{ $key+1 }}</td>
                                <td>{{ $item->goods->code}} - {{ $item->goods->name}}</td>
                                <td>{{ $item->qty }}</td>
                                <td>{{ $item->unit->name }}</td>
                                <td class="text-right">{{ $tx->showCurrency($item->price) }}</td>
                                <td class="text-right">{{ $item->disc }}%</td>
                                <td class="text-right">{{ $tx->showCurrency($item->sub_total) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            @if($type=="regular")
                            <tr>
                                <td colspan=6 class="text-right">Total :</td>
                                <td class="cart-total text-right">{{ $tx->showCurrency($tx->total) }}</td>
                            </tr>
                            <tr>
                                <td colspan=6 class="text-right">Tax (10%) :</td>
                                <td class="cart-tax text-right">{{ $tx->showCurrency($tx->tax) }}</td>
                            </tr>
                            <tr>
                                <td colspan=6 class="text-right"><b>Grand Total :</b></td>
                                <td class="cart-grand-total text-right"><b>{{ $tx->showCurrency($tx->grand_total) }}</b></td>
                            </tr>
                            @else
                            <tr>
                                <td colspan=6 class="text-right"><b>Grand Total :</b></td>
                                <td class="cart-grand-total text-right"><b>{{ $tx->showCurrency($tx->total) }}</b></td>
                            </tr>
                            @endif
                        </tfoot>
                    </table>
                    <table style="width: 60%; margin-top: 60px">
                        <tr>
                            <td align="center">Sender</td>
                            <td align="center">Receiver</td>
                        </tr>
                        <tr>
                            <td style="padding-top: 50px" align="center">__________________________</td>
                            <td style="padding-top: 50px" align="center">__________________________</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection