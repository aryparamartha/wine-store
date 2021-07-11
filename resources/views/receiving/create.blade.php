@extends('layouts.admin')

@section('title', 'WineryApp - New Receiving')

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
        $("#supplier_id").select2({placeholder: "Select Customer"});
        $("#seller_id").select2({placeholder: "Select Seller"});
        $(".select-goods").select2({placeholder: "Select Goods"});
    });
</script>
<script>
    function numberWithCommas(x) {
        var parts = x.toString().split(",");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
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
                    <input type="hidden" name="unit_id[]" class="cart-unit-id" />
                    <select class="cart-goods select-goods form-control" style="width:100% !important" required>
                        <option></option>
                        @foreach($goods as $good)
                        <option value="{{$good}}">{{$good->code}} - {{$good->name}}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="text" class="cart-qty form-control" name="qty[]" placeholder="Qty" value="" required autofocus></td>
                <td class="cart-unit"></td>
                <td>
                    <input type="text" name="sub_total[]" class="cart-sub-total form-control" />
                </td>
                <td>
                    <button type="button" class="btn-dlt-cart btn btn-danger btn-icon" data-title="Delete Goods" data-text="Are you sure you want to delete this data?">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    </button>
                </td>
            </tr>
            `;
            
            
            $("#goods-cart").append(template);
            $(".select-goods").select2({placeholder: "Select Goods"});

            render_cart_number();
        });
        $("#goods-cart").on('change', '.select-goods', change_selected_goods)
        $("#goods-cart").on('change', '.cart-sub-total', calculate_total)
        $("#goods-cart").on('click', '.btn-dlt-cart', delete_cart)

        function change_selected_goods(){
            let index = $(this).index(".select-goods"); 
            let goods = JSON.parse($(this).val());
            let qty = $(".cart-qty").eq(index).val()
            let sub_total = goods.selling_price * qty;

            $(".cart-goods-id").eq(index).val(goods.id)
            $(".cart-price").eq(index).val(goods.selling_price)
            $(".cart-unit-id").eq(index).val(goods.unit.id)
            $(".cart-unit").eq(index).html(goods.unit.name)
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
                total+= parseInt(($(this).val() == "") ? 0 : $(this).val());
            });
            
            $("#grand_total").val(total);

            $(".cart-grand-total").html(showCurrency(total));
        }

        function render_cart_number(){
            $(".cart-no").each(function( index ) {
                $( this ).html(index + 1);
            });
        }
    });   
</script>
@endsection

@section('content')
<form name="memberForm" class="forms-sample" action="{{route('receiving.store')}}" method="POST">
    @csrf
<div class="page-content">
    <div class="row">
        <div class="col-md-4 grid-margin">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">New Receiving</h4>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="invoice_id" class="w-100">Receiving No</label>
                                        <input name="invoice_id" type="text" class="w-100 form-control" required />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="supplier_id" class="w-100">Supplier</label>
                                        <select id="supplier_id" name="supplier_id" class="w-100 form-control" required>
                                            <option></option>
                                            @foreach($suppliers as $supplier)
                                            <option value="{{$supplier->id}}">{{$supplier->id}} - {{$supplier->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="receiving_date" class="w-100">Receiving Date</label>
                                        <input name="receiving_date" type="date" class="w-100 form-control" required />
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
                    <div class="row">
                        <div class="col-md-6 col-6">
                            <h4 class="card-title">Items</h4>
                        </div>
                        <div class="col-md-6 col-6">
                            <div class="flt-right" style="float: right">
                                <a class="btn btn-success btn-icon-text btn-edit-profile" href="{{route('receiving.new')}}" id="btn-add-breakage" >
                                    <i data-feather="plus" class="btn-icon-prepend"></i> Create Goods
                                </a>
                            </div>  
                        </div>
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th width="40%">Name</th>
                                            <th style="min-width:60px">Qty</th>
                                            <th>Unit</th>
                                            <th style="min-width:120px">Sub Total</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="goods-cart">
                                        <tr>
                                            <td class="cart-no">1</td>
                                            <td>
                                                <input type="hidden" name="goods_id[]" class="cart-goods-id" />
                                                <input type="hidden" name="unit_id[]" class="cart-unit-id" />
                                                <select class="cart-goods select-goods form-control" style="width:100% !important" required>
                                                    <option></option>
                                                    @foreach($goods as $good)
                                                    <option value="{{$good}}">{{$good->code}} - {{$good->name}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="text" class="cart-qty form-control" name="qty[]" placeholder="Qty" value="" required autofocus></td>
                                            <td class="cart-unit"></td>
                                            <td>
                                                <input type="text" name="sub_total[]" class="cart-sub-total form-control" />
                                            </td>
                                            <td>
                                                <button type="button" class="btn-dlt-cart btn btn-danger btn-icon" data-title="Delete Goods" data-text="Are you sure you want to delete this data?">
                                                    <i data-feather="trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        
                                        <tr>
                                            <td colspan=5>           
                                                <button id="btn-add-goods" type="button" class="btn btn-primary btn-icon w-100" data-title="Delete Goods" data-text="Are you sure you want to delete this data?">
                                                    <i data-feather="plus"></i>ADD ITEM
                                                </button>
                                            </td>
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