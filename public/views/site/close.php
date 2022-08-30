<!---->
<!--<pre>-->
<!--    --><?//=print_r($model)?>
<!--</pre>-->

<?php
use app\modules\admin\models\Podr;
use app\modules\admin\models\MyDate;
use yii\helpers\Html;
use app\modules\admin\models\AppComment;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;

?>
<div class="col-md-12 px-5">
    <?php Pjax::begin(['id' =>'ups', 'enablePushState' => false]); ?>
    <?php if(isset($model)){ ?>
        <?= Html::a('<span class="glyphicon glyphicon-remove"> Отметить все, как прочтенные </span>',['close', 'delete' => 'All']);?>
        <br><br>
        <?php foreach ($model as $item){ ?>
            <?php $icon = isset($item->type) ?  'glyphicon glyphicon-list-alt' : 'glyphicon glyphicon-earphone'; ?>
            <table class="table table-sm table-bordered col-12 py-0 px-0" style="font-size: 10pt;display: inline-block !important; vertical-align: top;">
                <tr style="background: #E4E4E4">
                    <td class="text-center" style="width: 70px;"> №<?= $item->id ?> </td>
                    <td class="text-center" style="width: 35px; font-size: 10pt"> <span class="<?= $icon ?>"></span></td>
                    <td class="text-center" style="width: 100px;"> <?= $item->priority->name ?> </td>
                    <td class="text-center" style="width: 150px;"> <?= MyDate::getDate($item->date_ct) ?> </td>
                    <td class="text-center" style="width: 200px;"> <?= $item->podr->name ?> - <?= $item->problem->name ?> </td>
                    <td class="text-center" style="width: 250px ;"> <?= $item->user->username ?> </td>
                    <td class="text-center" style="width: 50px; vertical-align: middle; ">
                        <?php if ($item->status == 3){ ?>
                            <?= Html::a('<span class="fas fa-times" ></span>',['close', 'delete' => $item->appContent->id]);?>
                        <?php } ?>
                </tr>
                <?php if(isset($item->type)){?>
                    <tr>
                        <td colspan="8">
                            <strong>Сл.записка:  <?= $item->appContent->dv ?></strong>
                        </td>
                    </tr>
                <?php } ?>

                <tr>
                    <td colspan="8" style="font-size:10pt; padding: 10px 20px">
                        <?= $item->appContent->content ?>
                    </td>
                </tr>
                <?php foreach (AppComment::find()->where(['id_app' => $item->id])->orderBy(['date' => SORT_DESC])->joinWith(['user'])->all() as $comment){ ?>
                    <tr>
                        <?php if(Yii::$app->user->identity->settings_comment == 0){ ?>
                            <td class="comment-main bg-sitdesk-block" colspan="7" style="font-size: 8pt ">
                                <small style=" max-width: 200px;">
                                    <?= MyDate::getDate($comment->date, 1) ?>. <strong><?= \app\models\Sitdesk::fio($comment->user->username) ?>: </strong>
                                </small>
                                <?= nl2br($comment->comments->name) ?>
                            </td>
                        <?php }else{ ?>
                            <td class="bg-sitdesk-block" colspan="7" style="font-size: 8pt ">
                                <small style="">
                                    <?= MyDate::getDate($comment->date, 1) ?>.<strong><?= $comment->user->login ?></strong>
                                </small>
                                <?= nl2br($comment->comments->name) ?>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>
    <?php } ?>
    <h2> <?= $status ?> </h2>
    <?php Pjax::end(); ?>
</div>



