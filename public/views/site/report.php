<?php

    use app\components\AppViewWidget;
    use app\components\AppWidget;
    use app\models\Sitdesk;
    use app\modules\admin\models\History;
    use app\modules\admin\models\MyDate;
    use app\modules\admin\models\Podr;
    use app\modules\admin\models\Status;
    use yii\helpers\ArrayHelper;
    use yii\widgets\Pjax;
    use yii\helpers\Html;
    use kartik\date\DatePicker;
    use yii\helpers\Url;
    use phpnt\chartJS\ChartJs;

    /**
     * @var $model  \app\modules\admin\models\App;
     * @var $history \app\modules\admin\models\History;
     */


    $sitedesk = new Sitdesk();

    $func = function ($value) {
        return Sitdesk::fio($value, 1);
    };

    $class = '';

    $depart_name = ArrayHelper::map(\app\models\Depart::getDepart(), 'id', 'name');

    $__status = [100, 12, 1, 2, 3, 200];
    $_total = array_fill_keys($__status, 0);

    echo $id_depart;
    $params = [
        'class' => 'form-control col-3',
        'prompt' => 'Выберите организацию...',
        'id' => 'report-org',
        'onchange' => "function(e) {  $('#refreshButton').click();  ",
        'options' => [
            $org => ['selected' => true]
        ]
    ];

    $params_dep = [
        'class' => 'form-control ml-2 col-5',
        'prompt' => 'Выберите отдел...',
        'id' => 'report-depart',
        'onchange' => "function(e) {  $('#refreshButton').click();  ",
        'options' => [
            $id_depart => ['selected' => true]
        ]
    ];

?>


<?php Pjax::begin(['id' => 'stat', 'enablePushState' => false]); ?>

<?php
    $for_day =  $sitedesk->getReportForDay($date_to, $date_do);
    $status_for_day = $for_day['status'];
    $user_for_day = $for_day['user'];
    $login_for_day = $for_day['login'];
?>

<?php $count = 0;
    $activ = 0;
    $pending = 0;
    $close = 0;
    $dv = 0; ?>

