<?php
use app\modules\admin\models\Help;
use app\modules\admin\models\Problem;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use kartik\widgets\DatePicker;
use yii\widgets\Pjax;
use yii\helpers\Url;
$i=0;
?>


<div class="org-form">


    <div class="row">
        <div class="col-12" >
            <?php Pjax::begin([ 'id' =>'app-help', 'enablePushState' => false]); ?>
            <?php $form = ActiveForm::begin(['action' => ['/adm/help'],'options' => ['data-pjax' => true]]); ?>
            <div class="row">
                <div class="col-2">
                    <?= $form->field($model, 'parent_id')->dropDownList(Help::getHelp(), ['tabindex' => '4', 'prompt' => '- Выбрать родителя -'])->label(false) ?>
                </div>
                <div class="col-2">
                    <?= $form->field($model, 'problem')->dropDownList(Problem::getList(), ['tabindex' => '4', 'prompt' => '- Выбрать родителя -'])->label(false) ?>
                </div>
                <div class="col-6">
                    <?= $form->field($model, 'name')->textInput()->label(false) ?>
                </div>
                <div class="col-2">
                    <?= Html::submitButton('Добавить', ['class' => 'btn btn-success']) ?>
                </div>
            </div>



            <table class="table table-sm">
                <tr>
                    <td>Название</td>
                    <td>Родитель</td>
                    <td>Тип проблемы</td>
                </tr>
                <?php  foreach($list as $item) : ?>
                    <tr>
                        <td><?= $item->name ?></td>
                        <td><?= $model->getNameById($item->parent_id);  ?></td>
                        <td><?= Problem::getNameById($item->problem); ?></td>
                        <td><?= Html::a('<span class="fas fa-window-close text-danger float-right"></span>',['/adm/help', 'delete' => $item->id, ],
                                ['data' => ['confirm' => 'Удалить?']]);
                            ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </table>
            <?php ActiveForm::end(); ?>

            <?php Pjax::end(); ?>
        </div>
    </div>


</div>
