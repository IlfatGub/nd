/**
 * Created by 01gig on 15.08.2017.
 */
$(function(){
    $('#btnComment').click(function(){
        var val = $("#textComment").val();
        var idApp = $(this).val();
        if(val !=='' ){
            addComment(val, idApp);
        }
    });

    $('#textComment').keydown(function (e) {
        if (e.ctrlKey && e.keyCode == 13) {
            $("#btnComment").trigger("click");
        }
    });

    $('#olComment li').click(function() {
        var val = $(this).text();
        var idApp = $(this).val();
        if(val !=='' ){
            addComment(val, idApp);
        }
        e.preventDefault();
    });



    // Добовляем напоминание
    function addComment(val, idApp){
        var text = $.trim(val).replace(/([^>])\n/g, '$1<br/>');
        $.ajax({
            type: "GET",
            url: "admin/comment/add",
            data: { text: text, id: idApp},
            timeout:5000,
            cache: false,
            dataType: "json",
            success: function(data) {
                var elem = $("<tr id =comment" + data[1] + " style='display: none'></tr>");
                var elem2 = $("<td class='comment-main'></td>").html(text);
                $(elem2).prepend(data[0]);
                $(elem).prepend(elem2);
                $("#tableComment").prepend(elem);
                $("#comment" + data[1]).fadeIn(200);
                $("#comment" + data[1]).addClass("alert-success");
                setTimeout(function () {
                    $("#comment" + data[1]).removeClass("alert-success", 1000);
                }, 2000);
                $("#textComment").val('');
                if (data[2]) {
                    $("#status" + idApp).fadeIn(1000);
                }
            }
        });
    }
})