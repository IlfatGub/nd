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
        line-height: 1.5em;
        margin: 5px;
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
    <?php Pjax::begin([
        'id' =>'podr',
//        'size' => '15px',
        'enablePushState' => false
    ]); ?>
    <div>
        <?php $form = ActiveForm::begin(['action' => ['/adm/podr'],'options' => ['data-pjax' => true]]); ?>

        <div class="row col-12">
            <div class="col-12">
                <div class="row">
                    <div class="col-10">
                        <?= $form->field($model, 'name')->textInput()->label(false) ?>
                    </div>
                    <div class="col-2">
                        <?= Html::submitButton('Добавить', ['class' => 'btn btn-success col-sm-12']) ?>
                    </div>
                </div>
                <div class="row">
                    <ul id="sortable" class="col-12 table" style="font-size: 10pt; vertical-align: middle; ">
                        <?php  foreach ($list as $item){ ?>
                            <li id="<?=$item->id?>">
                                <div class="col-1" ><div style="vertical-align: middle"><?= $item->id ?></div></div>
                                <div class="col-10"><?= $item->name ?></div>
                                <div class="col-1" ><?= Html::a('<span class="glyphicon glyphicon-remove "></span>',['/adm/podr', 'delete' => $item->id, ],
                                        ['data' => ['confirm' => 'Удалить?', 'method' => 'post'],  'style' => 'float: right']);
                                    ?></div>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
                <?php ActiveForm::end(); ?>
                <?php Pjax::end(); ?>
            </div>
        </div>