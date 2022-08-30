<?php


use yii\helpers\Html;
// use yii\bootstrap\ActiveForm;
use yii\bootstrap4\ActiveForm;
// use kartik\form\ActiveForm;
use app\models\LoginForm;

?>

<style>
    #sidebar-wrapper{
        display: none !important;
    }
    #wrapper{
        padding-left: 0 !important;
    }
    .modal-dialog{
        width: 500px;
    }
</style>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" align="center">
                <h1>Sitdesk</h1>
            </div>
            <?php
            if(Yii::$app->session->get('error')) : ?>
                <div class="col-lg-12" style="margin-top: 10px; margin-bottom: 20px">
                    <div class="alert alert-danger" style="margin-bottom: 0px">
                        <?= Yii::$app->session->get('error') ?>
                    </div>
                </div>
            <?php endif ?>
            <div id="div-forms">
                <?php $form = ActiveForm::begin([
                    'id' => 'login-form',
                ]); ?>
                    <div class="modal-body">
                        <label for=""> Login </label>
                        <?= $form->field($model, 'username')->textInput(['autofocus' => true])->label(false) ?>

                        <label for=""> Password </label>
                        <?= $form->field($model, 'password')->passwordInput()->label(false) ?>

                         <?= $form->field($model, 'rememberMe')->checkbox() ?>
                    </div>
                    <div class="modal-footer">
                        <div>
                            <button type="submit" class="btn btn-primary btn-lg btn-block">Login</button>
                        </div>
                    </div>
                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>

<?php unset(Yii::$app->session['error']) ?>
