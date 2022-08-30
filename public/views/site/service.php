<?php
/**
 * Created by PhpStorm.
 * User: 01gig
 * Date: 18.12.2019
 * Time: 16:41
 *
 * @var $model \app\modules\admin\models\App;
 * @var $app \app\modules\admin\models\App;
 */

use app\models\Sitdesk;
use app\modules\admin\models\Buh;
use app\modules\admin\models\Podr;
use app\modules\admin\models\Problem;
use kartik\helpers\Html;
use kartik\select2\Select2;
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



$model->id_class = $app->id_class;
$model->id_object = $app->id_object;
$model->id_problem = $app->id_problem;
$model->id_podr = $app->id_podr;
$model->buh = $app->appContent->buh;
?>
<div class="jumbotron px-5 py-3">
    <h1 class="h4">Добавить новую услугу <br> заявка № <?= $_GET['id'] ?></h1>
</div>

<div class="row justify-content-md-center">
    <div class="col-12">
        <label class="<?= $classLabel ?>"> Тип проблемы </label>

        <?= $form->field($model, 'id_class')->dropDownList(ArrayHelper::map(Problem::getProblemMain(), 'id', 'name'), ['id' => 'service-id_class', 'tabindex' => '6', 'class' => $classInput . ' buh', 'disabled' => $disabled, 'prompt' => '- Выбрать проблему -'])->label(false) ?>
        <?= $form->field($model, 'id_object')->dropDownList(ArrayHelper::map(Problem::getProblemMain($model->id_class), 'id', 'name'), ['id' => 'service-id_object', 'tabindex' => '6', 'class' => $classInput . ' buh', 'disabled' => $disabled])->label(false) ?>
        <?= $form->field($model, 'id_problem')->dropDownList(ArrayHelper::map(Problem::getProblemMain($model->id_object), 'id', 'name'), ['id' => 'service-id_problem', 'tabindex' => '6', 'class' => $classInput . ' buh', 'disabled' => $disabled])->label(false) ?>

        <label class="<?= $classLabel ?>"> Исполнитель: </label>
        <?= $form->field($model, 'id_user')->dropDownList(Login::getList(), ['id' => 'service-id_user','tabindex' => '7', 'class' => $classInput, 'disabled' => $disabled, 'prompt' => '- Выбрать исполнителя -'])->label(false) ?>

        <label class="<?= $classLabel ?>"> 1С база: </label>
        <?= $form->field($model, 'buh')->dropDownList(Buh::getList(), ['id' => 'service-buh', 'tabindex' => '7', 'class' => $classInput, 'disabled' => $disabled, 'prompt' => '- Выбрать базу 1с -'])->label(false) ?>

        <label class="<?= $classLabel ?>"> Подразделение: </label>
        <?= $form->field($model, 'id_podr')->dropDownList(ArrayHelper::map(Podr::getList(), 'id', 'name'), ['id' => 'service-id_podr', 'tabindex' => '7', 'class' => $classInput, 'disabled' => $disabled, 'prompt' => '- Выбрать подразделение -'])->label(false) ?>

        <label class="<?= $classLabel ?>"> Коментарий: </label>
        <?= $form->field($model, 'comment')->textInput()->label(false) ?>

        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'В работу', ['class' => $model->isNewRecord ? 'btn btn-sm btn-success col-12 input' : ' btn btn-sm col-12 btn-primary input']) ?>

    </div>
</div>

<?php ActiveForm::end(); ?>

