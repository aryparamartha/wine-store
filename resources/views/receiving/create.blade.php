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

@section('custom-css')
<style>
    .readable {
        width: 100%;
        height: 100%;
        display:block;
    }
</style>
@endsection

@section('custom-js')
<script src="{{asset('assets/js/data-table.js')}}"></script>
<script>
    $(document).ready(function () {
        $("#supplier_id").select2({placeholder: "Select Customer"});
        $("#seller_id").select2({placeholder: "Select Seller"});
        $(".select-goods").select2({placeholder: "Select Product"});
        $("#unit_id").select2({placeholder: "Select Unit"});
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
        var new_goods_template = "";

        $("#btn-add-goods").click(function(){
            let template = `
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
                        `+ new_goods_template +`
                    </select>
                </td>
                <td><input type="text" class="cart-qty form-control" name="qty[]" placeholder="Qty" value="" required autofocus></td>
                <td class="cart-unit"></td>
                <td><input type="text" name="price[]" class="cart-price form-control" /></td>
                <td class="cart-sub-total text-right"></td>
                <td><input type="text" name="selling_price[]" class="cart-selling-price form-control" /></td>
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
        $("#goods-cart").on('change', '.cart-price', calculate_subtotal)
        $("#goods-cart").on('change', '.cart-price', calculate_total)
        $("#goods-cart").on('click', '.btn-dlt-cart', delete_cart)

        

        function change_selected_goods(){
            let index = $(this).index(".select-goods"); 
            let goods = JSON.parse($(this).val());
            let qty = $(".cart-qty").eq(index).val()
            let sub_total = goods.selling_price * qty;

            $(".cart-goods-id").eq(index).val(goods.id)
            $(".cart-unit-id").eq(index).val(goods.unit.id)
            $(".cart-unit").eq(index).html(goods.unit.name)
            calculate_subtotal();
            
            
        }

        function delete_cart(){
            let index = $(this).index(".btn-dlt-cart"); 
            $("#goods-cart > tr").eq(index).remove();
            render_cart_number();
            calculate_total();
        }

        function calculate_total(){
            let total = 0;
            console.log("kepanggil")
            
            $(".cart-sub-total-input").each(function( index ) {
                let sub_total = parseFloat($(this).val());
                console.log(sub_total)
                total+=  isNaN(sub_total) ? 0 : sub_total; 
                console.log(total)
                
            });
            
            let grand_total = total;
            
            $("#total").val(total);
            
            $("#grand_total").val(grand_total);

            $(".cart-total").html(showCurrency(total));
            
            $(".cart-grand-total").html(showCurrency(grand_total));
            
        }

        function calculate_subtotal(){
            let index = $(this).index(".cart-price");
            let price = $(".cart-price").eq(index).val();
            let qty = $(".cart-qty").eq(index).val();
            let total = 0;
            

            let sub_total = price * qty;
            
            
            $(".cart-sub-total").eq(index).html(showCurrency(sub_total))
            $(".cart-sub-total").eq(index).data("val", sub_total)
            $(".cart-sub-total-input").eq(index).val(sub_total)

            
        }

        

        // function calculate_total(){
            
        //     let total = 0;
        //     $(".cart-sub-total").each(function( index ) {
        //         total+= parseInt(($(this).val() == "") ? 0 : $(this).val());
        //     });

        //     console.log(total)
            
        //     $("#grand_total").val(total);
        // }

        function render_cart_number(){
            $(".cart-no").each(function( index ) {
                $( this ).html(index + 1);
            });
        }

        $('#btn-new-goods').click(function () {
            $('#code').val("");
            $('#name').val("");
            $('#unit_id').val("");
            $('#amount').val("");
            $('#purchase_price').val("");
            $('#selling_price').val("");
            $('#goods-modal').modal('show');
        });

        $("#goods-form").submit(function(e){
            e.preventDefault();
            let form = $(this);
            
            $.ajax({
                type: "POST",
                url: form.attr('action'),
                data: form.serialize(),
                success: function(response) {
                    let goods = response.data;
                    let new_item = `<option value='`+JSON.stringify(goods)+`'>`+goods.code+` - `+goods.name+`</option>`;
                    new_goods_template += new_item;
                    $('#goods-modal').modal('hide');
                    $(".select-goods").append(new_item);
                    $(".select-goods").select2({placeholder: "Select Product"});
                }, 
                fail: function(){
                    Swal.fire({
                        type: 'error',
                        title: "Failed to add new goods! Please check your internet connection! ",
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            });
        })
    });   
</script>
@endsection

@section('content')
<form name="memberForm" class="forms-sample" action="{{route('receiving.store')}}" method="POST">
    @csrf
<div class="page-content">
    <div class="row">
        <div class="col-md-3 grid-margin">
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
                                        <label for="name">Grand Total</label>
                                        <h3 class="cart-grand-total text-right">Rp0</h3>
                                        <input type="hidden" id="total" name="total" value="0">
                                        <input type="hidden" id="grand_total" class="form-control" name="grand_total" value="0">
                                        
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
        <div class="col-md-9 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-6">
                            <h4 class="card-title">Items</h4>
                        </div>
                        <div class="col-md-6 col-6">
                            <div class="flt-right" style="float: right">
                                <a href="javascript:void(0)" id="btn-new-goods" class="btn btn-success btn-icon-text btn-edit-profile">
                                    <i data-feather="plus" class="btn-icon-prepend"></i> Create Product
                                </a>
                            </div>  
                        </div>
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table easy-edit">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th width="40%">Name</th>
                                            <th style="min-width:90px">Qty</th>
                                            <th>Unit</th>
                                            <th style="min-width:120px">Price</th>
                                            <th style="min-width:120px">Sub Total</th>
                                            <th style="min-width:120px">Selling Price</th>
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
                                            <td><input type="text" name="price[]" class="cart-price form-control" /></td>
                                            <td class="cart-sub-total text-right" value=""></td>
                                            <td><input type="text" name="selling_price[]" class="cart-selling-price form-control" /></td>
                                            <td>
                                                <button type="button" class="btn-dlt-cart btn btn-danger btn-icon" data-title="Delete Product" data-text="Are you sure you want to delete this data?">
                                                    <i data-feather="trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        
                                        <tr>
                                            <td colspan=10  >           
                                                <button id="btn-add-goods" type="button" class="btn btn-primary btn-icon w-100" data-title="Delete Product" data-text="Are you sure you want to delete this data?">
                                                    <i data-feather="plus"></i>ADD ITEM
                                                </button>
                                            </td>
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


{{-- Modal --}}
<div class="modal fade" id="goods-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">        
        <form id="goods-form" class="forms-sample" action="{{route('goods.add')}}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="goods-modal-title">Add New Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="id">
                <input value="{{ Auth::user()->id }}" type="hidden" name="added_by" id="added_by">
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
                    <select id="unit_id" name="unit_id" class="w-100" required>
                        <option></option>
                        @foreach($units as $unit)
                        <option value="{{$unit->id}}">{{$unit->name}}</option>
                        @endforeach
                    </select>
                </div>
                {{-- <div class="form-group">
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
                </div> --}}
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