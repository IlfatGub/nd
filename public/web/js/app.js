$(function () {
    /**
     * Очичтка объекта
     * @param object_clk   объект при нажатии котором происходит событие
     * @param object_clear  объект который необходимо почистить
     */
    function clearObject(object_clk, object_clear) {
        $(object_clk).click(function () {
            appIllumination(object_clear, '', 'app-ip-error', 'app-ip-success');
            $(object_clear).empty();
        });
    }


    /**
     * Правый сайдбар
     */
    $('.icon-sidebar').click(function (event) {
        $('#sidebar-right').toggleClass('sidebar-right-active');
    });


    $('#app-id_user').change(function () {
        $('#app-back-div').show();
    });
    $('#app-back').change(function () {
        $('#app-back-info').show();
    });


    $('#app-search').click(function (event) {
        $(this).toggleClass('col-4 col-8');
    });
    $('#app-search').focusout(function (event) {
        $(this).toggleClass('col-4 col-8');
    });
    /**
     * Help-disp
     * Добавляем текст по клику на кнопки. Для диспетчера во время заведении заявки
     */
    $('.app-help-disp').click(function () {
        var text = $(this).text();
        var content = $('.app-content').val();      //записываем текст из поля "Описание"
        var id = this.id;
        $('.app-content').val(content + ' ' + text);   //Добавляем текст в конец поле "Описание"
        appIllumination('#app-id_problem', id, 'app-ip-error', 'app-ip-success'); // Изменяем выбранный элемент в поле "Тип Проблемы"
    });

    /**
     * При доавлении ФИО в поле "ФИО", заполняем поле "IP"
     */
    $('#app-fio').blur(function () {
        var text = $(this).val();

        var error = 'app-ip-error';
        var success = 'app-ip-success';

        if (text) {

            jQuery.ajax({
                type: "GET",
                url: "adm/fio",
                data: "fio=" + encodeURIComponent(text),
                dataType: 'json',
                success: function (data) {
                    // appIllumination('#app-ip', data[0], error, success); //Записываем полученные данные в поле "IP" и подсвечиваем его

                    if (data[0]) {
                        appIllumination('#app-ip', data[0], error, success); //Записываем полученные данные в поле "Подразделение" и подсвечиваем его
                    } else {
                        appIllumination('#app-ip', null, success, error); // Очищаем поле
                    }

                    if (data[1]) {
                        appIllumination('#app-id_podr', data[1], error, success); //Записываем полученные данные в поле "Подразделение" и подсвечиваем его
                    } else {
                        appIllumination('#app-id_podr', null, success, error); // Очищаем поле
                    }

                    if (data[2]) {
                        appIllumination('#app-phone', data[2], error, success); //Записываем полученные данные в поле "Тел" и подсвечиваем его
                    } else {
                        appIllumination('#app-phone', null, success, error); // Очищаем поле
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    appIllumination('#app-ip', null, success, error); // Поле АйПИ
                    appIllumination('#app-id_podr', null, success, error);   // Поле Подразделение
                    console.log(xhr.status); //Записываем ошибки в консоль
                    console.log(thrownError);
                }
            });
        }

    });


    function highlightText(v) {
        v.stop(true, true).addClass("alert-success", 300);
        setTimeout(function () {
            v.stop(true, true).removeClass("alert-success", 300);
        }, 1000);
    }


    function highlightTextError(v) {
        v.stop(true, true).addClass("alert-danger", 300);
        setTimeout(function () {
            v.stop(true, true).removeClass("alert-danger", 300);
        }, 1000);
    }


    /**
     * Загрузка списка "Тип проблем"
     * При выборе БД 1с
     */
    $(document).on('change', '#app-buh, #app-id_podr', function () {
        // var buh = $("#app-buh").val();
        // var podr = $("#app-id_podr").val();
        // var select = $("select#app-id_class");
        //
        // buhClick(buh, podr, select);
        //
        // $('#app-id_object').html("<option value=''></option>");
        // $('#app-id_problem').html("<option value=''></option>");
    });


    /**
     * Загрузка списка "Тип проблем"
     * При выборе БД 1с
     */
    $(document).on('click', '#app-documentfiles', function () {
        // alert('asdas');
        $(".file-preview").css({"display": "block"});
    });

    $('#app-documentfiles').hover(function () {
        $(".file-preview").css({"display": "block"});
    });



    $(document).on('click', '#api-uri-add', function () {
        var api_uri = $("#api-uri").val();
        if (api_uri) {
            jQuery.ajax({
                type: "GET",
                url: "temp/add-uri",
                data: "uri=" + encodeURIComponent(api_uri),
                success: function (data) {

                    $('#api-uri-content').empty().html(data);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#api-uri-error').text("Error");
                }
            });
        }
    });


    $(document).on('click', '#project-comment-add', function () {
        var text = $("#project-comment-text").val();
        var id_app = $(this).attr('data-id');

        if (text) {
            jQuery.ajax({
                type: "GET",
                url: "admin/comment/ajax",
                data: "text=" + encodeURIComponent(text) + "&id=" + id_app + "&type=1",
                success: function (data) {
                    var result = jQuery.parseJSON(data);
                    console.log(result);
                    if (result.result === true) {
                        $("#project-comment-text").empty();
                        $('#project-comment-content').empty().html(result.data);
                    }
                    animation(result, $('#show-message'));
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#project-comment-content').text("Error");
                }
            });
        }
    });


    $(document).on('click', '.status-ticket', function () {
        var status = $(this).attr('data-id');
            jQuery.ajax({
                type: "GET",
                url: "/site/get-status-ticket",
                data: "status=" + status,
                success: function (data) {
                    $(".status-ticket").remove();
                    $('#status-ticket-' + status).empty().html(data);
                },
            });
    });




    function animation(result, selector) {
        console.log(selector);

        if (result.result === true)
            selector.addClass('alert-success').html(result.message).show();
        else
            selector.addClass('alert-danger').html(result.message).show();

        setTimeout(function () {
            selector.removeClass('alert-danger', 'alert-success').hide();
        }, 5000);
    }


    // Вывод занятости сотрудников. Статистика ------------------------------------------------

    // меняем название АйДи  с app-id_user_new  на app-id_user
    $(document).mouseup(function (e) { // событие клика по веб-документу
        var div = $("#app-id_user_new"); // тут указываем ID элемента
        if (!div.is(e.target) // если клик был не по нашему блоку
            && div.has(e.target).length === 0) { // и не по его дочерним элементам
            div.attr('id', 'app-id_user');
        }
    });

    // по клику выводим нужный нам список, где указазны польватели с нужными пометками
    $(document).on('click', '#app-id_user', function () {
        //
        // employment(); //подгружаем таблицу
        //
        // $.post('/site/employment-user', function (data) {
        //
        //     var result = jQuery.parseJSON(data);
        //     if (result.data == true) {
        //         var selected = $('#app-id_user').val(); //записываем активного пользователя с выпдаюащего спсика
        //
        //         $('#app-id_user').html(result.select).attr('id', 'app-id_user_new');
        //
        //         $('#app-id_user_new option[value=' + selected + ']').prop('selected', true); // выбираем нужного пользователя уже с нового списка
        //     } else {
        //
        //     }
        // });
    });

    // по клику выводим нужный нам список, где указазны польватели с нужными пометками
    $(document).on('click', '#service-id_user', function () {

        $.post('/site/employment-user', function (data) {
            var result = jQuery.parseJSON(data);
            if (result.data == true) {
                $('#service-id_user').html(result.select).attr('id', 'service-id_user_new');
            } else {

            }
        });
    });

    //аяак подгрузка пользователей
    function employment() {
        jQuery.ajax({
            type: "GET",
            url: "employment",
            success: function (data) {
                $("#sitdesk-user-employment").html(data);
            },
        });
    }

    /**
     * подгружаем по интервалу
     */
    setInterval(function () {
        employment();
    }, 30000);

    // Вывод занятости сотрудников. Статистика --------------------------------------------------------------------------


    $(document).on('click', '', function () {
        var id = $(this).attr('id');
        if (id) {
            jQuery.ajax({
                type: "GET",
                url: "temp/delete",
                data: "id=" + encodeURIComponent(id),
                success: function (data) {
                    $("#temp-line-" + id).hide();
                    $('#api-uri-error').text("Элемент удален");
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#api-uri-error').text("Error");
                }
            });
        }
    });


    /**
     * Загрузка списка "Тип проблем"
     * При выборе БД 1с
     */
    $(document).on('change', '#service-buh, #service-id_podr', function () {
        // var buh = $("#service-buh").val();
        // var podr = $("#service-id_podr").val();
        // var select = $("select#service-id_class");
        //
        // buhClick(buh, podr, select);

    });


    function buhClick(buh, podr, select) {
        if (buh) {
            $.post('/site/class?buh=' + buh + '&podr=' + podr, function (data) {
                var result = jQuery.parseJSON(data);
                if (result.data == true) {
                    select.html(result.select);
                    setInterval(highlightText(select), 1000);
                } else {
                    select.html("<option value=''></option>");
                    setInterval(highlightTextError(select), 1000);
                }
            });

            $('#service-id_object').html("<option value=''></option>");
            $('#service-id_problem').html("<option value=''></option>");
        }
    }

    function problemClick(val, buh, podr, select) {
        if (buh) {
            $.post('/site/problem?id=' + val + '&buh=' + buh + '&podr=' + podr, function (data) {
                var result = jQuery.parseJSON(data);
                if (result.data == true) {
                    select.html(result.select);
                    setInterval(highlightText(select), 1000);
                } else {
                    setInterval(highlightTextError(select), 1000);
                }
            });
        } else {
            $.post('/site/problem?id=' + val, function (data) {
                var result = jQuery.parseJSON(data);
                if (result.data == true) {
                    select.html(result.select);
                    setInterval(highlightText(select), 1000);
                } else {
                    setInterval(highlightTextError(select), 1000);
                }
            });
        }
    }


    /**
     * Загрузка списка Отделов по организации
     */
    $(document).on('change', '#report-org', function () {
        var val = $(this).val();
        var select = $('#report-depart');

        $.post('/site/report-list?org=' + val, function (data) {
            var result = jQuery.parseJSON(data);
            console.log(result.data);
            if (result.data == true) {
                select.html(result.select);
                setInterval(highlightText(select), 1000);
            } else {
                select.empty();
                setInterval(highlightTextError(select), 1000);
            }
        });
    });


    /**
     * Загрузка списка "Тип проблем"
     *  + выводим список статей из Базы Знаний по выбранному Типу проблем
     */
    $(document).on('change', '#app-id_object', function () {
        // var buh = $("#app-buh").val();
        // var podr = $("#app-id_podr").val();
        var buh = '';
        var podr = '';
        var select = $("select#app-id_problem");
        var val = $(this).val();

        problemClick(val, buh, podr, select);
    });


    /**
     * Загрузка пользователей по типу проблем
     */
    $(document).on('change', '#app-id_problem', function () {
        // var buh = $("#app-buh").val();
        var buh = '';
        var podr = '';
        var select = $("select#app-id_user");

        if (buh) {
            $.post('/site/pr-user?id=' + $(this).val(), function (data) {
                var result = jQuery.parseJSON(data);
                if (result.data == true) {
                    select.html(result.select);
                    setInterval(highlightText(select), 1000);
                } else {
                    setInterval(highlightTextError(select), 1000);
                }
            });
        }
    });

    /**
     * Загрузка пользователей по типу проблем
     */
    $(document).on('change', '#service-id_problem', function () {
        // var buh = $("#service-buh").val();
        var buh = '';
        var podr = '';
        var select = $("select#service-id_user");

        if (buh) {
            $.post('/site/pr-user?id=' + $(this).val(), function (data) {
                var result = jQuery.parseJSON(data);
                if (result.data == true) {
                    select.html(result.select);
                    setInterval(highlightText(select), 1000);
                } else {
                    setInterval(highlightTextError(select), 1000);
                }
            });
        }
    });


    /**
     * Отстствую на месте
     */
    $(document).on('click', '#hd-absent', function () {
        var absent = $("#hd-absent");
        var nav = $("#hd-nav");

        $.post('/site/absent', function (data) {
            var result = jQuery.parseJSON(data);
            console.log(result);
            if (result.data == 1) {
                absent.removeClass('btn-outline-danger').addClass('btn-outline-success').text('На месте');
                nav.removeClass('alert-info').addClass('alert-danger');
            } else {
                absent.removeClass('btn-outline-success').addClass('btn-outline-danger').text('Отсутствую');
                nav.removeClass('alert-danger').addClass('alert-info');
            }
        });
    });


    /**
     * Отмечаем заявку как лишняя
     */
    $(document).on('change', '.hd-stupid', function () {
        var id = $(this).attr('id');
        var select = $("#hd-stupid-main");
        $.post('/site/stupid?id=' + id, function (data) {
            var result = jQuery.parseJSON(data);
            console.log(result);
            if (result.data == true) {
                setInterval(highlightText(select), 1000);
            } else {
                setInterval(highlightTextError(select), 1000);
            }
        });
    });


    /**
     * Отмечаем заявку как лишняя
     */
    $(document).on('change', '.hd-no-exec', function () {
        var id = $(this).attr('id');
        var select = $("#hd-no-exec-main");
        $.post('/site/exec?id=' + id, function (data) {
            var result = jQuery.parseJSON(data);
            console.log(result);
            if (result.data == true) {
                setInterval(highlightText(select), 1000);
            } else {
                setInterval(highlightTextError(select), 1000);
            }
        });
    });


    /**
     * Изменяем переключатель
     * Видимость коментария для пользователя
     */
    $(document).on('click', '.hd-side', function () {
        var id = $(this).attr('id');
        $.post('/site/side?id=' + id, function (data) {
            $(".side-panel").html(data);
        });
    });


    /**
     * Изменяем переключатель
     * Видимость коментария для пользователя
     */
    $(document).on('change', '.hd-checkbox', function () {
        var id = $(this).attr('id');
        var vis = null;
        // alert(vis + ' - ' + id);

        if ($(this).is(':checked')) {
            vis = 1;
        }

        $.post('/site/comvis?id=' + id + '&vis=' + vis);
    });


    /**
     * Загрузка списка "Предмета проблем"
     */
    $(document).on('change', '#app-id_class', function () {
        // var buh = $("#app-buh").val();
        // var podr = $("#app-id_podr").val();
        var buh = '';
        var podr = '';
        var select = $("select#app-id_object");
        var val = $(this).val();

        problemClick(val, buh, podr, select);

        $('#app-id_problem').html("<option value=''></option>");
    });


    /**
     * Загрузка списка "Предмета проблем"
     */
    $(document).on('blur', '#app-fio', function () {
        var podr = '';
        var val = $(this).val();

        if (val) {
            jQuery.ajax({
                type: "GET",
                url: "adm/api-depart",
                data: "fio=" + encodeURIComponent(text),
                dataType: 'json',
                success: function (data) {
                    console.log("asdasda");
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    appIllumination('#app-ip', '10.224.', 'app-ip-success', 'app-ip-error'); // Поле АйПИ
                    appIllumination('#app-id_podr', '', 'app-ip-success', 'app-ip-error');   // Поле Подразделение
                    console.log(xhr.status); //Записываем ошибки в консоль
                    console.log(thrownError);
                }
            });
        }
    });


    /**
     * Загрузка списка "Предмета проблем"
     */
    $(document).on('change', '#service-id_class', function () {
        // var buh = $("#service-buh").val();
        // var podr = $("#service-id_podr").val();
        var buh = '';
        var podr = '';
        var select = $("select#service-id_object");
        var val = $(this).val();

        problemClick(val, buh, podr, select);

        $('#service-id_problem').html("<option value=''></option>");

    });

    /**
     * Загрузка списка "Тип проблем"
     *  + выводим список статей из Базы Знаний по выбранному Типу проблем
     */
    $(document).on('change', '#service-id_object', function () {

        // var buh = $("#service-buh").val();
        // var podr = $("#service-id_podr").val();
        var buh = '';
        var podr = '';
        var select = $("select#service-id_problem");
        var val = $(this).val();

        problemClick(val, buh, podr, select);

    });


    /**
     */
    $(document).on('click', '.hd-input', function () {
        $(this).removeClass('hd-input');
    });

    // /**
    //  * Загрузка списка "Предмета проблем"
    //  */
    // $('#app-id_class').change(function () {
    //     $.post('/site/problem?id=' + $(this).val(), function(data) {
    //         var result = jQuery.parseJSON(data);
    //         $( "select#app-id_object").html(result.select);
    //     });
    //
    //     $('#app-id_problem').html("<option value=''></option>");
    // });
    //
    // /**
    //  * Загрузка списка "Тип проблем"
    //  *  + выводим список статей из Базы Знаний по выбранному Типу проблем
    //  */
    // $('#app-id_object').change(function () {
    //     $.post('/site/problem?id=' + $(this).val(), function(data) {
    //         var result = jQuery.parseJSON(data);
    //         $( "select#app-id_problem").html(result.select);
    //     });
    // });


    /**
     * Очистка поле "Описание"
     * @param объект при нажатии котором происходит событие
     * @param объект который необходимо почистить
     */
    clearObject('#app-help-clear', '#app-content');

    /**
     * Меняем значения поля "Исполнитель", при выборе типа заявки "Служебка"
     */
    $('#app_dv_btn').click(function () {
        $('#app-id_user').val(4).trigger("change");  //Задаем значения для поля "Исполнитель", и устанавливаем обработчик изменения
    });


    /**
     * /site/about
     * Скрыть показать форму добавления
     */
    $('#app-about-hide').click(function () {
        $('.app-about-add').slideToggle();
    });

    /**
     * Меняем значения поля "Исполнитель", при выборе типа заявки "1с"
     */
    $('#buh').click(function () {
        // $('#app-id_user').val([4,34]).trigger("change");  //Задаем значения для поля "Исполнитель", и устанавливаем обработчик изменения
    });

    /**
     * Подсечиваем поле  на 2 сек
     * @param object        объект к оторорму будет применяться событие
     * @param text          значение который необходимо задать
     * @param style1        стиль который надо удалить
     * @param style2        стиль который надо добавить
     */
    function appIllumination(object, text, style1, style2) {
        if (object) {
            if (text) {
                $(object).val(text).removeClass(style1).addClass(style2);   //При ошибке поле object подсвечиваеться
            } else {
                $(object).removeClass(style1).addClass(style2);   //При ошибке поле object подсвечиваеться
            }
            setTimeout(function () {
                $(object).removeClass(style2);       //Через 2сек убраем подсветку
            }, 2000);
        }
    }


    function errorAnimation(object) {
        setTimeout(function () {
            $(object).removeClass('app-ip-error');       //Через 2сек убраем подсветку
        }, 2000);
    }

});


