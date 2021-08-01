var status = "draft";
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

function getOptionTemplate(){
    let options = "";
    goods_list.forEach((goods, index) => {
        options += "<option value='"+JSON.stringify(goods)+"'>"+goods.code+" - "+goods.name+"</option>";
    })
    return options;
}

function getItemTemplate(){
    return `
    <tr>
        <td class="cart-no">1</td>
        <td>
            <input type="hidden" name="goods_id[]" class="cart-goods-id" />
            <input type="hidden" name="unit_id[]" class="cart-unit-id" />
            <input type="hidden" name="sub_total[]" class="cart-sub-total-input" />
            <select class="cart-goods select-goods form-control" style="width:100% !important" required>
                <option></option>
                `+getOptionTemplate()+`
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
}

$(function() {
    'use strict';
    $(function() {
        $("#payment-wrap").on('click', '.file-upload-browse', function(e) {
            var file = $(this).parent().parent().parent().find('.file-upload-default');
            file.trigger('click');
        });
        $("#payment-wrap").on('change', '.file-upload-default', function() {
            $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
        });
    });

    $('.btn-payment-proof').click(function () {
        $('#img-proof').attr("src", $(this).data('src'));
        $('#proof-modal').modal('show');
    });
});

$(function() {
    status = ($("#status").val() != "unpaid") ? "payment" : "draft";
    $("#customer_id").select2({placeholder: "Select Customer", width: '100%'});
    $("#seller_id").select2({placeholder: "Select Seller", width: '100%'});
    $(".select-payment-id").select2({placeholder: "Select Payment Method", width: '100%'});
    $(".select-goods").select2({placeholder: "Select Product", width: '100%'});

    $("#goods-cart").on('change blur', '.select-goods', change_selected_goods)
    $("#goods-cart").on('change blur', '.cart-qty', change_qty)
    $("#goods-cart").on('change blur', '.cart-price', change_price)
    $("#goods-cart").on('change blur', '.cart-disc', change_disc)
    $("#goods-cart").on('change blur', '.cart-disc-price', change_disc_price)
    $("#goods-cart").on('click', '.btn-dlt-cart', delete_cart)
    
    $("#payment-wrap").on('click', '.btn-dlt-payment', delete_payment)
    $("#payment-wrap").on('change blur', '.select-payment-id', change_payment)
    $("#payment-wrap").on('keyup', '.paid-amount', calculate_current_paid)

    $("#btn-add-goods").click(function(){
        $("#goods-cart").append(getItemTemplate());
        $(".select-goods").select2({placeholder: "Select Product", width: '100%'});

        render_cart_number();
    });
    $("#btn-add-payment").click(function(){
        $("#payment-body").append(paymentTemplate);
        $(".select-payment-id").select2({placeholder: "Select Payment Method", width: '100%'});

        render_payment_number();
    });

    function change_payment(){
        let index = $(this).index(".select-payment-id");
        let payment_type = $(this).select2('data')[0].text;
        $(".payment-name").eq(index).val(payment_type);
        if($(this).val()!="1"){
            $(".transfer-proof-body").eq(index).show();
        } else {
            $(".transfer-proof-body").eq(index).hide();
            $(".file-upload-default").eq(index).val("");
            $(".file-upload-info").eq(index).val("");
        }
    }

    function calculate_sub_total(index){
        let price = $(".cart-price").eq(index).val();
        let qty = $(".cart-qty").eq(index).val();
        let disc_price = $(".cart-disc-price").eq(index).val();

        let sub_total = price * qty;
        sub_total = sub_total - disc_price;
        
        $(".cart-sub-total").eq(index).html(showCurrency(sub_total))
        $(".cart-sub-total").eq(index).data("val", sub_total)
        $(".cart-sub-total-input").eq(index).val(sub_total)
    }

    function change_discount(index){
        let price = $(".cart-price").eq(index).val();
        let qty = $(".cart-qty").eq(index).val();
        let disc = $(".cart-disc").eq(index).val();
        
        let sub_total = price * qty;
        let discount = sub_total * ( disc / 100);

        $(".cart-disc-price").eq(index).val(discount)
        calculate_sub_total(index)
        calculate_total();
    }

    function change_price(){
        let index = $(this).index(".cart-price"); 
        change_discount(index);
    }

    function change_disc(){
        let index = $(this).index(".cart-disc"); 
        change_discount(index);
    }
    
    function change_disc_price(){
        let index = $(this).index(".cart-disc-price"); 
        
        let price = $(".cart-price").eq(index).val();
        let qty = $(".cart-qty").eq(index).val();
        let disc_price = $(this).val();
        
        let sub_total = price * qty;
        let discount = (disc_price/sub_total)*100;

        $(".cart-disc").eq(index).val(discount)
        change_discount(index);
    }

    function check_stock(index){
        let select_goods_val = $(".cart-goods").eq(index).val();
        let goods = JSON.parse(select_goods_val);
        let total = 0;
        $('.cart-goods-id[value="'+goods.id+'"]').each(function(){
            let index = $(this).index(".cart-goods-id");
            total += parseInt($(".cart-qty").eq(index).val());
        });

        if(total > goods.amount) {
            Swal.fire({
                type: 'error',
                title: "Input qty is more than available stock!",
                showConfirmButton: true
            });
            $(".cart-qty").eq(index).val("")
            return false;
        }
        return true;
    }

    function change_qty(){
        let index = $(this).index(".cart-qty");
        check_stock(index);
        change_discount(index);
    }

    function change_selected_goods(){
        let index = $(this).index(".cart-goods"); 
        let goods = JSON.parse($(this).val());

        $(".cart-goods-id").eq(index).val(goods.id)
        $(".cart-price").eq(index).val(goods.purchase_price)
        $(".cart-unit-id").eq(index).val(goods.unit.id)
        $(".cart-unit").eq(index).html(goods.unit.name)

        check_stock(index);
        change_discount(index);
    }

    function delete_cart(){
        let index = $(this).index(".btn-dlt-cart"); 
        if($(this).hasClass('update-stock')){
            let goods_val = $(".cart-goods").eq(index).val();
            let goods = JSON.parse(goods_val);
            let qty = parseInt($(".cart-qty").eq(index).val());
            console.log(goods);
            goods.amount+=qty;
            console.log(goods);

            $("option[value='"+goods_val+"']").each(function(){
                $(this).val(JSON.stringify(goods))
            })
            $(".select-goods").select2({placeholder: "Select Product", width: '100%'});

            let goods_index = goods_list.findIndex(x => x.id === goods.id);
            goods_list[goods_index].amount=goods.amount;

            console.log('stock updated')
        }
        $("#goods-cart > tr").eq(index).remove();
        render_cart_number();
        calculate_total();
    }

    function delete_payment(){
        let index = $(this).index(".btn-dlt-payment");
        $("#payment-body > div").eq(index).remove();
        render_payment_number();
        calculate_total()
    }

    function calculate_total(){
        let total = 0;
        let _tax = parseFloat($("#_tax").val());
        $(".cart-sub-total-input").each(function( index ) {
            let sub_total = parseFloat($(this).val());
            total+=  isNaN(sub_total) ? 0 : sub_total; 
        });
        let tax = _tax * total;
        let grand_total = total + tax;
        
        $("#total").val(total);
        $("#tax").val(tax.toFixed(2));
        $("#grand_total").val(grand_total);

        $(".cart-total").html(showCurrency(total));
        $(".cart-tax").html(showCurrency(tax.toFixed(2)));
        $(".cart-grand-total").html(showCurrency(grand_total));
        
        if(status=="draft") calculate_already_paid();
        else calculate_current_paid();
    }

    //calculate already paid (without new payments)
    function calculate_already_paid(){
        let grand_total = parseFloat($("#grand_total").val());
        let total_paid = parseFloat($("#total_paid").val());
        
        let remainder = grand_total - total_paid;
        $("#remainder").val(remainder)

        $(".cart-total-paid").html(showCurrency(total_paid.toFixed(2)));
        $(".cart-remainder").html(showCurrency(remainder.toFixed(2)));

        if(status=="payment"){
            if(remainder > 0) {
                $("#status").val("down payment");
            } else {
                $("#status").val("paid");
            }
        }
    }

    //calculate current paid (with new payments)
    function calculate_current_paid(){
        let total_paid = 0;
        let grand_total = parseFloat($("#grand_total").val());
        let already_paid = parseFloat($("#already_paid").val());
        console.log(already_paid)
        $(".paid-amount").each(function( index ) {
            let paid = parseFloat($(this).val());
            total_paid+=  isNaN(paid) ? 0 : paid; 
        });
        already_paid+=total_paid;
        $("#total_paid").val(already_paid)
        
        calculate_already_paid();
    }

    function render_cart_number(){
        $(".cart-no").each(function( index ) {
            $( this ).html(index + 1);
        });
        render_delete_item_btn()
    }
    function render_payment_number(){
        $(".payment-no").each(function( index ) {
            $( this ).html(index + 1);
        });
        render_delete_payment_btn()
    }

    function render_delete_item_btn(){
        $(".btn-dlt-cart").show();
        if($(".btn-dlt-cart").length == 1){
            $(".btn-dlt-cart").hide();
        }
    }

    function render_delete_payment_btn(){
        $(".btn-dlt-payment").show();
        if($(".btn-dlt-payment").length == 1){
            $(".btn-dlt-payment").hide();
        }
    }

    $("#btn-draft").click(function(){changePaymentAndStatus("draft")})
    $("#btn-payment").click(function(){changePaymentAndStatus("payment")})

    function changePaymentAndStatus(param){
        if(param=="draft"){
            status="draft";
            $('#btn-payment').removeClass('btn-primary').addClass('btn-light');
            $('#btn-draft').removeClass('btn-light').addClass('btn-primary');
            $("#payment-wrap").hide();
            $("#status").val("unpaid");
            
            //reset total paid & remainder
            let total = parseFloat($("#grand_total").val());
            $("#total_paid").val(0)
            $("#remainder").val(total)
            $(".cart-total-paid").html(showCurrency(0));
            $(".cart-remainder").html(showCurrency(total.toFixed(2)));
        } else if(param=="payment"){
            status="payment";
            $('#btn-draft').removeClass('btn-primary').addClass('btn-light');
            $('#btn-payment').removeClass('btn-light').addClass('btn-primary');
            $("#payment-wrap").show();

            calculate_current_paid();
        }
    }

    $("#form-tx").on("submit", function(e){
        e.preventDefault();
        let stock_valid = true;
        $(".cart-qty").each(function(index){
            if(!check_stock(index)) {
                stock_valid = false;
            }
            change_discount(index);
        }) 
        if(stock_valid){
            $(this).unbind();
            $(this).submit();
        }
    })
    render_delete_payment_btn();
    render_delete_item_btn();
    calculate_total();
});