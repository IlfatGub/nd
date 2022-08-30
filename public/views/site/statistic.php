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

$func = function ($value) {
    return Sitdesk::fio($value, 1);
};

$class = '';


$__status = [100,12,1,5,3,200];
$_total = array_fill_keys($__status, 0);

?>



<?php Pjax::begin(['id' => 'stat', 'enablePushState' => false]); ?>
<?php $count = 0;
$activ = 0;
$pending = 0;
$close = 0;
$dv = 0; ?>


<div class="row" style="font-size: 10pt !important;">

    <div class="col-12 text-center">
        <?= Html::beginForm(['site/stat'], 'post', ['data-pjax' => '', 'class' => 'form-inline']); ?>
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
                'class' => 'col-12',
            ],
            'pluginEvents' => [
                "changeDate" => "function(e) {  $('#refreshButton').click();  }",
            ],
        ]);
        $params = [
            'class' => 'form-control ml-2',
            'prompt' => 'Выберите подразделение...',
            'onchange'=> "function(e) {  $('#refreshButton').click();  ",
            'options' => [
                $org => ['selected' => true]
            ]

        ];

        ?>

        <?= Html::dropDownList('org', 'null', ArrayHelper::map(Podr::getList(), 'id', 'name'), $params); ?>
        <?= Html::submitButton('Выполнить ', ['class' => 'btn btn-primary display-none invisible', 'id' => 'refreshButton', 'name' => 'hash-button']) ?>
        <?= Html::endForm() ?>


    </div>
    <hr>



    <div class="col-12">


        <?php

        //echo "<pre>";
        $_user_app = ArrayHelper::map($app, 'id', 'status', 'user.username');
        $_user_runtime = ArrayHelper::map($app, 'id', 'history.runtime');
        $_user_date_ct = ArrayHelper::map($app, 'id', 'date_ct');
        $_user_date_cl = ArrayHelper::map($app, 'id', 'appContent.date_cl');


        // print_r($_user_date_cl);

        $user_total = $user_status = array();
        foreach ($_user_app as $username => $tickets) {
            if($username){
                $_stat = array();
                $_stat[100] = 0; //общее количество заявок для пользователя
                $_stat[200] = 0; //просроченные заявки


//    echo $username . '-> <br>';
                foreach ($tickets as $id_app => $status) {

//            print_r($_user_runtime[$id_app]);

//          ---------------------------------- Вышитываем Просроченные заявки ----------------------------------
//            $history_time = new History(['id_app' => $id_app]);
//            $end_date = 1;
//
//            if (isset($_user_date_cl[$id_app])){
//                if ($end_date < $_user_date_cl[$id_app]){
//                    $_stat[200] += 1;
//                }
//            }else{
//                if ($end_date < strtotime('now')){
//                    $_stat[200] += 1;
//                }
//            }
//          ---------------------------------- Вышитываем Просроченные заявки ----------------------------------------------------------

                    $_stat[100] += 1;

                    if (array_key_exists($status, $_stat)) {
                        $_stat[$status] += 1;
                    } else {
                        $_stat[$status] = 1;
                    }
//        echo $id_app . '->' . $status . '<br>';
                }
                $user_status[$username] = $_stat;
                $user_total[] = $_stat[100];
//    echo '<br>';
            }
        }
        $user_total[] = 0;

        ?>
        <br>

        <table class="table table-sm table-hover text-center table-bordered">
            <thead>
            <tr class="alert-info">
                <th colspan="7"> Заявки непосредственно у пользователей </th>
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

                    <?php foreach($__status as $st): ?>
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
                <?php foreach($__status as $st): ?>
                    <td><?= array_key_exists($st, $_total) ? $_total[$st] : 0 ?></td>
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

        $app = ArrayHelper::map($history, 'date', 'date_do', 'id_app');
        $app_history = ArrayHelper::map($history, 'id', 'id_history', 'id_app');

        $app_date_to = ArrayHelper::map($history, 'id_history', 'date', 'id');
        $app_date_do = ArrayHelper::map($history, 'id_history', 'date_do', 'id');

        $app_service = ArrayHelper::map($history, 'id_problem', 'id_object', 'id_app');

        $user_app_username = ArrayHelper::map($history, 'id', 'id', 'usercomment.username');

        $user_app_username_2 = ArrayHelper::map($history, 'id_app', 'id_app', 'usercomment.username');

        $username = array_keys($user_app_username);
        $u = array_map($func, $username);

        foreach ($username as $item) {
            $count_app[] = count($user_app_username_2[$item]);
        }
        $count_app[]=0;

        $p = array();

        $app_info = array();
        $app_count = 0;
        $time = array();

        foreach ($user_app_username as $username => $apps) {
            $time = array();
            foreach ($apps as $a) {
                $app_count++;

                foreach ($app_date_to[$a] as $id_his_to => $date_to) {

                    $date = new MyDate();
                    $date->date_to = $date_to;
                    $date->date_do = isset($app_date_do[$a][$id_his_to]) ? $app_date_do[$a][$id_his_to] : strtotime('now');

//            $_time = in_array($id_his_to, [6]) ? 0 : $date->getsum();
                    $_time = in_array($id_his_to, [6]) ? 0 : $date->betweenTime();
                    if (array_key_exists($id_his_to, $time)) {
                        $time[$id_his_to] = $time[$id_his_to] + $_time;
                    } else {
                        $time[$id_his_to] = $_time;
                    }
                    $_time = 0;
                }
                $app_info[$username]['time'] = $time;

            }

            $app_info[$username]['count'] = count($user_app_username_2[$username]);
            $app_count = 0;

        }

        //echo "<pre>"; print_r($history);

        ?>



        <table class="table table-sm table-hover text-center table-bordered">
            <thead>
            <tr class="alert-info">
                <th colspan="6"> Потраченное время на все заявки, где участвовал пользователь </th>
            </tr>
            <tr>
                <th class="text-left">ФИО</th>
                <th>Заявок</th>
                <th>На рассмотрении</th>
                <th>В работе</th>
                <th>Отложено</th>
            </tr>
            </thead>
            <?php foreach ($app_info as $username => $status): ?>
                <?php $class = $username == Yii::$app->user->identity->username ? 'alert-warning' : ''; ?>
                <?php $new = 0;
                $look = 0;
                $work = 0;
                $aside = 0; ?>
                <?php foreach ($status['time'] as $stat => $item): ?>
                    <?php
                    if ($stat == 11) {
                        $new = MyDate::normalizeTime($item);
                    } elseif ($stat == 12) {
                        $look = MyDate::normalizeTime($item);
                    } elseif ($stat == 4) {
                        $work = MyDate::normalizeTime($item);
                    } elseif ($stat == 5) {
                        $aside = MyDate::normalizeTime($item);
                    }
                    ?>
                <?php endforeach; ?>

                <tr class="<?= $class?>">
                    <td class="text-left"><?= $username ?></td>
                    <td><?= count($user_app_username_2[$username]) ?></td>
                    <td><?= $look ?></td>
                    <td><?= $work ?></td>
                    <td><?= $aside ?></td>
                </tr>

                <?php $new = 0;
                $look = 0;
                $work = 0;
                $aside = 1; ?>

            <?php endforeach; ?>
        </table>

        <div >
            <?php


            if (isset($count_app)) {
                $dataWeatherTwo = [
                    'labels' => $u,
                    'datasets' => [
                        [
                            'data' => $count_app,
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
        </div>

        <?php // print_r($u);  ?>
        <?php // print_r($app_info);  ?>



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








