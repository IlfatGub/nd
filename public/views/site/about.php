<?php

/* @var $this yii\web\View */
/* @var $model app\models\About */

use kartik\widgets\DatePicker;
use kartik\file\FileInput;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\modules\admin\models\MyDate;
?>

<?php
//exec('start "C:\\Program Files (x86)\\SolarWinds\\DameWare Remote Support\DWRCC.exe"');
//?>


<div class="col-lg-12 p-3">




    <?php if (Yii::$app->user->can('SuperAdmin')) : ?>
        <div class="mb-3 ">
            <div>
                <div id="app-about-hide" class="btn btn-dark btn-sm col-md-2 mb-2">Добавить/Скрыть</div>
            </div>
            <div class="app-about-add alert alert-primary" >
                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'class' => 'form-control-sm']]); ?>
                <?= $form->field($model, 'name')->textInput(['autofocus' => true])->label(false); ?>
                <?= $form->field($model, 'description')->textarea(['option' => 'form-control-sm', 'row' => 1])->label(false); ?>
                <?= $form->field($model, 'role')->dropDownList(['100' => 'User', '105' => 'Disp', '110' => 'Admin', '120' => 'SuperAdmin'])->label(false); ?>
                <?php
                $model->date_ct = date('Y-m-d');
                echo $form->field($model, 'date_ct')->widget(DatePicker::classname(), [
                    'type' => DatePicker::TYPE_INPUT,
                    'language' => 'ru',
                    'pluginOptions' => [
                        'todayHighlight' => true,
                        'autoclose'=>true,
                        'format' => 'yyyy-mm-dd'
                    ]
                ])->label(false);
                ?>
                <?= $form->field($model, 'image[]')->fileInput(['multiple' => true, 'accept' => 'image/*'])->label(false); ?>
                <button type="submit" class="btn btn-success btn-sm col-md-2">Добавить</button>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    <?php endif; ?>

    <?= Yii::$app->user->can('DISP') ?>
    <?php
    echo FancyBox::widget([
        'target' => 'a[rel=fancybox]',
        'helpers' => true,
        'mouse' => true,
        'config' => [
            'maxWidth' => '100%',
            'maxHeight' => '100%',
            'playSpeed' => 7000,
            'padding' => 0,
            'fitToView' => false,
            'width' => '70%',
            'height' => '70%',
            'autoSize' => true,
            'closeClick' => false,
            'openEffect' => 'elastic',
            'closeEffect' => 'elastic',
            'prevEffect' => 'elastic',
            'nextEffect' => 'elastic',
            'closeBtn' => false,
            'openOpacity' => false,
//        'helpers' => [
//            'title' => ['type' => 'float'],
//            'buttons' => [],
//            'thumbs' => ['width' => 68, 'height' => 50],
//            'overlay' => [
//                'css' => [
//                    'background' => 'rgba(0, 0, 0, 0.8)'
//                ]
//            ]
//        ],
        ]
    ]);
    ?>

    <table class="table table-bordered col-md-12">
        <?php  foreach($list as $item) : ?>
            <tr class="col-md-12">
                <td style="width: 250px;"><?= $item->name ?></td>
                <?php $data = $item->date_ct ? "<span class='badge badge-success'>" . MyDate::getDate($item->date_ct , '100'). "</span>"  : '' ?>
                <td style="width: 450px;"><?= $data ?>   <?= $item->description ?></td>
                <td>
                    <?php if(isset($item->image)) : ?>
                        <?php  foreach( (array)  Yii::$app->storage->getFile($item->image) as $item) : ?>
                            <?php
                            echo Html::a(Html::img(Yii::$app->homeUrl.Yii::$app->params['storagePath'].$item, ['class' => 'lightzoom rounded img-thumbnail float-left app-about-image m-1']), Yii::$app->homeUrl.Yii::$app->params['storagePath'].$item , ['rel' => 'fancybox']);
                            ?>
                        <?php endforeach ?>
                    <?php endif ?>
                </td>
            </tr>
        <?php endforeach ?>
    </table>

</div>







