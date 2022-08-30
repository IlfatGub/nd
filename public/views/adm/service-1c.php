<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use kartik\widgets\DatePicker;
use yii\widgets\Pjax;
/**
 * @var  $model  \app\modules\admin\models\Problem;
 */

?>


<div class="org-form">
    <?php Pjax::begin([
        'id' =>'problem',
//        'size' => '15px',
        'enablePushState' => false
    ]); ?>
    <?php $form = ActiveForm::begin(['action' => ['/adm/problem'],'options' => ['data-pjax' => true]]); ?>

    <div class="row" style=" font-size: 10pt">
        <div class="col-12 col-md-offset-2" >
            <div class="row">
                <div class="col-10">
                    <?= $form->field($model, 'name')->textInput()->label(false) ?>
                    <?php
                    echo $form->field($model, 'parent_id')->widget(Select2::classname(), [
                        'data' => $model->getListItil(),
                        'options' => ['placeholder' => 'Select a state ...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>
                </div>
                <div class="col-2">
                    <?= Html::submitButton('Добавить', ['class' => 'btn btn-success', 'id' => 'problem-btn']) ?>
                </div>
            </div>

            <table class="table table-border table-sm">
                <tr class="bg-info text-white">
                    <td>№</td>
                    <td colspan="3">Наименование</td>
                    <td></td>
                </tr>
                <?php  foreach ($list as $item){ ?>
                    <tr>
                        <td><?= $item->id ?></td>
                        <td><?= $item->name ?></td>
                        <td>
                            <?= Html::input('runtime', 'string', $item->runtime,
                                ['class' => 'form-control form-control-sm input-noborder',
                                    'onchange' => '$.post(" '.Url::toRoute(['adm/problem']).'?id='.$item->id.'&type=runtime&text='.'"+$(this).val());'
                                ])
                            ?>
                        </td>
                        <td>
                            <?= Html::dropDownList('role', [$item->db] ,[null => 'Пользователи','1' => 'СИТ', '2' => 'СИРИАС', '3' => 'SAP', '4' => 'Связисты'],
                                ['class' => 'form-control  form-control-sm input-noborder',
                                    'onClick' => '$.post(" '.Url::toRoute(['adm/problem']).'?id='.$item->id.'&type=role&text='.'"+$(this).val());'
                                ]) ?>
                        </td>
                        <td>
                            <?= Html::dropDownList('role', [$item->podr] ,[null => 'Пользователи','1' => 'СИТ', '2' => 'СИРИАС', '3' => 'SAP', '4' => 'Связисты'],
                                ['class' => 'form-control  form-control-sm input-noborder',
                                    'onClick' => '$.post(" '.Url::toRoute(['adm/problem']).'?id='.$item->id.'&type=role&text='.'"+$(this).val());'
                                ]) ?>
                        </td>
                        <td>
                            <?= Html::dropDownList('role', [$item->user_id] ,[null => 'Пользователи','1' => 'СИТ', '2' => 'СИРИАС', '3' => 'SAP', '4' => 'Связисты'],
                                ['class' => 'form-control  form-control-sm input-noborder',
                                    'onClick' => '$.post(" '.Url::toRoute(['adm/problem']).'?id='.$item->id.'&type=role&text='.'"+$(this).val());'
                                ]) ?>
                        </td>
                        <td>
                            <?= Html::dropDownList('role', [$item->role] ,[null => 'Пользователи','1' => 'СИТ', '2' => 'СИРИАС', '3' => 'SAP', '4' => 'Связисты'],
                                ['class' => 'form-control  form-control-sm input-noborder',
                                    'onClick' => '$.post(" '.Url::toRoute(['adm/problem']).'?id='.$item->id.'&type=role&text='.'"+$(this).val());'
                                ]) ?>
                        </td>

                        <td><?= Html::a('<span class="fas fa-window-close text-danger float-right"></span>',['/adm/problem', 'delete' => $item->id, ],
                                ['data' => ['confirm' => 'Удалить?', 'method' => 'post', ]]);
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
            <?php ActiveForm::end(); ?>
            <?php Pjax::end(); ?>
        </div>
    </div>


</div>


<?php

$js = <<<JS
    $('#problem-btn').unbind('click')
JS;
$this->registerJs($js)
?>
