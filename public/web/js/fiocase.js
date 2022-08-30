/**
 * Created by 01gig on 15.08.2017.
 */
$(function () {

    $('#ajaxExpandFiocase').click(function () {
        var val = $(this).val();
        if (val !== '') {
            getContent(val, 1);
        }
    });

    $('#ajaxFiocase').click(function () {
        var val = $("#ajaxFiocase").val();
        if (val !== '') {
            getContent(val, null);
        }
    });


    /**
     * Из текста выдергиваем ФИО, получаем АйПи и логин
     * @param val АйДи заявки
     * @param type обычный/расширенный поиск.
     */
    function getContent(val, type) {
        var prg1 = "<div class='progress'></div>";
        var prg2 = "<div class='progress-bar my-2 progress-bar-striped bg-success progress-bar-animated' role='progressbar' aria-valuenow='75' aria-valuemin='0' aria-valuemax='100' style='width: 100%'></div>";
        $(prg1).prepend(prg2);
        $('#ajaxFioacaseContent').html(prg2);
        $.ajax({
            type: "post",
            url: "site/fiocase",
            data: {id: val, type: type},
            timeout: 5000,
            cache: false,
            // dataType: "json",
            success: function (data) {
                if (data) {
                    $("#ajaxFioacaseContent").empty();
                    $("#ajaxFioacaseContent").prepend(data);
                    // $("#ajaxFioacaseContent").fadeIn(200);
                    // $('#ajaxFiocase').hide();
                    // $('#collapseExample').slideToggle(200);
                    $('#collapseExample').addClass('collapse show');
                }
            }
        });
    }
});