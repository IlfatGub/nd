<?php
/**
 * Created by PhpStorm.
 * User: 01gig
 * Date: 20.03.2020
 * Time: 13:17
 */

use app\models\Sitdesk;
use app\modules\admin\models\Problem;
use kartik\helpers\Html;
use kartik\select2\Select2;
    use kartik\widgets\DateTimePicker;
    use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\admin\models\Login;

?>


<?php $form = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data'],
    'id' => 'login-form',
    'fieldConfig' => [
        'options' => ['class' => 'col-lg-121'],
        'template' => '<div >{input}</div> <span class="text-danger"> <small>{error}</small> </span>',
        'labelOptions' => ['class' => 'col-sm-2 control-label'],
    ],
]);

$classInput = "form-control form-control-sm mb-2 app-input select2-results__options";
$classLabel = "main-input mb-1";
$disabled = false;

$id_app =  $_GET['id'];
$analog = new \app\modules\admin\models\AppAnalog(['id_app' => $id_app]);


?>
<div class="jumbotron px-5 py-3">
    <h1 class="h4">Перевод заявка в ожидание <br> заявка № <?= $id_app ?></h1>
</div>

<div class="row justify-content-md-center">
    <div class="col-12">
        <label class="<?= $classLabel ?>"> Напомнить через </label>
        <?= $form->field($model, 'time')->dropDownList(['1' => '1 час' , '2' => '2 часа'], ['id' => 'service-id_class', 'tabindex' => '6', 'class' => $classInput . ' buh', 'disabled' => $disabled, 'prompt' => '- Выбрать  время -'])->label(false) ?>

        <label class="<?= $classLabel ?>"> Выбрать дату напоминания </label>

        <?php
            // Usage with model and Active Form (with no default initial value)
            echo $form->field($model, 'datetime')->widget(DateTimePicker::classname(), [
                'options' => ['placeholder' => 'Выбрать дату...'],
                'removeButton' => false,
                'pluginOptions' => [
                    'autoclose' => true,
                ]
            ])->hint('время будет округлено до часового промежутка');
        ?>

        <?php if($analog->getAnalog()): ?>
            <?php $analog_app = $analog->getAnalog(); ?>
            <?php $analog_app = array_combine($analog_app, $analog_app); unset($analog_app[$id_app]) ?>

            <label class="<?= $classLabel ?>"> Выбрать последовательную заявку : </label>
            <?= $form->field($model, 'serial_app')->dropDownList($analog_app, ['tabindex' => '7', 'class' => $classInput, 'disabled' => $disabled, 'prompt' => '- Выбрать заявку -'])->label(false) ?>
        <?php endif; ?>

        <label class="<?= $classLabel ?>"> Коментарий: </label>
        <?= $form->field($model, 'comment')->textInput()->label(false) ?>

        <?= $form->field($model, 'in_work')->checkbox() ?>
        <?= $form->field($model, 'user_comment')->checkbox() ?>

        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'В работу', ['class' => $model->isNewRecord ? 'btn btn-sm btn-success col-12 input' : ' btn btn-sm col-12 btn-primary input']) ?>

    </div>
</div>

<?php ActiveForm::end(); ?>

