$(document).ready(function() {
    $('.category').on('change', function() {
        var category = $('.category').val();

        if (category) {
            $.ajax({
                url: '/inventory/inventory/getsubcategory/' + category,
                type: "GET",
                dataType: "json",
                success: function(data) {


                    $('.subcategory').empty();
                    $.each(data, function(key, value) {
                        $('.subcategory').append('<option value="' + key + '">' + value + '</option>');
                    });


                }
            });
        } else {
            $('.subcategory').empty();
        }
    });
});