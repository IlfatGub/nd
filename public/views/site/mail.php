<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>jQuery UI Sortable - Drop placeholder</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <style>
        #sortable { list-style-type: none; margin: 0; padding: 0;}
        #sortable li { margin: 0 5px 5px 5px; padding: 5px; font-size: 1.2em; height: 1.5em; }
        html>body #sortable li { height: 1.5em; line-height: 1.2em; }
        .ui-state-highlight { height: 1.5em; line-height: 1.2em; }
    </style>
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>

    </script>
</head>
<body>
<ul id="sortable">
    <li id="item-1">Item 1</li>
    <li id="item-2">Item 2</li>
</ul>

Query string: <span id="spans"></span>
</body>
</html>


<br>




<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use kartik\widgets\DatePicker;
use yii\widgets\Pjax;
use timurmelnikov\widgets\LoadingOverlayPjax;
?>
<style>
    #sortable > li > div{
        display: inline-block;
        height: 100%;
        vertical-align: middle;
    }
    #sortable > li {
        background: #F5F5F5;
        padding: 0;
        line-height: 1.5em
    }
</style>

<script>
    $( function() {
        $( "#sortable" ).sortable({
            placeholder: "ui-state-highlight"
        });
        $( "#sortable" ).disableSelection();


        $('ul').sortable({
            axis: 'y',
            stop: function(event,ui) {
                var data = $(this).sortable("toArray");
                $('#spans').text(data);
                $.ajax({
                    type: "GET",
                    url: "/adm/podrsort",
                    data: "a=" + data,
                    timeout:5000,
                    cache: false,
                    dataType: "html",
                    success: function(data) {
                        var elem = $("<div id='qwe' class='alert alert-info fade in myRecal' style='display: none'></div>").text("выполнено");
                        $("#sortable").prepend(elem);
                        $("#qwe").fadeIn(1000);
                    },
                    error: function(){
                        $("#sortable").append('p>Error!</p>');
                    }
                });
            }
        });

    } );
</script>

<div class="org-form">
    <?php LoadingOverlayPjax::begin([
        'color'=> 'rgba(217, 237, 247, 0.2)',
        'fontawesome' => 'fa fa-spinner fa-spin',
        'id' =>'podr',
//        'size' => '15px',
        'enablePushState' => false
    ]); ?>
    <div>
        <?php $form = ActiveForm::begin(['action' => ['/adm/podr'],'options' => ['data-pjax' => true]]); ?>

        <div class="row" style=" font-size: 10pt">
            <div class="col-md-8 col-md-offset-2" >
                <div class="row">
                    <div class="col-md-10">
                        <?= $form->field($model, 'name')->textInput()->label(false) ?>
                    </div>
                    <div class="col-md-2">
                        <?= Html::submitButton('Добавить', ['class' => 'btn btn-success col-sm-12']) ?>
                    </div>
                </div>
                <div class="row">
                    <ul id="sortable" class="col-lg-6 table" style="font-size: 8pt; vertical-align: middle;">
                        <li class="ui-state-disabled" >
                            <div class="col-sm-1">№</div>
                            <div class="col-sm-10">Наименование</div>
                            <div class="col-sm-1"></div>
                        </li>
                        <?php  foreach ($list as $item){ ?>
                            <li id="<?=$item->id?>">
                                <div class="col-sm-1" ><div style="vertical-align: middle"><?= $item->id ?></div></div>
                                <div class="col-sm-10"><?= $item->name ?></div>
                                <div class="col-sm-1" ><?= Html::a('<span class="glyphicon glyphicon-remove "></span>',['/adm/podr', 'delete' => $item->id, ],
                                        ['data' => ['confirm' => 'Удалить?', 'method' => 'post'],  'style' => 'float: right']);
                                    ?></div>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
                <?php ActiveForm::end(); ?>
                <?php LoadingOverlayPjax::end(); ?>
            </div>
        </div>
