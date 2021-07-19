@extends('layouts.admin')

@section('title', 'WineryApp - New Regular Transaction')

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
<script src="{{asset('assets/js/custom/tx.js')}}"></script>
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
</script>
@endsection

@section('content')
<form name="memberForm" class="forms-sample" action="{{route('tx.regular.store')}}" method="POST" enctype='multipart/form-data'>
    @csrf
<div class="page-content">
    <div class="row">
        <div class="col-md-4 grid-margin">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">New Regular Transaction</h4>
                            <div class="row">
                                <!--
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="name">Invoice ID</label>
                                        <input type="text" class="form-control" id="nama_pegawai" name="nama_pegawai" placeholder="Putra Dinata" value="" required autofocus>
                                    </div>
                                </div>
                                -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="customer_id" class="w-100">Customer</label>
                                        <select id="customer_id" name="customer_id" class="w-100 form-control" required>
                                            <option></option>
                                            @foreach($customers as $customer)
                                            <option value="{{$customer->id}}">{{$customer->id}} - {{$customer->name}}</option>
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
                                            <option value="{{$seller->id}}">{{$seller->id}} - {{$seller->name}}</option>
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
                                        <label for="name">Total</label>
                                        <h3 class="cart-grand-total text-right">Rp0</h3>
                                        <input type="hidden" id="total" name="total" value="0">
                                        <input type="hidden" id="tax" name="tax" value="0">
                                        <input type="hidden" id="grand_total" name="grand_total" value="0">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                    <button type="button" id="btn-draft" class="btn btn-primary mr-2 w-100">Keep Open</button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                    <button type="button" id="btn-cash" class="btn btn-light mr-2 w-100">Cash</button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                    <button type="button" id="btn-transfer" class="btn btn-light mr-2 w-100">Transfer</button>
                                    </div>
                                </div>
                                <input type="hidden" id="payment_type" name="payment_type" value="" />
                                <input type="hidden" id="status" name="status" value="unpaid" />
                                <div id="transfer-proof-body" class="col-md-12" style="display: none !important">
									<div class="form-group">
										<label>Upload Transfer Proof</label>
										<input type="file" name="tf_proof" class="file-upload-default">
										<div class="input-group col-xs-12">
											<input type="text" class="form-control file-upload-info" disabled="" placeholder="Upload Image">
											<span class="input-group-append">
												<button class="file-upload-browse btn btn-primary" type="button">Upload</button>
											</span>
										</div>
									</div>
                                </div>
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
                                                    <i data-feather="trash"></i>
                                                </button>
                                            </td>
                                        </tr>
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
                                            <td class="cart-total text-right">-</td>
                                        </tr>
                                        <tr>
                                            <td colspan=6 class="text-right">Tax (10%):</td>
                                            <td class="cart-tax text-right">-</td>
                                        </tr>
                                        <tr>
                                            <td colspan=6 class="text-right">Grand Total:</td>
                                            <td class="cart-grand-total text-right">-</td>
                                        </tr>
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