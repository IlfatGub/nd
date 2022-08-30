<?php

    /**
     * @var  $model \app\models\AppProject;
     */

    use app\components\CommentWidget;
    use app\components\template\DatePickerWidget;
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;
    use app\modules\admin\models\Login;
    use yii\helpers\Url;

//    $model->date_pl = isset($model->date_pl) ? date('Y-m-d', $model->date_pl) : null;
//    $model->date_cur = isset($model->date_cur) ? date('Y-m-d', $model->date_cur) : null;
//    $model->date_ct = isset($model->date_ct) ? date('Y-m-d', $model->date_ct) : null;
?>

<?php
    $this->registerJs(
        '$("document").ready(function(){
            $("#project").on("pjax:end", function() {
            $.pjax.reload({container:"#notes"});  //Reload GridView
        });
    });'
    );
?>


<div class="card mb-3 pb-2" style="max-width: 550px">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
        'id' => 'login-form',
        'fieldConfig' => [
            'options' => ['class' => 'col-lg-121 fs-8'],
            'template' => '<div >{input}</div> ',
            'labelOptions' => ['class' => 'col-sm-2 control-label'],
        ],
    ]); ?>


    <div class="card-body pb-1 ">
        <table class="table table-sm table-hover fs-10 ">
            <tr>
                <th  colspan="2" class="col-3 fs-16">
                    № Задача <?= $model->id ?>
                    <?= Html::submitButton('<i class="fa fa-refresh" aria-hidden="true"></i>', ['class' => 'float-right btn btn-primary btn-sm']) ?>
                </th>
            </tr>

            <tr>
                <th class="col-3">Наименование проекта</th>
                <td class="col-7">
                    <?= $form->field($model, 'name')->textInput(['class' => 'form-control form-control-sm ', 'placeholder' => 'Название', 'disable' => true])->label(false) ?>
                </td>
            </tr>

            <tr>
                <th class="col-3">Дата планирования</th>
                <td class="col-7">
                    <?= DatePickerWidget::widget(['form' => $form, 'model' => $model, 'var' => 'date_transfer_test', 'placeholder' => 'Дата планирования']) ?>
                </td>
            </tr>

            <tr>
                <th class="col-3">Дата по проекту</th>
                <td class="col-7">
                    <?= DatePickerWidget::widget(['form' => $form, 'model' => $model, 'var' => 'date_cur', 'placeholder' => 'Дата по проекту']) ?>
                </td>
            </tr>

            <tr>
                <th class="col-3">Дата поступления</th>
                <td class="col-7">
                    <?= DatePickerWidget::widget(['form' => $form, 'model' => $model, 'var' => 'date_ct', 'placeholder' => 'Дата по проекту']) ?>
                </td>
            </tr>

            <tr>
                <th class="col-3">Инициатор</th>
                <td class="col-7">
                    <?= $form->field($model, 'user_init')->textInput(['class' => 'form-control form-control-sm ', 'placeholder' => 'Инициатор'])->label(false) ?>
                </td>
            </tr>
            <tr>
                <th class="col-3">Кураторы задачи</th>
                <td class="col-7">
                    <?= $form->field($model, 'user_cur_name')->textInput(['class' => 'form-control form-control-sm ', 'placeholder' => 'Кураторы задачи'])->label(false) ?>
                </td>
            </tr>
            <tr>
                <th class="col-3">Куратор</th>
                <td class="col-7">
                    <?= $form->field($model, 'user_exec')->dropDownList(Login::getList(), ['class' => 'form-control form-control-sm ', 'placeholder' => 'Куратор'])->label(false) ?>
                </td>
            </tr>

            <tr>
                <th class="col-3">Статус</th>
                <td class="col-7">
                    <?= $form->field($model, 'status')->dropDownList($model->getProjectStatus(), ['class' => 'form-control form-control-sm ', 'prompt' => ' - Статус - ']); ?>
                </td>
            </tr>

            <tr>
                <th class="col-3">Техничексое задание</th>
                <td class="col-7">
                    <?= $form->field($model, 'tz')->textarea(['class' => 'form-control form-control-sm ', 'placeholder' => 'Техничексое задание'])->label(false) ?>
                </td>
            </tr>

            <tr>
                <th class="col-3">Акт</th>
                <td class="col-7">
                    <?= $form->field($model, 'act')->textarea(['class' => 'form-control form-control-sm ', 'placeholder' => 'Комментарий'])->label(false) ?>
                </td>
            </tr>

        </table>
    </div>
    <?php ActiveForm::end() ?>

    <?php if (Yii::$app->user->can('AdminProject')): ?>
        <div class="col-12">
            <div>
                <div class="row" style="padding-top: 10px">
                    <div class="col-10">
                                <textarea type="text" id="project-comment-text" rows="1"
                                          class="form-control input-sm input app-input-comment"
                                          placeholder="Добавить запись"></textarea>
                    </div>
                    <div>
                        <button class="btn-warning fa fa-plus float-right btn btn-lg ml-2" id="project-comment-add"
                                data-id="<?= $model->id_app ?>" title="Сделать пометку">
                    </div>
                </div>
            </div>
            <div id="project-comment-content">
                <?= CommentWidget::widget(['id' => $model->id_app, 'type' => 1]) ?>
            </div>
        </div>
    <?php endif; ?>

</div>


<!---->
<?php //echo "<pre class='fs-8'>";
    //    print_r($model);
    //    echo "</pre>"; ?>

