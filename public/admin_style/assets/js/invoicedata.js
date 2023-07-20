$(document).ready(function() {
    $('.product').on('change', function() {
        var product = $('.product').val();

        if (product) {
            $.ajax({
                url: '/invoice/invoice/getsize/' + product,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    $('.size').empty();
                    $('.size').append('<option value="">-- Select Size --</option>');
                    $.each(data, function(key, value) {
                        $('.size').append('<option value="' + key + '">' + value + '</option>');
                    });
                }
            });
        } else {
            $('.size').empty();
        }
    });
});

//Fetch available Qty for all item.
$(document).ready(function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.product').on('change', function() {
        var product = $('.product').val();

        $.ajax({
            url: "/invoice/invoice/getallqty",
            method: "POST",
            data: {
                'product': product,
            },
            success: function(response) {
                if (response.error_status == 'error') {

                    $('.product').val('');
                } else {
                    var avqty = response.avqty;
                    var price = response.price;
                    var discount = response.discount;
                    $('.avqty').val(avqty);
                    $('.price').val(price);
                    $('.discount').val(discount);
                }
            },
        });
    });
});

//Fetch available Qty for Fashion item.
$(document).ready(function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.size').on('change', function() {
        var product = $('.product').val();
        var size = $('.size').val();


        $.ajax({
            url: "/invoice/invoice/getqty",
            method: "POST",
            data: {
                'product': product,
                'size': size,
            },
            success: function(response) {
                if (response.error_status == 'error') {
                    $('.size').val('');
                    $('.product').val('');
                } else {
                    var avqty = response.avqty;

                    $('.avqty').val(avqty);

                }
            },
        });
    });
});

//Delete Cookies
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
            url: '/invoice/invoice/deltocookie',
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

//Delete Cookies
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
            url: '/invoice/invoice/items/itemdeltocookie',
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