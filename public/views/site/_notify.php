<?php
/**
 * Created by PhpStorm.
 * User: 01gig
 * Date: 25.10.2019
 * Time: 9:16
 */

use kartik\datetime\DateTimePicker;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\modules\admin\models\Login;

?>

<style>

</style>

<?php $form = ActiveForm::begin()?>
<?php $model->user_id = Yii::$app->user->id ?>
<?php $class = "form-control" ?>
<div>

    <div class="col-6 ml-4">
        <?php
        echo $form->field($model, 'datetime')->widget(DateTimePicker::classname(), [
            'options' => ['placeholder' => 'До какого времени оповещать', 'autocomplete'=>'off' ],
            'pluginOptions' => [
                'autoclose' => true,
            ]
        ])->label(false);
        ?>
        <?php
        echo $form->field($model, 'user_id')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(Login::getLoginList(), 'id', 'username'),
            'options' => ['class' => $class, 'placeholder' => '- Выбрать исполнителя -', 'multiple' => true],
            'size' => 'sm',
            'pluginOptions' => [
                'multiple' => true,
                'maximumInputLength' => 20,
                'font-size' => '10pt'
            ],
        ])->label(false);
        ?>

        <?= $form->field($model, 'text')->textarea( ['class' => $class, 'placeholder' => 'Текст оповещения'])->label(false) ?>

        <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary col-md-12 btn-sm my-2']) ?>
    </div>


<?php $form = ActiveForm::end()?>

