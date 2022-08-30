
<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Sitdesk;
use app\modules\admin\models\Login;
use app\components\AssistsAppWidget;
?>





<?php  foreach ($model as $item) { ?>
    <?php if(Login::validateLoginApp($item->id)){ ?>

        <div class="panel label-info">
            <div class="panel-heading alert-info" >
                <div class="panel-title"> <?= $item->username ?> </div>
            </div>
            <!--            <div class="panel-body" style="display: inline-block;  padding: 0 !important; ">-->
            <?= AssistsAppWidget::widget(['id' => $item->id]) ?>
            <!--            </div>-->
        </div>

    <?php } ?>
<?php } ?>
