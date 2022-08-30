<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\typeahead\TypeaheadBasic;
use app\modules\admin\models\Fio;
use app\modules\admin\models\Login;
use app\modules\admin\models\Problem;
use app\modules\admin\models\Priority;
use app\modules\admin\models\Podr;
use yii\widgets\Pjax;
use app\modules\admin\models\MyDate;





$st_id = isset($_GET['t']) ? $_GET['t'] : null;

?>

<?php //if($_SESSION['User']['login']) {  ?>
    <div class="col-12 " style="padding: 0 0 0 40px">
        <div>
            <!--        Общая информация по заявке -->
            <div class="row" >
                <div class="col-11 col-lg-8" style="border: 1px dashed silver; height: 100%; background: #F4F4F4">
                    <div class="ticket-form ">
                        <div class="row">
                            <div class="bg-infos " style="background: #E4E4E4; border-radius: 2px;  padding:  5px; margin: 5px">
                               Настройка учетной записи
                            </div>
                        </div>

                        <?php $form = ActiveForm::begin(); ?>
                        <div class="row">
                            <div class="col-lg-12">
                                <label> Добавь IP адрес своего компьютера. И с данного компьютера сможешь заходить без авторизации. IP вашего компьютера - <?=$_SERVER['REMOTE_ADDR']?> </label>
                                <?= $form->field($model, 'ip')->textInput(['maxlength' => true, 'class'=>'form-control input-sm input']) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <label> Выбрать домен </label>
                                <?= $form->field($model, 'domain')->dropDownList(Login::getDomianList())->label(false) ?>
                            </div>
                        </div>
                        <hr style="border-top:1px solid #E4E4E4; margin: 5px">
                        <div class="row">
                            <div class="col-lg-12">
                                <label> Если хочешь что бы заявка закрывалась автоматически при добавлении коментария "Выполнено", то поставь галочку </label>
                                <?= $form->field($model, 'close')->checkbox([
                                    'template' => "<div class=\"col-lg-offset-1 col-lg-3\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
                                ]) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <?= $form->field($model, 'assist')->checkbox([
                                    'template' => "<div class=\"col-lg-offset-1 col-lg-3\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
                                ]) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <?= $form->field($model, 'settings_phone')->checkbox([
                                    'template' => "<div class=\"col-lg-offset-1 col-lg-3\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
                                ]) ?>
                            </div>
                        </div>
                        <div class="row <?= $st_id == 10 ? 'alert-success' : ''; ?>" >
                            <div class="col-lg-12">
                                <?= $form->field($model, 'settings_userticket')->checkbox([
                                    'template' => "<div class=\"col-lg-offset-1 col-lg-3\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
                                ]) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <?= $form->field($model, 'settings_active')->checkbox([
                                    'template' => "<div class=\"col-lg-offset-1 col-lg-3\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
                                ]) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <?= $form->field($model, 'settings_recal')->checkbox([
                                    'template' => "<div class=\"col-lg-offset-1 col-lg-3\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
                                ]) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <?= $form->field($model, 'settings_url')->checkbox([
                                    'template' => "<div class=\"col-lg-offset-1 col-lg-3\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
                                ]) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <?= $form->field($model, 'settings_menu')->checkbox([
                                    'template' => "<div class=\"col-lg-offset-1 col-lg-3\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
                                ]) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <?= $form->field($model, 'settings_help')->checkbox([
                                    'template' => "<div class=\"col-lg-offset-1 col-lg-3\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
                                ]) ?>
                            </div>
                        </div>
                        <hr style="border-top:1px solid #E4E4E4; margin: 5px">

                        <div class="row">
                            <div class="col-lg-12">
                                <label> Количество информации заявок в меню.</label>
                                <?= $form->field($model, 'menu')->dropDownList(['0' => 'Стандартный','1' => 'Компактный' ,'2' => 'Больше информации'])->label(false) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <label> Вид комментариев </label>
                                <?= $form->field($model, 'settings_comment')->dropDownList(['0' => 'Стандартный(ФИО)', '1' => 'Компктный(Date/Login)'])->label(false) ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <label> Количество отображаемых заявок(Закрытых/Выполненных). </label>
                                <?= $form->field($model, 'count')->dropDownList(['0' => 0,'10' => 10, '20'=>20, '30'=>30, '50'=>50, '100'=>100, '150'=>150, '200'=>200, '250'=>250])->label(false) ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <label> Список готовых комментариев. Добавляем через запятую </label>
                                <?= $form->field($model, 'comment_list')->textarea(['row' => 2 , 'maxlength' => true, 'class'=>'form-control input-sm input'])->label(false) ?>
                            </div>
                        </div>

                        <div class="form-group col-lg-1 col-lg-offset-5">
                            <?= Html::submitButton('Изменить', ['class' => 'btn btn-sm btn-primary input']) ?>
                        </div>

                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
            <!--        Общая информация по заявке -->
            <br>

<?php //} else {
//    return Yii::$app->response->redirect(['site/login']);
//}

$r = new Problem();
?>
