$(document).ready(function () {
    var clear_timer;
    updateStockList();
    $('#stock_upload_form').on('submit', function (event) {
        $('#message').html('');
        event.preventDefault();
        $.ajax({
            url: "../../php/upload.php",
            method: "POST",
            data: new FormData(this),
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                $('#import').attr('disabled', 'disabled');
                $('#import').val('Importing');
            },
            success: function (data) {
                if (data.success) {
                    $('#total_data').text(data.total_line);

                    start_import();

                    clear_timer = setInterval(get_import_data, 2000);

                    //$('#message').html('<div class="alert alert-success">CSV File Uploaded</div>');

                }
                if (data.error) {
                    $('#message').html('<div class="alert alert-danger">' + data.error + '</div>');
                    $('#import').attr('disabled', false);
                    $('#import').val('Import');
                }
            }
        })

    });

    $('#find_stock').on('submit', function (event) {

        $('#message').html('');
        event.preventDefault();
        $.ajax({
            url: "../../php/find_stock.php",
            method: "POST",
            data: new FormData(this),
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                if (data.success) {
                    message = '<p>Stock : ' + $('#find_stock').find('select[name="stock_list"]').val() + '</p>';
                    message += '<p>BUY THIS STOCK ON ' + data.data.buy_stock_date + ' at RS.' + data.data.buy_stock_price +
                        ' AND SELL THIS STOCK ON ' + data.data.sell_stock_date + ' AT RS.' + data.data.sell_stock_price + '' +
                        ' to book a profit of RS.' + data.data.profit + ' per stock</p>' +
                        '<p>Mean : ' + data.data.mean + '</p>' +
                        '<p>Std deviation : ' + data.data.std_dev + '</p>';

                    $('#message').html('<div class="alert alert-success">' + message + '</div>');
                }
                if (data.error) {
                    $('#message').html('<div class="alert alert-danger">' + data.error + '</div>');

                }
            }
        })

    });

    function start_import() {
        $('#process').css('display', 'block');
        $.ajax({
            url: "../../php/import.php",
            success: function (data) {

                if (data.error) {
                    $('#message').html('<div class="alert alert-danger">' + data.error + '</div>');
                    $('#import').attr('disabled', false);
                    $('#import').val('Import');
                }
            }
        });

    }

    function get_import_data() {
        $.ajax({
            url: "../../php/process.php",
            success: function (data) {
                var total_data = $('#total_data').text();
                var width = Math.round((data / total_data) * 100);
                $('#process_data').text(data);
                $('.progress-bar').css('width', width + '%');
                if (width >= 100) {
                    clearInterval(clear_timer);
                    $('#process').css('display', 'none');
                    $('#file').val('');
                    $('#message').html('<div class="alert alert-success">Data Successfully Imported</div>');
                    $('#import').attr('disabled', false);
                    $('#import').val('Import');
                    updateStockList();
                }
            }
        })
    }

    function updateStockList() {

        //stock_list
        $('#stock_list').empty();
        $('#stock_list')
            .append($("<option></option>")
                .attr("value", '')
                .text('Select stock'));
        $.ajax({
            url: "../../php/stock_list.php",
            dataType: 'json',
            success: function (data) {
                console.log(data);
                $.each(data, function (key, value) {
                    //console.log(value);
                    $('#stock_list')
                        .append($("<option></option>")
                            .attr("value", value)
                            .text(value));
                });

            }
        })

    }


});
