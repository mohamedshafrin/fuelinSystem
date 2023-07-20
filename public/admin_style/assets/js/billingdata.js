// Delete Cart Data
$(document).ready(function() {

    $('.delete_cart_data').click(function(e) {
        e.preventDefault();

        var product_id = $(this).closest(".cartpage").find('.product_id').val();

        var data = {
            '_token': $('input[name=_token]').val(),
            "product_id": product_id,
        };


        $.ajax({
            url: '/billsystem/addtobill/delete',
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


$(document).ready(function() {

    $('.changeQuantity').change(function(e) {
        e.preventDefault();
        var quantity = $(this).closest(".cartpage").find('.qty_input').val();
        var product_id = $(this).closest(".cartpage").find('.product_id').val();

        var data = {
            '_token': $('input[name=_token]').val(),
            'quantity': quantity,
            'product_id': product_id,
        };

        $.ajax({
            url: '/billsystem/addtobill/update',
            type: 'POST',
            data: data,
            success: function(response) {
                window.location.reload();
            }
        });
    });

});