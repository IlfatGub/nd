<?php

use app\modules\admin\models\AppComment;
use app\modules\admin\models\MyDate;
use app\modules\admin\models\Status;
use app\modules\admin\models\History;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;


$id = isset($_GET['id']) ? $_GET['id'] : null;
$app = isset($_GET['app']) ? $_GET['app'] : null;
$search = isset($_GET['search']) ? $_GET['search'] : null;
?>



    <div id="myAffix" class="alert alert-info py-1 px-1 mt-2 mb-1 text-dark stick_menu" data-spy="affix"
         data-offset-top="60" data-offset-bottom="200">
        <div style="display: inline-block;">
            <?php if (!isset($model)) { ?>
                Новая заявка
            <?php } else { ?>
                <small><strong>№ <?= $id ?></strong></small>
                <small class="topForm "> <?= MyDate::getDate($model->date_ct) ?>
                    ( <?= Status::Name($model->status) ?> )
                </small>
            <?php } ?>
        </div>


        <?php if ((Yii::$app->user->id == $model->id_user or Yii::$app->user->can('Admin') or Yii::$app->user->can('Disp'))  and $model->status != Status::STATUS_AGREED) { ?>
            <?= isset($model) ? Html::button('', ['value' => Url::to(['site/history', 'id' => $id]), 'class' => 'btn btn-sm  btn-info fas fa-info modalButton', 'title' => 'История заявки']) : ''; ?>

            <?php if ((Yii::$app->user->can('Disp') or Yii::$app->user->can('Admin')))  { ?>
                <?php if (Yii::$app->user->can('Disp')) { ?>
                    <?php Pjax::begin(['id' => 'call', 'enablePushState' => false, 'options' => ['style' => 'display:inline-block']]); ?>
                    <?= Html::a('<span class="btn  btn-sm btn-info fas fa-phone" title="Справочная"></span>', ['/site/call']) ?>
                    <?php Pjax::end(); ?>
                <?php } ?>

                <?= Html::a('<span class="btn btn-sm  btn-info fas fa-plus" title="Добавить новую заявку"></span>', ['index', 'app' => 1])?>

                <?php if(isset($model) and !$model->id_project){ ?>
                    <button class="btn btn-sm  btn-warning fa fa-address-book modalButton-xl" title="Добавить аналогичную заявку" value="<?= Url::toRoute(['service-add', 'id' => $model->id]) ?>"></button>
                <?php } ?>
            <?php } ?>

            <?php if(Yii::$app->user->can('AdminProject') and $model->id_project and $model->type == 5){ ?>
                <button class="btn btn-sm  btn-warning fa fa-anchor modalButton-xl"  title="Добавить задачу про проекту"  value="<?= Url::toRoute(['project-add', 'id' => $model->id]) ?>"></button>
            <?php } ?>

            <div style="float: right">
                <?php if (isset($_GET['app'])) { ?>
                    <input class="btn btn-sm  btn-primary fas fa-" type="button" value="dv" id="app_dv_btn"  onClick="show('htb1')">
                    <input class="btn btn-sm  btn-warning fas fa-" type="button" value="1C" id="buh" onClick="show('htb2');">
                <?php } else { ?>
                    <input class="btn btn-sm  btn-warning fas fa-" type="button" value="1C" id="buh" onClick="show('htb2');">

                    <?php if (AppComment::appComment($model->id)) {
                        $statusStyle = 'inline-block';
                    } else {
                        $statusStyle = 'display-none';
                    } ?>
                    <?php $serach = isset($_GET['search']) ? $_GET['search'] : null ?>

                    <?php if ($model->status != 11 and $model->status != 12) { ?>
                        <?php if ($model->status == 1) { ?>
                            <button class="btn btn-sm  btn-primary glyphicon fas fa-question-circle modalButton-xl" title="В ожидание" value="<?= Url::toRoute(['status-form', 'id' => $id, 'status' => 2]) ?>"></button>
                            <?= Html::a('<span class="btn btn-sm  btn-primary glyphicon fas fa-ban ' . $statusStyle . '"></span>', ['status', 'id' => $id, 'status' => 3, 'search' => $serach], ['title' => 'Закрыть', 'id' => 'status' . $id]) ?>
                        <?php } ?>

                        <?php if ($model->status == 2) { ?>
                            <?= Html::a('<span class="btn btn-primary btn-sm  fas fa-fire"></span>', ['status', 'id' => $id, 'status' => 1, 'search' => $serach], ['title' => 'В работу']) ?>
                            <?= Html::a('<span class="btn btn-sm  btn-primary glyphicon fas fa-ban ' . $statusStyle . '"></span>', ['status', 'id' => $id, 'status' => 3, 'search' => $serach], ['title' => 'Закрыть', 'id' => 'status' . $id]) ?>
                        <?php } ?>

                        <?php if ($model->status == 3) { ?>
                            <?= Html::a('<span class="btn btn-primary btn-sm  fas fa-fire"></span>', ['status', 'id' => $id, 'status' => 1, 'search' => $serach], ['title' => 'В работу']) ?>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            </div>
        <?php } ?>
        <?php if (Yii::$app->user->can('SuperAdmin')) { ?>
            <?= Html::a('<span class="btn btn-sm  btn-danger glyphicon far float-right fa-trash-alt"></span>', ['delete', 'id' => $id, 'search' => $serach], ['title' => 'Удалить заявку и все что с ним связано', 'data-confirm' => 'Удалить?'], ['data-confirm' => 'Удалить?']) ?>
        <?php } ?>

    </div>


<?php if(!$app): ?>
    <?php
    $history = new History(['id_app' => $id]);
    $runtime = $history->getLastRuntime();
    $end_date = $history->endDate();

    ?>
    <?php if(isset($model->agreed)): ?>
        <div id="myAffix" class="alert <?= $model->agreed == 1 ? 'alert-success' : 'alert-danger' ?> py-1 px-1 mt-2 mb-1 fs-10 text-dark stick_menu" data-spy="affix" data-offset-top="60" data-offset-bottom="200">
            <?= $model->agreed == 1 ? '+' : 'Не согласован' ?>  <span class="float-right"> <?= date('Y-m-d H:i:s',$end_date); ?> / <?= MyDate::normalizeTime($history->getLastRuntime()); ?></span>
        </div>
    <?php else: ?>
        <div id="myAffix" class="alert alert-success py-1 px-1 mt-2 mb-1 fs-10 text-dark stick_menu" data-spy="affix" data-offset-top="60" data-offset-bottom="200">
            До <?= date('Y-m-d H:i:s',$end_date); ?> <span class="float-right"> <?= MyDate::normalizeTime($history->getLastRuntime()); ?></span>
        </div>
    <?php endif; ?>

<?php endif; ?>

