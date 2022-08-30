<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
?>
<?php Pjax::begin([
    'id' =>'buh',
    'enablePushState' => false
]); ?>

<div class="container">
    <div class="row justify-content-md-center">
        <div class="buh-form col-12 bg-sitdesk-block mt-2 pt-3" style="border: 1px dotted silver">
            <?php $form = ActiveForm::begin(['action' => ['/adm/buh'],'options' => ['data-pjax' => 1], 'id' => 'app-buh']); ?>
            <div class="row" style=" font-size: 10pt">
                <div class="col-12" >
                    <div class="row">
                        <div class="col-10">
                            <?= $form->field($model, 'name')->textInput()->label(false) ?>
                        </div>
                        <div class="col-2">
                            <?= Html::submitButton('Добавить', ['class' => 'btn btn-success']) ?>
                        </div>
                    </div>

                    <table class="table table-border table-sm">
                        <tr class="bg-info text-white">
                            <td width="50px">№</td>
                            <td>Наименование</td>
                            <td></td>
                        </tr>
                        <?php  foreach ($list as $item){ ?>
                            <tr>
                                <td><?= $item->id ?></td>
                                <td>
                                    <?= Html::input('name', 'string', $item->name,
                                        ['class' => 'form-control form-control-sm inp-change ',
                                            'onchange' => '$.post(" '.Url::toRoute(['buh']).'?id='.$item->id.'&text='.'"+$(this).val());'
                                        ])
                                    ?>
                                </td>

                                <td><?= Html::a('<span class="fas fa-window-close text-danger float-right"></span>',['/adm/buh', 'delete' => $item->id, ],
                                        ['data' => ['confirm' => 'Удалить?', 'method' => 'post', ]]);
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>


<?php Pjax::end(); ?>



