$(function(){



    var getUrlParameter = function getUrlParameter(sParam) {
        var sPageURL = decodeURIComponent(window.location.search.substring(1)),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;
        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');
            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }
    };


    /**
     * подгрузхка актинвых заявок
     */
    // function activeApp(){
    //     jQuery.ajax({
    //         type: "GET",
    //         url: "/site/active",
    //         success: function (data) {
    //             $('#sitdesk-upd-app-active').empty().html(data);
    //         },
    //     });
    // }

    /**
     * подгружаем по интервалу
     * если есть запрос по посику, то не обновляем
     */
    setInterval(function () {
        activeApp();
    }, 15000);

    /**
     * подгрузхка уведмлений
     */
    // function notify(){
    //     jQuery.ajax({
    //         type: "GET",
    //         url: "/ajax-notify",
    //         success: function (data) {
    //             $('#sitdesk-notify').empty().html(data);
    //         },
    //     });
    // }

    /**
     * подгружаем по интервалу
     */
    setInterval(function () {
        notify();
    }, 15000);



    /**
     * site/main
     * Замена англ букв на русские
     * Живая подгрузка данных при поиске
     */
    $('.notify-btn-delete').click(function () {

    });

    $(document).on('click', '.notify-btn-delete', function () {
        var id = $(this).attr('data-id');
        if (confirm("Вы точно хотите удлаить?")) {
            jQuery.ajax({
                type: "GET",
                url: "/ajax-notify",
                data: { del: id },
                success: function (data) {
                    $('#notify-' + id).fadeOut();
                },
            });
        }
    });



    // /**
    //  * подгрузхка список поиска
    //  */
    // function searchHistory(){
    //     jQuery.ajax({
    //         type: "GET",
    //         url: "/site/ram",
    //         success: function (data) {
    //             $('#sitdesk-search-history-vis').remove();
    //             $('#sitdesk-search-history').empty().html(data);
    //         },
    //     });
    // }
    //
    // /**
    //  * подгружаем по интервалу
    //  * если есть запрос по посику, то не обновляем
    //  */
    // setInterval(function () {
    //     searchHistory();
    // }, 5000);


    $('#sitdesk-form-hide-btn').click(function () {
        $('#sitdesk-form-hide').fadeToggle();
        $(this).hide();
    });
    $('#sitdesk-search-history-btn').click(function () {
        $('#sitdesk-search-history').fadeToggle();
    });

});


