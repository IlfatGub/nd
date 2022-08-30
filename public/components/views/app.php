<?php
use yii\helpers\Url;
use app\modules\admin\models\MyDate;
use app\components\AppViewWidget;
?>



<?php $class = "alert-primary text-dark text-center mt-3" ?>
<?php $style = "margin:0 25px 0 10px" ?>
    <li class="<?=$class?>" style="<?=$style?>">
        В работе - <?= count($active) ?>
    </li>

    <?php if (Yii::$app->user->identity->settings_menu == 1) { ?>
        <style>
            #wrapper {
                padding-left: 260px;
            }
            #sidebar-wrapper {
                width: 270px;
            }
            .sidebar-nav {
                width: 280px;
            }
            .search-width {
                width: 210px;
            }
            .app-menu{
                width: 245px;
            }

        </style>
    <?php } ?>

<?php

$array = [

]
?>


        <?php if(Yii::$app->user->id == 1): ?>
            <?php if(isset($_GET['search'])): ?>
                <?php $none = 'none' ?>
            <?php endif; ?>
            <div>
                <?php if(count($active) > 0){foreach ($active as $item) { ?>
                    <?= AppViewWidget::widget([
                        'iReview'=>$item->review,
                        'iType' => $item->type,
                        'iId' => $item->id,
                        'iContent'=> isset($item->appContent->content) ? $item->appContent->content : null,
                        'iLogin'=>$item->user->login,
                        'iPodr' => $item->podr->name,
                        'iProblem' => $item->problem->name,
                        'iDate' => $item->date_ct,
                        'iPriority' => $item->id_priority,
                        'iFio' => isset($item->appContent->fio->name) ? $item->appContent->fio->name : null,
                        'iIp' => isset($item->appContent->ip) ? $item->appContent->ip : null,
                        'iUsername' => $item->user->username,
                        'iApiLogin' => $item->api_login,
                    ]) ?>
                <?php }} ?>
            </div>

        <?php else: ?>
            <?php if(count($active) > 0){foreach ($active as $item) { ?>

                <?= AppViewWidget::widget([
                    'iReview'=>$item->review,
                    'iType' => $item->type,
                    'iId' => $item->id,
                    'iContent'=> isset($item->appContent->content) ? $item->appContent->content : null,
                    'iLogin'=>$item->user->login,
                    'iPodr' => $item->podr->name,
                    'iProblem' => $item->problem->name,
                    'iDate' => $item->date_ct,
                    'iPriority' => $item->id_priority,
                    'iFio' => isset($item->appContent->fio->name) ? $item->appContent->fio->name : null,
                    'iIp' => isset($item->appContent->ip) ? $item->appContent->ip : null,
                    'iUsername' => $item->user->username,
                    'iBuh' => isset($item->appContent->buh) ? $item->appContent->buh : null,
                    'iApiLogin' => $item->api_login,

                ]) ?>
            <?php }} ?>
        <?php endif; ?>


    <li class="<?=$class?>" style="<?=$style?>">
        В ожидании - <?= count($pending) ?>
    </li>

    <?php if(count($pending) > 0){foreach ($pending as $item) { ?>
        <?= AppViewWidget::widget([
            'iReview'=>$item->review,
            'iType' => $item->type,
            'iId' => $item->id,
            'iContent'=> isset($item->appContent->content) ? $item->appContent->content : null,
            'iLogin'=>$item->user->login,
            'iPodr' => $item->podr->name,
            'iProblem' => $item->problem->name,
            'iDate' => $item->date_ct,
            'iPriority' => $item->id_priority,
            'iFio' => isset($item->appContent->fio->name) ? $item->appContent->fio->name : null,
            'iIp' => isset($item->appContent->ip) ? $item->appContent->ip : null,
            'iUsername' => $item->user->username,
            'iBuh' => isset($item->appContent->buh) ? $item->appContent->buh : null,
            'iApiLogin' => $item->api_login,

        ]) ?>
    <?php }} ?>

    <li class="<?=$class?>" style="<?=$style?>">
        Закрытые
    </li>

    <?php if(count($close) > 0){foreach ($close as $item) { ?>
        <?= AppViewWidget::widget([
            'iReview'=>$item->review,
            'iType' => $item->type,
            'iId' => $item->id,
            'iContent'=> isset($item->appContent->content) ? $item->appContent->content : null,
            'iLogin'=>$item->user->login,
            'iPodr' => $item->podr->name,
            'iProblem' => $item->problem->name,
            'iDate' => $item->date_ct,
            'iPriority' => $item->id_priority,
            'iFio' => isset($item->appContent->fio->name) ? $item->appContent->fio->name : null,
            'iIp' => isset($item->appContent->ip) ? $item->appContent->ip : null,
            'iUsername' => $item->user->username,
            'iBuh' => isset($item->appContent->buh) ? $item->appContent->buh : null,
            'iApiLogin' => $item->api_login,
        ]) ?>
    <?php }} ?>
