<?php
    /**
     * Created by PhpStorm.
     * User: 01gig
     * Date: 18.12.2019
     * Time: 16:41
     *
     * @var $model \app\modules\admin\models\App;
     * @var $app \app\modules\admin\models\App;
     * @var $project \app\models\AppProject;
     */

    use app\components\template\DatePickerWidget;
    use app\models\Sitdesk;
    use app\modules\admin\models\Buh;
    use app\modules\admin\models\Podr;
    use app\modules\admin\models\Problem;
    use kartik\date\DatePicker;
    use kartik\helpers\Html;
    use kartik\select2\Select2;
    use yii\bootstrap\ActiveForm;
    use yii\helpers\ArrayHelper;
    use app\modules\admin\models\Login;

    Date


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
//
//    $model->id_class = $app->id_class;
//    $model->id_object = $app->id_object;
//    $model->id_problem = $app->id_problem;
//    $model->id_podr = $app->id_podr;
//    $model->buh = $app->appContent->buh;
?>
<div class="jumbotron px-5 py-3">
    <h1 class="h4"> Проект: <?= $project->name ?> <br> Добавить задачу </h1>
</div>

<div class="row justify-content-md-center">
    <div class="col-12">
        <label class="<?= $classLabel ?>"> Куратор: </label>
        <?= $form->field($model, 'user_cur')->dropDownList(Login::getList(), ['id' => 'service-id_user','tabindex' => '7', 'class' => $classInput, 'disabled' => $disabled, 'prompt' => '- Выбрать Куратора -'])->label(false) ?>

        <label class="<?= $classLabel ?>"> Исполнитель: </label>
        <?= $form->field($model, 'user_exec')->dropDownList(Login::getList(), ['id' => 'service-id_user','tabindex' => '7', 'class' => $classInput, 'disabled' => $disabled, 'prompt' => '- Выбрать Исполнителя -'])->label(false) ?>

        <label class="<?= $classLabel ?>"> Инициатор: </label>
        <?= $form->field($model, 'user_init')->textInput([ 'class' => $classInput])->label(false) ?>

        <label class="<?= $classLabel ?>"> Дата планирования: </label>
        <?php

             echo '<div class="border border-secondary rounded p-1">';

            echo DatePicker::widget([
                'name' => 'date_pl',
                'value' => $date_to,
                'type' => DatePicker::TYPE_COMPONENT_PREPEND,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                    'class' => 'col-12 ml-2',
                ],
                'pluginEvents' => [
                    "changeDate" => "function(e) {  $('#refreshButton').click();  }",
                ],
            ]);
            echo '</div>'
        ?>


        <label class="<?= $classLabel ?>"> Текст задачи: </label>
        <?= $form->field($model, 'description')->textarea([ 'class' => $classInput])->label(false) ?>

        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'В работу', ['class' => $model->isNewRecord ? 'btn btn-sm btn-success col-12 input' : ' btn btn-sm col-12 btn-primary input']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

<?php //echo "<pre class='fs-12'>"; print_r($project ); echo "</pre>"; die(); ?>
