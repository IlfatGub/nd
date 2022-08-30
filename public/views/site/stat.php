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


$__status = [100,12,1,5,3,200,300,400];
$_total = array_fill_keys($__status, 0);


$app_list = $app;
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
                'prompt' => 'Выберите сотрудника...',
                'onchange'=> "function(e) {  $('#refreshButton').click();  ",
                'options' => [
                    $org => ['selected' => true]
                ]

        ];

        ?>

        <?= Html::dropDownList('id_user', 'null', ArrayHelper::map(Podr::getList(), 'id', 'name'), $params); ?>
        <?= Html::dropDownList('id_user', 'null', \app\modules\admin\models\Login::getList(), $params); ?>
        <?= Html::submitButton('Выполнить ', ['class' => 'btn btn-primary display-none invisible', 'id' => 'refreshButton', 'name' => 'hash-button']) ?>
        <?= Html::endForm() ?>


    </div>
<hr>



<div class="col-12">


<!--    --><?php //echo "<pre>"; print_r($app); die(); ?>

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
        $_stat[100] = 0; // общее количество заявок для пользователя
        $_stat[200] = 0; // просроченные заявки
        $_stat[300] = 0; // KPI_1   Заявки, закрытые в рамках установленных сроков за период.
        $_stat[400] = 0; // KPI_2   Количество заявок, принятые в работу специалистом в рамках установленного срока за период.
        $ticketNoWork = 0; // количество заявок не принятых в работу в рамках установленных сроков

        foreach ($tickets as $id_app => $status) {

//          ---------------------------------- Высчитываем Просроченные заявки ----------------------------------
            $history_time = new History(['id_app' => $id_app]);
            $end_date = $history_time->endDate(); //конечная дата выполнения по регламенту

            $ticketNoWork += $history_time->getTimeFromConsToWork(); //Добавляем если заявка не принятра во время в работу

            if (isset($_user_date_cl[$id_app])){
                if ($end_date < $_user_date_cl[$id_app]){
                    $_stat[200] += 1;
                }
            }else{
                if ($end_date < strtotime('now')){
                    $_stat[200] += 1;
                }
            }
//          ---------------------------------- Высчитываем Просроченные заявки -------------------------------------

            $_stat[100] += 1;

            if (array_key_exists($status, $_stat)) {
                $_stat[$status] += 1;
            } else {
                $_stat[$status] = 1;
            }

        }

        $_stat[300] = $history_time->kpi($_stat[100], $_stat[200]); // КПИ Процент заявок, выполненных в рамках установленных сроков согласно регламенту
        $_stat[400] = $history_time->kpi($_stat[100], $ticketNoWork); // КПИ Процент заявок, принятых в работу в рамках установленных сроков согласно регламенту

        $user_status[$username] = $_stat;
        $user_total[] = $_stat[100]; // Всего
    }
}

$user_total[] = 0;

?>
    <br>

<table class="table table-sm table-hover text-center table-bordered">
    <thead>
    <tr class="alert-info">
        <th colspan="9"> Заявки непосредственно у пользователей </th>
    </tr>
    <tr>
        <th class="text-left">ФИО</th>
        <th>всего</th>
        <th>На рассмотрении</th>
        <th>В работе</th>
        <th>Отложено</th>
        <th>Закрыто</th>
        <th>Просрочено</th>
        <th>KPI 1, %</th>
        <th>KPI 2, %</th>
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











