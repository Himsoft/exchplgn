jQuery(function($){
    var change_valute_process = function ($element) {
        if($.inArray($element.val(), ['UAH','USD'])+1){
            $element.parent().parent().find('.city').show();
        }else{
            $element.parent().parent().find('.city').hide();
            $element.parent().parent().find('.city input').val('');
        }

        if($.inArray($element.val(), ['BTC', 'ETH', 'LTC'])+1){
            $element.parent().parent().find('.purse').show();
        }else{
            $element.parent().parent().find('.purse').hide();
            $element.parent().parent().find('.purse input').val('');
        }

        if($.inArray($element.val(), ['UAH_P24'])+1){
            $element.parent().parent().find('.card').show();
        }else{
            $element.parent().parent().find('.card').hide();
            $element.parent().parent().find('.card input').val('');
        }



        $('.crypto-hide, .nal-hide').hide();
        if($.inArray($('#forvalute').val(), ['BTC', 'ETH', 'LTC'])+1 && $.inArray($('#tovalute').val(), ['BTC', 'ETH', 'LTC'])+1){
            $('.crypto-hide, .nal-hide').hide();
            //$('.crypto-hide input, .nal-hide input').val('');
        }

        if(($.inArray($('#forvalute').val(), ['UAH','USD'])+1 || $.inArray($('#tovalute').val(), ['UAH','USD'])+1) && !$.inArray($('#forvalute').val(), ['UAH_P24'])+1 && !$.inArray($('#tovalute').val(), ['UAH_P24'])+1){
            $('.crypto-hide').show();
            $('.nal-hide').hide();
            //$('.nal-hide input').val('');
        }

        if($.inArray($('#forvalute').val(), ['UAH_P24'])+1 || $.inArray($('#tovalute').val(), ['UAH_P24'])+1){
            $('.crypto-hide, .nal-hide').show();
        }


        if(!$('.crypto-hide:visible').length){
            $('.crypto-hide input').val('');
        }

        if(!$('.nal-hide:visible').length){
            $('.nal-hide input').val('');
        }

        if ($('#forvalute').val() != '' && $('#tovalute').val() != '' && $('#sumfor').val() != '' && $('#forvalute').val() != null && $('#tovalute').val() != null && $('#sumfor').val() != null) {
            $.ajax({
                type: "POST",
                url: exhanger_params.ajaxurl,
                data: {
                    action: 'get_course',
                    for: $('#forvalute').val(),
                    to: $('#tovalute').val(),
                    sumfor: $('#sumfor').val(),
                },
                success: function (response) {
                    var resp = JSON.parse(response);
                    //console.log('AJAX response : ', response);
                    $('#sumto').val(resp.request_vars);
                    $('#sumto').attr('data-cource',(resp.cource).toFixed(2));
                    $('#sumto').removeAttr('readonly');
                    var forvalute = $('#forvalute').val().split('_');
                    var tovalute = $('#tovalute').val().split('_');
                    var cource = (resp.cource < 1) ? (1 / resp.cource).toFixed(2) + ' ' + forvalute[0] + ' = 1 ' + tovalute[0] : '1 ' + forvalute[0] + ' = ' + (resp.cource).toFixed(2) + ' ' + tovalute[0];
                    $('.cource').text(cource);
                    //$('.cource').text('1 ' + forvalute[0] + ' = ' + resp.cource + ' ' + tovalute[0]);
                }
            });
        }
        if ($('#tovalute').val() != '' && $('#tovalute').val() != null) {
            $.ajax({
                type: "POST",
                url: exhanger_params.ajaxurl,
                data: {
                    action: 'get_minsum_reserv',
                    for: $('#forvalute').val(),
                    to: $('#tovalute').val(),
                },
                success: function (response) {
                    var resp = JSON.parse(response);
                    console.log('AJAX response : ', response);

                    $('#sumfor').attr('min',parseFloat(resp.minsum));
                    $('#sumto').attr('max',parseFloat(resp.reserv));
                    if(parseFloat($('#sumfor').val()) < parseFloat(resp.minsum)){
                        $('#sumfor').val(parseFloat(resp.minsum).toFixed(2));
                    }

                    var tovalutereserv = $('#tovalute').val().split('_');
                    $('.reserv').text('Резерв: ' + resp.reserv + ' ' + tovalutereserv[0]);
                    var forvalute = $('#forvalute').val().split('_');
                    var tovalute = $('#tovalute').val().split('_');
                    var cource = (resp.cource < 1) ? (1 / resp.cource).toFixed(2) + ' ' + forvalute[0] + ' = 1 ' + tovalute[0] : '1 ' + forvalute[0] + ' = ' + (resp.cource).toFixed(2) + ' ' + tovalute[0];
                    $('.cource').text(cource);

                    /*$('#sumto').val(resp.request_vars);
                    $('#sumto').attr('data-cource',resp.cource);
                    $('#sumto').removeAttr('readonly');
                    var forvalute = $('#forvalute').val().split('_');
                    var tovalute = $('#tovalute').val().split('_');
                    $('.cource').text('1 ' + forvalute[0] + ' = ' + resp.cource + ' ' + tovalute[0]);
                    */
                }
            });
        }
    }

    $('body').on('change','#tovalute, #forvalute',function () {
        change_valute_process($(this));
    });

    $('body').on('change','#tovalute',function () {
        var thval = $(this).val();
        $('#forvalute option').each(function () {
            if($(this).val() == thval) $(this).attr('disabled', 'disabled');
            else                       $(this).removeAttr('disabled');
        });
        /*if($.inArray($(this).val(),['USD','EUR','UAH'])+1){
            $('#forvalute option').each(function () {
                if($.inArray($(this).val(),['USD','EUR','UAH'])+1){
                    $(this).attr('disabled', 'disabled');
                }
            });
        }else{
            $('#forvalute option').each(function () {
                $(this).removeAttr('disabled');
            });
        }*/

    });

    $('body').on('change','#forvalute',function () {
        var thval = $(this).val();
        $('#tovalute option').each(function () {
            if($(this).val() == thval) $(this).attr('disabled', 'disabled');
            else                       $(this).removeAttr('disabled');
        });
        /*if($.inArray($(this).val(),['USD','EUR','UAH'])+1){
            $('#tovalute option').each(function () {
                if($.inArray($(this).val(),['USD','EUR','UAH'])+1){
                    $(this).attr('disabled', 'disabled');
                }
            });
        }else{
            $('#tovalute option').each(function () {
                $(this).removeAttr('disabled');
            });
        }*/
    });

    $('#sumfor').keyup(function (e) {
        console.log(parseFloat($(this).val()));
        console.log(parseFloat($(this).attr('min')));
        if(parseFloat($(this).val()) < parseFloat($(this).attr('min'))){
            console.log(parseFloat($(this).val()));
            $(this).val(parseFloat($(this).attr('min')).toFixed(2));
        }

        if ($('#forvalute').val() != '' && $('#tovalute').val() != '' && $('#sumfor').val() != '' && $('#forvalute').val() != null && $('#tovalute').val() != null && $('#sumfor').val() != null) {
            $.ajax({
                type: "POST",
                url: exhanger_params.ajaxurl,
                data: {
                    action: 'get_course',
                    for: $('#forvalute').val(),
                    to: $('#tovalute').val(),
                    sumfor: $('#sumfor').val(),
                },
                success: function (response) {
                    var resp = JSON.parse(response);
                    //console.log('AJAX response : ', response);

                    if(parseFloat(resp.request_vars) > parseFloat($('#sumto').attr('max'))){
                        var mins = parseFloat($('#sumto').attr('max')/resp.cource);
                        if(mins < parseFloat($('#sumfor').attr('min'))){
                            $('#sumfor, #sumto').val('');
                            return false;
                        }

                        $('#sumfor').val(parseFloat(mins).toFixed(2));
                        $('#sumto').val(parseFloat($('#sumto').attr('max')).toFixed(2));
                    }else{
                        $('#sumto').val(resp.request_vars);
                    }

                    $('#sumto').attr('data-cource',(resp.cource).toFixed(2));
                    $('#sumto').removeAttr('readonly');

                    var forvalute = $('#forvalute').val().split('_');
                    var tovalute = $('#tovalute').val().split('_');
                    var cource = (resp.cource < 1) ? (1 / resp.cource).toFixed(2) + ' ' + forvalute[0] + ' = 1 ' + tovalute[0] : '1 ' + forvalute[0] + ' = ' + (resp.cource).toFixed(2) + ' ' + tovalute[0];
                    $('.cource').text(cource);
                    //$('.cource').text('Курс: ' + '1 ' + forvalute[0] + ' = ' + resp.cource + ' ' + tovalute[0]);
                }
            });
        }
        if(parseFloat($(this).val()) == 0){
            $('#sumto').val('');
        }

        if(parseFloat($(this).val()) < parseFloat($(this).attr('min'))){
            $(this).val($(this).attr('min')).change();
        }
    });
    $('#sumto').keyup(function (e) {
        if ($('#forvalute').val() != '' && $('#tovalute').val() != '' && $(this).val() != '' && $('#forvalute').val() != null && $('#tovalute').val() != null && $(this).val() != null) {
            if(parseFloat($(this).val()) > parseFloat($(this).attr('max'))){
                $(this).val($(this).attr('max').toFixed(2));
            }
            var sumfor = parseFloat($(this).val() / $(this).data('cource'));
            if(sumfor < $('#sumfor').attr('min')){
                $('#sumfor').val(parseFloat($('#sumfor').attr('min')).toFixed(2));
                var sumto = parseFloat($('#sumfor').attr('min'))*$(this).data('cource');
                $(this).val(sumto.toFixed(2));
            }else {
                $('#sumfor').val(sumfor.toFixed(2));
            }
        }else{
            $('#sumfor').val('');
        }
    });

    $('#sumto, #sumfor').keyup(function (e) {
        console.log(e.which);
        if(e.which >= 37 && e.which <= 40) return;
        $(this).val($(this).val().replace(/[^\d\.]/g, ""));
        if($(this).val().match(/\./g) && $(this).val().match(/\./g).length > 1) {
            $(this).val($(this).val().substr(0, $(this).val().lastIndexOf(".")));
        }
    });

    $(document).ready(function () {
        change_valute_process($('#forvalute'));
        change_valute_process($('#tovalute'));
    });
    $('body').on('click','.transfer-ico',function () {
        var forval = $('#forvalute').val();
        var toval = $('#tovalute').val();
        $('#forvalute option,#tovalute option').removeAttr('selected');
        $('#forvalute option,#tovalute option').removeAttr('disabled');
        $('#forvalute option[value="'+toval+'"]').attr('selected', 'selected');
        $('#tovalute option[value="'+forval+'"]').attr('selected', 'selected');
        $('#forvalute, #tovalute').change();
    });

});