<div class="row" style="font-size: 10pt !important;">
    <div class="col-12">
        <?= Html::beginForm(['site/report'], 'post', ['data-pjax' => '', 'class' => 'form-inline']); ?>
        <div class="col-12 row text-center">
            <?=
                DatePicker::widget([
                    'name' => 'date_to',
                    'value' => $date_to,
                    'type' => DatePicker::TYPE_RANGE,
                    'name2' => 'date_do',
                    'value2' => $date_do,
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                        'class' => 'col-6 ml-2',
                    ],
                    'pluginEvents' => [
                        "changeDate" => "function(e) {  $('#refreshButton').click();  }",
                    ],
                ]);
            ?>

        </div>

        <div class="col-12 mt-2  row text-center">
            <?= Html::dropDownList('org', 'null', ArrayHelper::map(Podr::getList(), 'id', 'name'), $params); ?>
            <?= Html::dropDownList('depart', 'null', $depart, $params_dep); ?>
            <?= Html::submitButton('Поиск ', ['class' => 'btn btn-primary display-non1e ml-1 invisibl1e', 'id' => 'refreshButton', 'name' => 'hash-button']) ?>
        </div>
        <?= Html::endForm() ?>
    </div>

    <hr>

    <div class="col-12">
        <?php

            $_user_app = ArrayHelper::map($app, 'id', 'status', 'user.username');
            $_user_app_username = ArrayHelper::map($app, 'id_user', 'user.username');
            $_user_runtime = ArrayHelper::map($app, 'id', 'history.runtime');
            $_user_date_ct = ArrayHelper::map($app, 'id', 'date_ct');
            $_user_date_cl = ArrayHelper::map($app, 'id', 'appContent.date_cl');

            $user_total = $user_status = array();
            foreach ($_user_app as $username => $tickets) {
                if ($username) {
                    $_stat = array();
                    $_stat[100] = 0; //общее количество заявок для пользователя
                    $_stat[200] = 0; //просроченные заявки
                    foreach ($tickets as $id_app => $status) {

                        $_stat[100] += 1;

                        if (array_key_exists($status, $_stat)) {
                            $_stat[$status] += 1;
                        } else {
                            $_stat[$status] = 1;
                        }
                    }
                    $user_status[$username] = $_stat;
                    $user_total[] = $_stat[100];
                }
            }
            $user_total[] = 0;

        ?>
        <br>

        <table class="table table-sm table-hover text-center table-bordered">
            <thead>
            <tr class="alert-info">
                <th colspan="7"> Заявки непосредственно у пользователей</th>
            </tr>
            <tr>
                <th class="text-left">ФИО</th>
                <th>всего</th>
                <th>На рассмотрении</th>
                <th>В работе</th>
                <th>Отложено</th>
                <th>Закрыто</th>
                <th>Просрочено</th>
            </tr>
            </thead>

            <?php foreach ($user_status as $username => $status): ?>
                <?php $class = $username == Yii::$app->user->identity->username ? 'alert-warning' : ''; ?>
                <tr class="<?= $class ?>">
                    <td class="text-left"><?= $username ?></td>

                    <?php foreach ($__status as $st): ?>
                        <?php
                        $res = array_key_exists($st, $status) ? $status[$st] : 0;
                        $_total[$st] += $res;
                        ?>

                        <td><?= $res ?></td>

                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            <tr class="alert-danger">
                <td class="text-left">Всего</td>
                <?php foreach ($__status as $st): ?>
                    <td><?= array_key_exists($st, $_total) ? $_total[$st] : 0 ?></td>
                <?php endforeach; ?>

            </tr>
        </table>

        <table class="table table-sm table-hover text-center table-bordered">
            <thead>
            <tr class="alert-info">
                <th colspan="7"> По датам</th>
            </tr>
            <tr>
                <th class="text-left">Дата</th>
                <th>В работе</th>
                <th>На рассмотрении</th>
                <th>Отложено</th>
                <th>Закрыто</th>
                <th>всего</th>
            </tr>
            </thead>

            <?php foreach ($status_for_day as $day => $status): ?>
                <tr class="<?= $class ?>">
                    <td class="text-left"><?= $day . $sitedesk->getDayRus(strtotime($day)) ?></td>
                    <td class="text-left"><?= $status[1] ?></td>
                    <td class="text-left"><?= $status[12] ?></td>
                    <td class="text-left"><?= $status[2] ?></td>
                    <td class="text-left"><?= $status[3] ?></td>
                    <td class="text-left alert-danger"><?= $status[3] +$status[1] + $status[12] +$status[14] +$status[11] ?></td>
                </tr>
            <?php endforeach; ?>

        </table>

