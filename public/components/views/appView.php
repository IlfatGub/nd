<?php
use app\models\Sitdesk;
use yii\helpers\Url;
use app\modules\admin\models\MyDate;
use yii\helpers\Html;
?>
<?php $content = ''; ?>
<?php //$review = isset($iReview) ? 'help_main_border_active' : 'help_main_border'; //Меняем цвет в зависимотсти от просмотра заявки(просмотрено/ не просмотрено)?>
<?php $review = isset($iType) ? 'help_main_border_active' : 'help_main_border'; //Меняем цвет в зависимотсти от заявки(служебка, звонок)?>
<?php $review = isset($iApiLogin) ? 'help_main_border_user' : $review; //Меняем цвет в зависимотсти от заявки(служебка, звонок)?>


<?php $icon = isset($iType) ? 'far fa-list-alt ' : 'fas fa-phone'; //Меняем иконку в зависимотсти от заявки(служебка, звонок)?>
<?php $icon = isset($iApiLogin) ? 'fa fa-address-book ' : $icon; //Меняем иконку в зависимотсти от заявки(служебка, звонок)?>


<?php $buh = isset($iBuh) ? '<span class="badge badge-warning">1C</span>' : ''  //Добавляем значок 1с, если заведен как заявка 1с?>
<?php $danger = $iPriority == 3 ? 'alert-danger' : '1'; ?>
<?php if (isset($_GET['id'])) {
    if ($_GET['id'] == $iId) {
        $style = "background: #B1D6F0; border-right: 0px solid white; ";
    } else {
        $style = '';
    }
} ?>
<?php //if(Yii::$app->user->id == 1): ?>
<!--    --><?php //echo "<pre>";
//    print_r($_GET); ?>
<?php //endif; ?>

<?php if (Yii::$app->user->identity->menu == 0) {
    $content .= strlen($iFio) > 0 ? 'ФИО: ' . $iFio . "<br>" : '' ;
    $content .= strlen($iIp) > 0 ? 'Ip: ' . $iIp . "<br>" : '';
    $content .= '----------------------------------------------<br>';
    $content .= $iContent;
} else {
    $content = $iPodr . " - " . $iProblem . "<br><hr style='margin: 0; padding: 0'>";
    $content .= strlen($iFio) > 0 ? 'ФИО: ' . $iFio . "<br>" : '' ;
    $content .= strlen($iIp) > 0 ? 'Ip: ' . $iIp . "<br>" : '';
    $content .= '----------------------------------------------<br>';
    $content .= $iContent;
} ?>
<style>
    .tooltip {
        margin-left: 25px;
    }
    .tooltip-inner{
        max-width: 400px
    }
</style>

    <li  class="app-menu" data-toggle="tooltip" data-placement="right" title="<?= nl2br(Html::encode($content)) ?>">
        <div class="<?= $review ?> sidebar-brand" style="margin-left: 10px;   <?= isset($style) ? $style : '' ?>">

            <a href="<?= Url::to(['/index', 'id' => $iId, 'search' => isset($_GET['search']) ? $_GET['search'] : null, '#' => 'hs_' . $iId]) ?>" id="hs_<?=$iId?>">
                <div style="display: inline-block">
                    <div class="<?= $danger ?> appview_width " style=" <?= Yii::$app->user->identity->settings_menu == 1 ? "width: 220px" : "width: 300px" ?>">
                        <strong>
                            <span class="<?= $icon . ' ' . $danger ?>"></span>  <?= $iId ?>
                        </strong>
                        <small class="appview_username">
                            <?= Yii::$app->user->identity->settings_menu == 1 ? Sitdesk::fio($iUsername, 1) : Sitdesk::fio($iUsername) ?>
                            <?=$buh?>
                        </small>
                        <div class="appview_date">
                            <small><?= MyDate::getDate($iDate) ?></small>
                        </div>
                    </div>
                    <?php if (Yii::$app->user->identity->menu == 0) { ?>
                        <div>
                            <small> <?= $iPodr ?> - <?= $iProblem ?></small>
                        </div>
                    <?php } ?>
                    <?php if (Yii::$app->user->identity->menu == 2) { ?>
                        <div>
                            <small> <?= $iPodr ?> - <?= $iProblem ?></small>
                        </div>
                        <div>
                            <small> <?= $iFio ?> <?= $iIp == '' ? '' : ' - ' . $iIp ?></small>
                        </div>
                    <?php } ?>
                </div>
            </a>
        </div>
    </li>
