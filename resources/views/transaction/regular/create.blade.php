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
<script>
    $(document).ready(function () {
        $("#customer_id").select2({placeholder: "Select Customer"});
        $("#seller_id").select2({placeholder: "Select Seller"});
        $(".select-goods").select2({placeholder: "Select Product"});
    });
</script>
<script>
    const truncateByDecimalPlace = (value, numDecimalPlaces) => Math.trunc(value * Math.pow(10, numDecimalPlaces)) / Math.pow(10, numDecimalPlaces)
    function numberWithCommas(x) {
        var parts = x.toString().replace(".",",").split(",");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        if(typeof parts[1] == 'undefined') parts[1] = "00";
        return parts.join(",");
    }

    function showCurrency(x){
        return "Rp" + numberWithCommas(x);
    }
     $(document).ready(function () {
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

        var total = 0;

        $("#btn-add-goods").click(function(){
            let template = `
            <tr>
                <td class="cart-no">1</td>
                <td>
                    <input type="hidden" name="goods_id[]" class="cart-goods-id" />
                    <input type="hidden" name="price[]" class="cart-price" />
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
                <td class="cart-sub-total text-right"></td>
                <td>
                    <button type="button" class="btn-dlt-cart btn btn-danger btn-icon" data-title="Delete Product" data-text="Are you sure you want to delete this data?">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    </button>
                </td>
            </tr>
            `;
            $("#goods-cart").append(template);
            $(".select-goods").select2({placeholder: "Select Product"});

            render_cart_number();
        });
        $("#goods-cart").on('change', '.select-goods', change_selected_goods)
        $("#goods-cart").on('change', '.cart-qty', change_qty)
        $("#goods-cart").on('click', '.btn-dlt-cart', delete_cart)

        function change_qty(){
            console.log(1)
            let index = $(this).index(".cart-qty"); 
            let price = JSON.parse($(".cart-price").eq(index).val());
            let qty = $(this).val();
            let sub_total = price * qty;

            $(".cart-sub-total").eq(index).html(showCurrency(sub_total))
            $(".cart-sub-total").eq(index).data("val", sub_total)
            $(".cart-sub-total-input").eq(index).val(sub_total)
            calculate_total();
        }

        function change_selected_goods(){
            let index = $(this).index(".select-goods"); 
            let goods = JSON.parse($(this).val());
            let qty = $(".cart-qty").eq(index).val()
            let sub_total = goods.selling_price * qty;

            $(".cart-goods-id").eq(index).val(goods.id)
            $(".cart-price").eq(index).val(goods.selling_price)
            $(".cart-unit-id").eq(index).val(goods.unit.id)
            $(".cart-unit").eq(index).html(goods.unit.name)
            $(".cart-sub-total").eq(index).html(showCurrency(sub_total))
            $(".cart-sub-total").eq(index).data("val", sub_total)
            $(".cart-sub-total-input").eq(index).val(sub_total)
            calculate_total();
        }

        function delete_cart(){
            let index = $(this).index(".btn-dlt-cart"); 
            $("#goods-cart > tr").eq(index).remove();
            render_cart_number();
            calculate_total();
        }

        function calculate_total(){
            let total = 0;
            $(".cart-sub-total").each(function( index ) {
                total+= parseInt($(this).data("val"));
            });
            let tax = 1/11 * total;
            let grand_total = total;
            
            $("#total").val(total);
            $("#tax").val(tax.toFixed(2));
            $("#grand_total").val(grand_total);

            $(".cart-total").html(showCurrency(total));
            $(".cart-tax").html(showCurrency(tax.toFixed(2)));
            $(".cart-grand-total").html(showCurrency(grand_total));
        }

        function render_cart_number(){
            $(".cart-no").each(function( index ) {
                $( this ).html(index + 1);
            });
        }

        $("#btn-cash").click(function(){changePaymentAndStatus("cash")})
        $("#btn-transfer").click(function(){changePaymentAndStatus("transfer")})
        $("#btn-draft").click(function(){changePaymentAndStatus("draft")})

        function changePaymentAndStatus(param){
            if(param=="cash"){
                $('#btn-cash').removeClass('btn-light').addClass('btn-primary');
                $('#btn-transfer').removeClass('btn-primary').addClass('btn-light');
                $('#btn-draft').removeClass('btn-primary').addClass('btn-light');
                $("#transfer-proof-body").hide();
                $("#payment_type").val(param);
                $("#status").val("paid");
            } else if(param=="transfer"){
                $('#btn-cash').removeClass('btn-primary').addClass('btn-light');
                $('#btn-transfer').removeClass('btn-light').addClass('btn-primary');
                $('#btn-draft').removeClass('btn-primary').addClass('btn-light');
                $("#transfer-proof-body").show();
                $("#payment_type").val(param);
                $("#status").val("paid");
            } else {
                $('#btn-cash').removeClass('btn-primary').addClass('btn-light');
                $('#btn-transfer').removeClass('btn-primary').addClass('btn-light');
                $('#btn-draft').removeClass('btn-light').addClass('btn-primary');
                $("#transfer-proof-body").hide();
                $("#payment_type").val("");
                $("#status").val("unpaid");
            }
        }
    });   
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
                                <div class="col-md-6">
                                    <div class="form-group">
                                    <button type="button" id="btn-cash" class="btn btn-primary mr-2 w-100">Cash</button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                    <button type="button" id="btn-transfer" class="btn btn-light mr-2 w-100">Transfer</button>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                    <button type="button" id="btn-draft" class="btn btn-light mr-2 w-100">Save as Draft</button>
                                    </div>
                                </div>
                                <input type="hidden" id="payment_type" name="payment_type" value="cash" />
                                <input type="hidden" id="status" name="status" value="paid" />
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
                                            <th style="min-width:105px">Qty</th>
                                            <th>Unit</th>
                                            <th>Price</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="goods-cart">
                                        <tr>
                                            <td class="cart-no">1</td>
                                            <td>
                                                <input type="hidden" name="goods_id[]" class="cart-goods-id" />
                                                <input type="hidden" name="price[]" class="cart-price" />
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
                                            <td colspan=5>           
                                                <button id="btn-add-goods" type="button" class="btn btn-primary btn-icon w-100" data-title="Delete Product" data-text="Are you sure you want to delete this data?">
                                                    <i data-feather="plus"></i>ADD ITEM
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- <tr>
                                            <td colspan=4 class="text-right">Total:</td>
                                            <td class="cart-total text-right">-</td>
                                        </tr> -->
                                        <tr>
                                            <td colspan=4 class="text-right">Tax (10%):</td>
                                            <td class="cart-tax text-right">-</td>
                                        </tr>
                                        <tr>
                                            <td colspan=4 class="text-right">Grand Total:</td>
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