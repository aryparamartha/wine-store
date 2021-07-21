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
    $("#customer_id").select2({placeholder: "Select Customer"});
    $("#seller_id").select2({placeholder: "Select Seller"});
    $(".select-goods").select2({placeholder: "Select Product"});

    var total = 0;

    $("#btn-add-goods").click(function(){
        $("#goods-cart").append(itemTemplate);
        $(".select-goods").select2({placeholder: "Select Product"});

        render_cart_number();
    });
    $("#goods-cart").on('change blur', '.select-goods', change_selected_goods)
    $("#goods-cart").on('change blur', '.cart-qty', change_qty)
    $("#goods-cart").on('change blur', '.cart-price', change_price)
    $("#goods-cart").on('change blur', '.cart-disc', change_disc)
    $("#goods-cart").on('change blur', '.cart-disc-price', change_disc_price)
    $("#goods-cart").on('click', '.btn-dlt-cart', delete_cart)

    function calculate_sub_total(index){
        let price = $(".cart-price").eq(index).val();
        let qty = $(".cart-qty").eq(index).val();
        let disc = $(".cart-disc").eq(index).val();
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
        }
    }

    function change_qty(){
        let index = $(this).index(".cart-qty");
        check_stock(index);
        change_discount(index);
    }

    function change_selected_goods(){
        let index = $(this).index(".select-goods"); 
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
        $("#goods-cart > tr").eq(index).remove();
        render_cart_number();
        calculate_total();
    }

    function calculate_total(){
        let total = 0;
        $(".cart-sub-total-input").each(function( index ) {
            total+= parseInt($(this).val());
        });
        let tax = 0.1 * total;
        let grand_total = total + tax;
        
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