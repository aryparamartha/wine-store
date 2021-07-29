@extends('layouts.admin')

@section('title', 'WineryApp - Draft Invoice')

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
<script src="{{asset('assets/js/file-upload.js')}}"></script>
@endsection

@section('custom-js')
<script src="{{asset('assets/js/data-table.js')}}"></script>
<script src="{{asset('assets/custom/js/tx.js')}}"></script>
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
    
    var itemTemplate = `
        <tr>
            <td class="cart-no">1</td>
            <td>
                <input type="hidden" name="goods_id[]" class="cart-goods-id" />
                <input type="hidden" name="unit_id[]" class="cart-unit-id" />
                <input type="hidden" name="sub_total[]" class="cart-sub-total-input" />
                <select class="cart-goods select-goods form-control" style="width:100% !important" required>
                    <option></option>
                    @foreach($goods as $good)
                    <option value="{{$good}}">{{$good->code}} - {{$good->name}}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="text" class="cart-qty form-control" name="qty[]" placeholder="Qty" value="" required autofocus></td>
            <td class="cart-unit"></td>
            <td><input type="text" class="cart-price form-control" name="price[]" /></td>
            <td>
                <div style="display: block">%
                <input style="display: inline-block; width: 60px !important" type="text" class="cart-disc form-control" name="disc[]" placeholder="Disc" value="" autofocus>
                Rp<input style="display: inline-block; width: 100px !important" type="text" class="cart-disc-price form-control" />
                </div>
            </td>
            <td class="cart-sub-total text-right"></td>
            <td>
                <button type="button" class="btn-dlt-cart btn btn-danger btn-icon" data-title="Delete Product" data-text="Are you sure you want to delete this data?">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>
            </td>
        </tr>
        `;
    
    var paymentTemplate = `
        <div class="col-md-12">
            <div class="form-group border-bottom">
                <div class="row">
                    <div class="col-md-12 pb-2">
                        <label for="payment_id">Payment <span class="payment-no">1</span></label>
                        <button type="button" style="float:right" class="btn-dlt-payment btn btn-danger btn-icon" data-title="Delete Product" data-text="Are you sure you want to delete this data?">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
                    </div>
                    <div class="col-md-6">
                        <input value="{{$payment_types[0]->name}}" type="hidden" name="payment_name[]" class="payment-name"/>
                        <select name="payment_id[]" class="select-payment-id w-100 form-control">
                            @foreach($payment_types as $payment)
                            <option value="{{$payment->id}}">{{$payment->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 pb-2">
                        <input type="text" class="paid-amount form-control" name="paid_amount[]">
                    </div>
                    <div class="col-md-12 transfer-proof-body" style="display:none">
                        <div class="form-group">
                            <input type="file" name="payment_proof[]" class="file-upload-default">
                            <div class="input-group col-xs-12">
                                <input type="text" class="form-control file-upload-info" disabled="" placeholder="Upload Proof">
                                <span class="input-group-append">
                                    <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
</script>
@endsection

@section('content')
<form id="form-tx" class="forms-sample" action="{{route('tx.compliment.update', $tx)}}" method="POST" enctype='multipart/form-data'>
    @csrf
<div class="page-content">
    <div class="row">
        <div class="col-md-4 grid-margin">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Draft Invoice</h4>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="name">Invoice ID</label>
                                        <input type="text" class="form-control" value="{{$tx->invoice_id}}" required readonly>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="customer_id" class="w-100">Customer</label>
                                        <select id="customer_id" name="customer_id" class="w-100 form-control" required>
                                            <option></option>
                                            @foreach($customers as $customer)
                                            <?php $selected = ($tx->customer_id==$customer->id)?"selected":""; ?>
                                            <option {{$selected}} value="{{$customer->id}}">{{$customer->id}} - {{$customer->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="seller_id" class="w-100">Seller</label>
                                        <select id="seller_id" name="seller_id" class="w-100 form-control">
                                            <option></option>
                                            @foreach($sellers as $seller)
                                            <option {{($tx->seller_id==$seller->id)?"selected":""}} value="{{$seller->id}}">{{$seller->id}} - {{$seller->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="name">Grand Total</label>
                                        <h3 class="cart-total text-right">{{$tx->showCurrency($tx->total)}}</h3>
                                        <input type="hidden" id="total" name="total" value="{{$tx->total}}">
                                        <input type="hidden" id="_tax" value="0">
                                        <input type="hidden" id="tax" name="tax" value="{{$tx->tax}}">
                                        <input type="hidden" id="grand_total" name="grand_total" value="{{$tx->grand_total}}">
                                        <input type="hidden" id="total_paid" name="total_paid" value="0">
                                        <input type="hidden" id="remainder" name="remainder" value="{{$tx->remainder}}">
                                        <input type="hidden" id="already_paid" name="already_paid" value="{{$tx->total_paid}}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="name">Total Paid</label>
                                        </div>
                                        <div class="col-md-6">
                                            <h5 class="cart-total-paid text-right">{{$tx->showCurrency($tx->total_paid)}}</h5>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">Remainder</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <h5 class="cart-remainder text-right">{{$tx->showCurrency($tx->remainder)}}</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                    <?php
                                    $disabled = "";
                                    $payment_class = "btn-light";
                                    $keep_open_class = "btn-primary";
                                    if($tx->status=="down payment"){
                                        $disabled = "disabled";
                                        $payment_class = "btn-primary";
                                        $keep_open_class = "btn-light";
                                    }
                                    ?>
                                    <button  {{ $disabled }} type="button" id="btn-draft" class="btn {{ $keep_open_class }} mr-2 w-100">Keep Open</button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                    <button type="button" id="btn-payment" class="btn {{ $payment_class }} mr-2 w-100">Payment</button>
                                    </div>
                                </div>
                                @if($tx->status=="unpaid")
                                <div id="payment-wrap" style="display:none">
                                @else
                                <div id="payment-wrap">
                                @endif
                                    @foreach($tx->payment_logs as $key=>$payment_log)
                                    <div class="col-md-12">
                                        <div class="form-group border-bottom">
                                            <div class="row">
                                                <div class="col-md-12 pb-2">
                                                    <label for="payment_id">Payment <span class="payment-no">{{$key+1}}</span></label>
                                                    @if($payment_log->payment_type_id!=1)
                                                    <button type="button" style="float:right" class="btn-payment-proof btn btn-light btn-icon">
                                                    <i data-feather="file"></i>
                                                    </button>
                                                    @endif
                                                </div>
                                                <div class="col-md-6">
                                                    @foreach($payment_types as $payment)
                                                    @if($payment->id==$payment_log->payment_type_id)
                                                    <input value="{{$payment->name}}" type="text" class="form-control" readonly>
                                                    @endif
                                                    @endforeach
                                                </div>
                                                <div class="col-md-6 pb-2">
                                                    <input value="{{$payment_log->paid_amount}}" type="text" class="form-control" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                    <div id="payment-body">
                                        @if(!count($tx->payment_logs))
                                        <div class="col-md-12">
                                            <div class="form-group border-bottom">
                                                <div class="row">
                                                    <div class="col-md-12 pb-2">
                                                        <label for="payment_id">Payment <span class="payment-no">1</span></label>
                                                        <button type="button" style="float:right" class="btn-dlt-payment btn btn-danger btn-icon" data-title="Delete Product" data-text="Are you sure you want to delete this data?">
                                                            <i data-feather="trash"></i>
                                                        </button>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input value="{{$payment_types[0]->name}}" type="hidden" name="payment_name[]" class="payment-name"/>
                                                        <select name="payment_id[]" class="select-payment-id w-100 form-control">
                                                            @foreach($payment_types as $payment)
                                                            <option value="{{$payment->id}}">{{$payment->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 pb-2">
                                                        <input type="text" class="paid-amount form-control" name="paid_amount[]">
                                                    </div>
                                                    <div class="col-md-12 transfer-proof-body" style="display:none">
                                                        <div class="form-group">
                                                            <input type="file" name="payment_proof[]" class="file-upload-default">
                                                            <div class="input-group col-xs-12">
                                                                <input type="text" class="form-control file-upload-info" disabled="" placeholder="Upload Proof">
                                                                <span class="input-group-append">
                                                                    <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                        <button type="button" id="btn-add-payment" class="btn btn-secondary mr-2 w-100">Add Payment</button>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="status" name="status" value="{{$tx->status}}" />
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-success mr-2 w-100">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Items</h4>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th width="40%">Name</th>
                                            <th style="min-width:90px">Qty</th>
                                            <th>Unit</th>
                                            <th style="min-width:130px">Price</th>
                                            <th>Discount</th>
                                            <th>Sub Total</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="goods-cart">
                                        @foreach($details as $key => $detail)
                                        <tr>
                                            <td class="cart-no">{{$key+1}}</td>
                                            <td>
                                                <input value="{{$detail->goods_id}}" type="hidden" name="goods_id[]" class="cart-goods-id" />
                                                <input value="{{$detail->unit_id}}" type="hidden" name="unit_id[]" class="cart-unit-id" />
                                                <input value="{{$detail->sub_total}}" type="hidden" name="sub_total[]" class="cart-sub-total-input" />
                                                <select class="cart-goods select-goods form-control" style="width:100% !important" required>
                                                    <option></option>
                                                    @foreach($goods as $good)
                                                    <option {{($detail->goods_id==$good->id)?"selected":""}} value="{{$good}}">{{$good->code}} - {{$good->name}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="text" class="cart-qty form-control" name="qty[]" placeholder="Qty" value="{{$detail->qty}}" required autofocus></td>
                                            <td class="cart-unit">{{$detail->unit->name}}</td>
                                            <td><input value="{{$detail->price}}" type="text" class="cart-price form-control" name="price[]" /></td>
                                            <td>
                                                <div style="display: block">%
                                                <input value="{{$detail->disc}}" style="display: inline-block; width: 60px !important" type="text" class="cart-disc form-control" name="disc[]" placeholder="Disc" value="" autofocus>
                                                Rp<input value="{{($detail->price * $detail->qty) * ($detail->disc/100)}}" style="display: inline-block; width: 100px !important" type="text" class="cart-disc-price form-control" />
                                                </div>
                                            </td>
                                            <td class="cart-sub-total text-right">{{$tx->showCurrency($detail->sub_total)}}</td>
                                            <td>
                                                <button type="button" class="btn-dlt-cart btn btn-danger btn-icon" data-title="Delete Product" data-text="Are you sure you want to delete this data?">
                                                    <i data-feather="trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        
                                        <tr>
                                            <td colspan=7>           
                                                <button id="btn-add-goods" type="button" class="btn btn-primary btn-icon w-100" data-title="Delete Product" data-text="Are you sure you want to delete this data?">
                                                    <i data-feather="plus"></i>ADD ITEM
                                                </button>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td colspan=6 class="text-right">Total:</td>
                                            <td class="cart-total text-right">{{$tx->showCurrency($tx->total)}}</td>
                                        </tr>
                                        <!-- <tr>
                                            <td colspan=6 class="text-right">Tax (10%):</td>
                                            <td class="cart-tax text-right">{{$tx->showCurrency($tx->tax)}}</td>
                                        </tr>
                                        <tr>
                                            <td colspan=6 class="text-right">Grand Total:</td>
                                            <td class="cart-grand-total text-right">{{$tx->showCurrency($tx->grand_total)}}</td>
                                        </tr> -->
                                    </tfoot>
                                </table>
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