<?php $dae_total=[];  ?>

        <table class="table table-sm table-hover text-center table-bordered">
            <thead>
            <tr class="alert-info">
                <th class="text-left" style="width: 250px !important;">ФИО</th>
                    <th class="text-left" colspan="20" >Даты</th>
            </tr>
            <tr>
                <th class="text-left" style="width: 250px !important;"></th>
                <?php foreach ($user_for_day as $day => $users): ?>
                    <th class="text-left" style="writing-mode: tb-rl;"><?=date('m-d') .  $sitedesk->getDayRus(strtotime($day))?></th>
                <?php endforeach; ?>
            </tr>
            </thead>


            <?php foreach($login_for_day as $item): ?>
                <tr class="<?= $class ?>">
                    <td style="width: 250px !important;"><?=$_user_app_username[$item]?></td>
                    <?php foreach ($user_for_day as $day => $users): ?>
                        <?php $days =  $day ?>
                        <td class="text-left"><?= $users[$item] ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>

            <tr class=" alert-danger">
                <td class="text-left" style="width: 250px !important;">Всего</td>
                <?php foreach ($user_for_day as $users): ?>
                    <?php $i =0; ?>
                    <?php foreach($users as $item): ?>
                        <?php $i = $i + $item; ?>
                    <?php endforeach; ?>
                    <td class="text-left"  ><?=$i?></td>
                <?php endforeach; ?>
            </tr>

        </table>




        <?php
            if (isset($username)) {
                $dataWeatherTwo = [
                    'labels' => array_map($func, array_keys($user_status)),
                    'datasets' => [
                        [
                            'data' => $user_total,
                            'label' => "Всего заявок",
                            'fill' => true,
                            'lineTension' => 0.1,
                            'backgroundColor' => "rgba(255,61,103,0.4)",
                            'borderColor' => "rgba(75,192,192,1)",
                            'borderCapStyle' => 'butt',
                            'borderDash' => [],
                            'borderDashOffset' => 0.0,
                            'borderJoinStyle' => 'miter',
                            'pointBorderColor' => "rgba(75,192,192,1)",
                            'pointBackgroundColor' => "#FF6384",
                            'pointBorderWidth' => 1,
                            'pointHoverRadius' => 2,
                            'pointHoverBackgroundColor' => "rgba(75,192,192,1)",
                            'pointHoverBorderColor' => "rgba(220,220,220,1)",
                            'pointHoverBorderWidth' => 2,
                            'pointRadius' => 1,
                            'pointHitRadius' => 1,
                            'spanGaps' => true,
                        ],
                    ]
                ];


                echo ChartJs::widget([
                    'type' => ChartJs::TYPE_BAR,
                    'data' => $dataWeatherTwo,
                    'options' => []
                ]);
            }

        ?>
        <br>
        <br>
        <br>
        <br>


        <?php
            $app_for_depart = ArrayHelper::map($app, 'id', 'id_problem', 'depart.name');
            $replace_arr = [
                    'ОП "Салаватский" ООО "НХРС".',
                    'ОП Центральный офис ООО "НХРС".',
                    'Департамент по правовой работе.',
                    'Департамент финансов и экономики.',
                ];
            krsort($app_for_depart);
        ?>


        <!--    Статистика по отделам-->
        <div class="col-12">
            <table class="table table-bordered table-sm  table-hover" style="margin-top: 25px">
                <tr class="alert-primary">
                    <td> Отдел</td>
                    <td class="text-center"> Количество</td>
                </tr>
                <?php foreach ($app_for_depart as $key => $item) { ?>
                    <tr class="<?= $class ?>">
                        <td style="padding: 2px"><?= str_replace($replace_arr, '', $key); ?></td>
                        <td style="padding: 2px; width: 100px;"
                            class="text-center"><?= count($app_for_depart[$key]) ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
        <!--    Статистика по отделам -->


        <!--    Статистика по типам проблем-->
        <div class="col-12">
            <table class="table table-bordered table-sm  table-hover" style="margin-top: 25px">
                <tr class="alert-primary">
                    <td> Тип проблемы</td>
                    <td class="text-center"> Количество</td>
                </tr>
                <?php $sum = 0; ?>
                <?php foreach ($problem as $item) { ?>
                    <?php $pr = new \app\modules\admin\models\Problem(['parent_id' => $item->problem['parent_id']]); ?>
                    <tr class="<?= $class ?>">
                        <td style="padding: 2px"><?= $pr->getParentName() . '/' . $item->problem['name'] ?></td>
                        <td style="padding: 2px; width: 100px;" class="text-center"><?= $item->cnt ?></td>
                    </tr>
                    <?php $sum = $item->cnt + $sum ?>
                <?php } ?>
                <tr style="padding: 2px" class="alert-danger">
                    <td><strong>Всего </strong></td>
                    <td class="text-center"><strong><?= $sum ?>   </strong></td>
                </tr>
            </table>
        </div>
        <!--    Статистика по типам проблем-->
    </div>
    <?php Pjax::end(); ?>








