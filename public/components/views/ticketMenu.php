<?php

use app\models\Sitdesk;
use app\modules\admin\models\History;
use yii\helpers\Url;
use app\modules\admin\models\MyDate;
use yii\helpers\Html;

/**
 * Created by PhpStorm.
 * User: 01gig
 * Date: 18.12.2019
 * Time: 16:41
 *
 * @var $model \app\modules\admin\models\App;
 * @var $name
 * @var $type - тип запроса
 * @var $status - статус заявок
 */


$i=0;
?>
<!-- CSS only -->

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

        .app-menu {
            width: 245px;
        }

    </style>
<?php } ?>

<?php
$status_array = [1, 11, 12];
?>

<?php $class = 'alert-primary text-dark text-center mt-3'; ?>
<?php $style = "margin:0 25px 0 10px" ?>

<li class="<?= $class ?>" style="<?= $style ?>">
    <?= $name ?> <?php echo count($model) ?>
</li>



<?php foreach ($model as $item): ?>

<?php $i++; ?>
    <?php
    $iType = $item->type;
    $iApiLogin = $item->api_login;
    $iReview = $item->review;
    $iId = $item->id;
    $iContent = isset($item->appContent->content) ? $item->appContent->content : null;
    $iLogin = $item->user->login;
    $iPodr = $item->podr->name;
    $iProblem = $item->problem->name;
    $iDate = $item->date_ct;
    $iPriority = $item->id_priority;
    $iFio = isset($item->appContent->fio->name) ? $item->appContent->fio->name : null;
    $iIp = isset($item->appContent->ip) ? $item->appContent->ip : null;
    $iUsername = $item->user->username;
    ?>

    <?php $content = ''; ?>
    <?php //$review = isset($iReview) ? 'help_main_border_active' : 'help_m
    //ain_border'; //Меняем цвет в зависимотсти от просмотра заявки(просмотрено/ не просмотрено)?>
    <?php if (isset($item->type)): ?>
        <?php $review = $item->type == 1 ? 'help_main_border_active' : 'help_main_border'; //Меняем цвет в зависимотсти от заявки(служебка, звонок)?>
        <?php $review = $item->type == 4 ? 'help_main_border_user' : $review; //Меняем цвет в зависимотсти от заявки(служебка, звонок)?>
    <?php else: ?>
        <?php $review = 'help_main_border'; ?>
    <?php endif; ?>



    <?php $icon = isset($iType) ? 'far fa-list-alt ' : 'fas fa-phone'; //Меняем иконку в зависимотсти от заявки(служебка, звонок)?>
    <?php $icon = $iType == 4 ? 'fa fa-address-book ' : $icon; //Меняем иконку в зависимотсти от заявки(служебка, звонок)?>
    <?php $icon = isset($iApiLogin) ? 'fa fa-address-book ' : $icon; //Меняем иконку в зависимотсти от заявки(служебка, звонок)?>
    <?php $review = isset($iApiLogin) ? 'help_main_border_user' : $review; //Меняем цвет в зависимотсти от заявки(служебка, звонок)?>

    <?php $review = $item->agreed == 2 ? 'help_main_border_no_agreed' : $review; //Меняем цвет в зависимотсти от согласованности заявки ?>
    <?php $review = isset($item->id_project) ? 'help_main_border_project' : $review; //Меняем цвет в зависимотсти от согласованности заявки ?>

    <?php $buh = isset($iBuh) ? '<span class="badge badge-warning">1C</span>' : ''  //Добавляем значок 1с, если заведен как заявка 1с?>
    <?php $danger = $iPriority == 3 ? 'alert-danger' : '1'; ?>
    <?php if (isset($_GET['id'])) {
        if ($_GET['id'] == $iId) {
            $style = "background: #B1D6F0; border-right: 0px solid white; ";
        } else {
            $style = '';
        }
    } ?>

    <?php if (Yii::$app->user->identity->menu == 0) {
        $content .= strlen($iFio) > 0 ? 'ФИО: ' . $iFio . "<br>" : '';
        $content .= strlen($iIp) > 0 ? 'Ip: ' . $iIp . "<br>" : '';
        $content .= '----------------------------------------------<br>';
        $content .= $iContent;
    } else {
        $content = $iPodr . " - " . $iProblem . "<br><hr style='margin: 0; padding: 0'>";
        $content .= strlen($iFio) > 0 ? 'ФИО: ' . $iFio . "<br>" : '';
        $content .= strlen($iIp) > 0 ? 'Ip: ' . $iIp . "<br>" : '';
        $content .= '----------------------------------------------<br>';
        $content .= $iContent;
    } ?>


    <?php
        $remind = new \app\modules\admin\models\AppRemind(['id_app' => $iId]);
        $_rem = $remind->existsIdApp() ? "<span class=\" badge badge-warning\">!</span>" : '';
    ?>

    <style>
        .tooltip {
            margin-left: 25px;
        }

        .tooltip-inner {
            max-width: 400px
        }
    </style>

    <li class="app-menu" data-toggle="tooltip" data-placement="right" title="<?= nl2br(Html::encode($content)) ?>">
        <div class="<?= $review ?> sidebar-brand" style="margin-left: 10px;   <?= isset($style) ? $style : '' ?>">

            <a href="<?= Url::to(['/index', 'id' => $iId, 'search' => isset($_GET['search']) ? $_GET['search'] : null, '#' => 'hs_' . $iId]) ?>" id="hs_<?=$iId?>">
                <div style="display: inline-block">
                    <div class="<?= $danger ?> appview_width "
                         style=" <?= Yii::$app->user->identity->settings_menu == 1 ? "width: 220px" : "width: 300px" ?>">
                        <strong>
                            <span class="<?= $icon . ' ' . $danger ?>"></span> <?= $iId ?>
                        </strong>
                        <small class="appview_username"> <?= Yii::$app->user->id == 3 ? $i : null; ?>
                            <?= $_rem ?>
                            <?= Yii::$app->user->identity->settings_menu == 1 ? Sitdesk::fio($iUsername, 1) : Sitdesk::fio($iUsername) ?>
                            <?= $buh ?>
                        </small>
                        <div class="appview_date">
                            <small><?= MyDate::getDate($iDate) ?> </small>
                        </div>
                    </div>
                    <?php if (Yii::$app->user->identity->menu == 0) { ?>
                        <div>
                            <small> <?= $iPodr ?> - <?= $iProblem ?>  </small>
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

            <?php if (in_array($status, $status_array)): ?>
                <?php
                $history = new History(['id_app' => $item->id]);
                $end_date = $history->endDate();
                $percent = 100;

                $runtime = $history->getLastRuntime();

                $_date = $item->date_ct;

                $_runtime = isset($runtime) ? $runtime * 60 : null;
                if ($_date and $_runtime) {

                    $sub = (int)(($end_date - strtotime('now')) / 60);

                    $_sub = (int)(($end_date - $_date)/60);

                    if ($sub > 0)
                        $percent = 100 - ($sub * 100) / $_sub;
                }
                ?>
            <?php endif; ?>

            <?php if(in_array($status, $status_array)): ?>
            <?php
                $per = (int)($percent); $style = '';
                if ($per > 70)
                    $style = '';
                if ($per >=100)
                    $style = 'bg-danger'
                ?>

                <div class="progress" style="height: 2px;">
                    <div class="progress-bar <?=$style?>" role="progressbar" style="width: <?=(int)($percent)?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            <?php endif; ?>
        </div>

    </li>
<?php endforeach; ?>
