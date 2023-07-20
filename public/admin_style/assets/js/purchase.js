$(document).ready(function() {
    $('.add-button').click(function(e) {
        e.preventDefault();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var product_id = $(this).closest('.product_data').find('.product').val();
        var size = $(this).closest('.product_data').find('.size').val();
        var qty = $(this).closest('.product_data').find('.qty').val();
        var price = $(this).closest('.product_data').find('.price').val();

        $.ajax({
            url: "/inventory/purchase/addtopurcase",
            method: "POST",
            data: {
                'size': size,
                'product_id': product_id,
                'qty': qty,
                'price': price,
            },
            success: function(response) {
                if (response.error_status == 'error') {
                    $('.product_id_err').text(response.status);

                } else if (response.error_status2 == 'error2') {
                    $('.product_qty_err').text(response.status2);

                } else if (response.error_status3 == 'error3') {
                    $('.product_price_err').text(response.status3);

                } else if (response.error_status4 == 'error4') {
                    $('.product_size_err').text(response.status4);

                } else {
                    location.reload();
                }
            },
        });
    });
});

// Delete Cart Data
$(document).ready(function() {

    $('.delete_cart_data').click(function(e) {
        e.preventDefault();

        var product_id = $(this).closest(".cartpage").find('.product_id').val();

        var data = {
            '_token': $('input[name=_token]').val(),
            "product_id": product_id,
        };

        // $(this).closest(".cartpage").remove();

        $.ajax({
            url: '/inventory/purchase/delete-from-cart',
            type: 'POST',
            data: data,
            headers: {
                Accept: '*/*'
            },
            success: function(response) {
                window.location.reload();
            }
        });
    });

});
