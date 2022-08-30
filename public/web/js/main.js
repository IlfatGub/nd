$(function(){

    $('#modalButton').click(function(){
        $('#modal').modal('show')
            .find('#modalContent')
            .load($(this).attr('value'));
    });
    $('.modalButton').click(function(){
        $('#modal').modal('show')
            .find('#modalContent')
            .load($(this).attr('value'));
        $('.modal-dialog').addClass('modal-lg');
    });



    $('.modalButton-lg').click(function(){
        $('#modal').modal('show')
            .find('#modalContent')
            .load($(this).attr('value'));
        $('.modal-dialog').addClass('modal-lg');
    });

    $('.modalButton-sm').click(function(){
        $('#modal').modal('show')
            .find('#modalContent')
            .load($(this).attr('value'));
        $('.modal-dialog').addClass('modal-sm');
    });

    $('.modalButton-xl').click(function(){
        $('#modal').modal('show')
            .find('#modalContent')
            .load($(this).attr('value'));
        $('.modal-dialog').addClass('modal-xl');
    });

    $('#modalFio').click(function(){
        var text = encodeURIComponent($('#app-fio').val().trim());
        $('#modal').modal('show')
            .find('#modalContent')
            .load($(this).attr('value') + "?search=" + text + "&limit=25");
        $('.modal-dialog').addClass('modal-lg');
    });
    $('#modalIp').click(function(){
        $('#modal').modal('show')
            .find('#modalContent')
            .load($(this).attr('value') + "?search=" + $('#app-ip').val());
    });
    $('#modalPhone').click(function(){
        $('#modal').modal('show')
            .find('#modalContent')
            .load($(this).attr('value') + "?search=" + $('#app-phone').val());
    });


    $('#app-fio').on('change blur keyup',function () {
        $('.sitdesk-ad-uri').attr("href", '/adm/ldap?fio=' +  $('#app-fio').val() );
    });


    // var hash = window.location.hash;
    // if(hash){
    //     // alert(hash);
    //     $('.sidebar-nav').animate({scrollTop:$(hash).position().top - 200});
    //     return false;
    // }



    $('#login-form').find('input, textarea').keyup(function () {
        $('#sitdesk-form-btn-update').show(100);
    });

    $('#login-form').find('select').change(function () {
        $('#sitdesk-form-btn-update').show(100);
    });


    /*
     Изменяем размер поля ввода "Коментарий, Напоминания" по мере изменения количество строк в тексте
     */
    $('.app-input-comment, .app-input-recal').on('input',function () {
        this.style.height = '1px';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // Новый класс, для Clipboard. Копирование текста в буффер.
    new Clipboard('.btncopy, .sitdesk-btncopy');

    $(document).on('mouseout', '.sitdesk-btncopy, .btncopy', function () {
        $(this).css({
            'color':'',
        });
    });
    $(document).on('mouseover', '.sitdesk-btncopy, .btncopy', function () {
        $(this).css({
            'color':'blue',
        });
    });

    // $('.sitdesk-btncopy, .btncopy').mouseout(function () {
    //     $(this).css({
    //         'color':'',
    //     });
    // }).mouseover(function () {
    //     $(this).css({
    //         'color':'blue',
    //     });
    // });

    // new Clipboard('.sitdesk-btncopy');

    // $('.btncopy').mouseover(function () {
    //     $(this).removeClass('myCopy');
    //     $(this).addClass('myCopyOver');
    //
    // });
    // $('.btncopy').mouseout(function () {
    //     $(this).removeClass('myCopyOver');
    //     $(this).addClass('myCopy');
    // });



    // Изменяем текст при выборе поска в LDAP
    function ldapText()
    {
        var txt = $('#LdapDropdownlist').val();
        switch (txt){
            case '1':
                $('#ldapText').html("Поиск по ФИО. Фио разделять <code>';'</code>. Вводить ФИО полностью, ишет полное совпадение<br>Пример - <code>Иванов Иван Иванович</code> или <code>Иванов Иван Иванович; Петров Петр Петрович</code><br>");
                break;
            case '2':
                $('#ldapText').html("Поиск по Группам. Группы разделять <code>';'</code>. Вводить Группы можно не полностью <br>Пример - <code>Access_uit</code> или <code>Access_uit_read; Access_uit_write</code><br>");
                break;
        }
    }

    $('.myRecNone').mouseover(function () {
        $(this).children().fadeIn(300);
    });
    $('.myRecNone').mouseout(function () {
        $('.remove').fadeOut(50);
    });

    $('#add').click(function(){
        var val = $("#inpText").val();
        if(val !=='' ){
            addRecal(val);
        }
    });

    $(".remove").click(function(){
        if(this.id !=='' ){
            deleteRecal(this.id);
        }
    });


    $('input#search').on('input', function(){
        var str = $(this).val().toLowerCase();
        if (str.length <= 1){
            $('ul#search-items li').show();
            // $('p').text('Введите не менее 3 символов');
        }
        else {
            $('ul#search-items li').each(function(){
                if ($(this).text().toLowerCase().indexOf(str) < 0){
                    $(this).hide();
                }
            });
        }
    });



    /**
     * site/main
     * Замена англ букв на русские
     * Живая подгрузка данных при поиске
     */
    $('#app-search').keyup(function () {

        var text = $(this).val();
        var str = $(this).val();
        var r = '';
        for (var i = 0; i < str.length; i++) {
            r += map[str.charAt(i)] || str.charAt(i);
        }
        text = r;

        $(this).val(text);

        if (text.length > 3) {
            $('#app-search-content').empty();
            $.ajax({
                type: "GET",
                url: "/global",
                data: "search=" + text + "&limit=10",
                success: function (data) {
                    $('#app-search-content').html(data);
                    // $('#app-search-content').append(data).find('#foo').html();
                }
            });
        } else {
            $('#app-search-content').empty();
        }
    });


    var map = {
        'q' : 'й', 'w' : 'ц', 'e' : 'у', 'r' : 'к', 't' : 'е', 'y' : 'н', 'u' : 'г', 'i' : 'ш', 'o' : 'щ',
        'p' : 'з', '[' : 'х', ']' : 'ъ', 'a' : 'ф', 's' : 'ы', 'd' : 'в', 'f' : 'а', 'g' : 'п', 'h' : 'р', 'j' : 'о',
        'k' : 'л', 'l' : 'д', ';' : 'ж', '\'' : 'э', 'z' : 'я', 'x' : 'ч', 'c' : 'с', 'v' : 'м', 'b' : 'и', 'n' : 'т',
        'm' : 'ь', ',' : 'б', '.' : 'ю','Q' : 'Й', 'W' : 'Ц', 'E' : 'У', 'R' : 'К', 'T' : 'Е', 'Y' : 'Н',
        'U' : 'Г', 'I' : 'Ш', 'O' : 'Щ', 'P' : 'З', '{' : 'Х', '}' : 'Ъ', 'A' : 'Ф', 'S' : 'Ы', 'D' : 'В',
        'F' : 'А', 'G' : 'П', 'H' : 'Р', 'J' : 'О', 'K' : 'Л', 'L' : 'Д', ':' : 'Ж', '"' : 'Э', 'Z' : '?',
        'X' : 'ч', 'C' : 'С', 'V' : 'М', 'B' : 'И', 'N' : 'Т', 'M' : 'Ь',
    };

    // Добовляем напоминание
    function addRecal(val){
        $.ajax({
            type: "GET",
            url: "admin/recal/add",
            data: "text=" + val.replace(/([^>])\n/g, '$1<br/>'),
            timeout:5000,
            cache: false,
            dataType: "html",
            success: function(data) {
                var res = data.split(',');
                var elem = $("<div id=parent"+res[0]+" class='alert alert-info myRecal myRecNone py-1 px-1 my-1' style='display: none'></div>").html(val.replace(/([^>])\n/g, '$1<br/>'));
                var elem2 = $("<a href='#' class='remove' id="+res[0]+" >×</a></div>");
                var elem3 = $("<span class='badge badge-info' style='font-size: 9pt' >  "+res[1]+"    </span>");
                var elem4 = $("<small>  "+res[2]+"    </small>");
                $(elem).prepend(elem2);
                $(elem).prepend(elem3);
                $(elem3).prepend(elem4);
                $("#mylist").prepend(elem);
                $("#parent"+res[0]).fadeIn(1000);
                $("#inpText").val('');

                $(".remove").click(function(){
                    deleteRecal(res[0]);
                });
            },
            error: function(){
                $("#mylist").append('<p>Error!</p>');
            }
        });
    }

    // Удаляем напоминаниае
    function deleteRecal(id){
        $.ajax({
            type: "GET",
            url: "admin/recal/delete",
            data: "id=" + id,
            timeout:5000,
            cache: false,
            dataType: "html",
            success: function(data) {
                if(data){
                    $("#"+id).parent().addClass('alert-danger');
                    $("#"+id).parent().fadeOut(1000);
                }else{
                    var elem = $("<div class='alert alert-danger fade in myRecal text-center test' style='display: none'></div>").text("Ошибка");
                    $("#mylist").prepend(elem);
                    toggle(".test");
                }
            },
            error: function(){
                $("#mylist").append('<p>Error!</p>');
            }
        });
    }

    $('#inpText').keydown(function (e) {
        if (e.ctrlKey && e.keyCode == 13) {
            $("#add").trigger("click");
        }
    });

    function toggle(val){
        $(val).slideDown(1000);
        $(val).delay(1500);
        $(val).slideUp(1000);
    }

    // Копируем текст
    function copyToClipboard(element) {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(element).text()).select();
        document.execCommand("copy");
        $temp.remove();
    }

    $('#LdapDropdownlist').change(function(){
        ldapText();
    });

    // alert("asdasd");

    $("#cb1").change(function(){
        $('.cat1').fadeToggle(300);
    });

    $("#fioCaseLogin").change(function(){
        $('.fioCaseLogin').fadeToggle(100);
    });
    $("#fioCaseName").change(function(){
        $('.fioCaseName').fadeToggle(100);
    });

    $("[data-toggle='tooltip']").tooltip({html:true});
    $("[data-toggle='popover']").popover();


    $('#buh').click(function(){
        $('.buh option[value='+18+']').attr('selected', 'selected').siblings().removeAttr('selected');
    });

    // Выполнение функиця сразу после загрузки страницы
    $(window).load(function() {
        ldapText();
    });

});


window.onload = function() {
    var hash = window.location.hash;

    if(hash){
        if ($(hash).position().top > $(window).height()){
            $('#sidebar-wrapper').animate({scrollTop: $(hash).position().top - 250 }, 100);
        }else{
            $('#sidebar-wrapper').animate({scrollTop: 1 }, 1);
        }
        return false;
    }
};


function keyUp(event){
    if(event.keyCode == 13){
        event.preventDefault();
    }
}

function keyDown(e) {
    if (e.keyCode == 17)
        ctrl = true;
    else if (e.keyCode == 13 && ctrl){
        document.getElementById("btnComment").click();
    }else{
        ctrl = false;
    }
}
function keyDownRecal(e) {
    if (e.keyCode == 17)
        ctrl = true;
    else if (e.keyCode == 13 && ctrl){
        document.getElementById("btnRecal").click();
    }else{
        ctrl = false;
    }
}
function show(id)
{
    div = document.getElementById(id);
    if(div.style.display == 'block') {
        div.style.display = 'none';
    }
    else {
        div.style.display = 'block';
    }
}

/**
 * Меня расположение верхнего меню заявки при прокручиваниии вниз
 */
$(document).ready(function(){
    $(window).scroll(function(){
        var scrollValue = $(window).scrollTop();
        if (scrollValue > 50) {
            $('#myAffix').addClass('fixed');
            $('#login-form').addClass('mt-5');
        }else{
            $('#myAffix').removeClass('fixed');
            $('#login-form').removeClass('mt-5');
        }
    });
});

