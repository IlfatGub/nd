<?php

use app\modules\admin\models\App;
use app\modules\admin\models\History;
Use app\modules\admin\models\MyDate;

/**
 * @var $history \app\modules\admin\models\History;
 */


$summ = null;
$id_app = $_GET['id'];
$his = new History(['id_app' => $id_app]);


//$app = App::findOne($id_app);
//if ($app->status == History::STATUS_CLOSE){
//    $date_cl = \app\modules\admin\models\AppContent::findOne(['id_app' => $id_app])->date_cl;
//    $time_work =  $date_cl - $app->date_ct;
//}else{
//    $time_work = strtotime('now') - $app->date_ct;
//}
//$clock_work = $time_work < 60  ? 1 : (int)($time_work / 60);
?>

<div class="jumbotron px-5 py-3">
    <h1 class="h3">История заявки</h1>
<!--    <h1 class="h3">Времени прошло: --><?//=$clock_work?><!-- минут</h1>-->
</div>


<table class="table table-sm fs-10">
    <thead>
    <tr>
        <th>Внес изм.</th>
        <th>Статус</th>
        <th>На ком заявка</th>
        <th>Время на работу</th>
        <th>Услуга</th>
        <th>Время</th>
    </tr>
    </thead>
    <?php foreach ($history as $item): ?>

        <?php

        $date = new MyDate();
        $date->date_to = $item->date;
        $date->date_do = isset($item->date_do) ? $item->date_do : strtotime('now');
        $date->status = $item->id_history;

//        $clock = $date->getTotalTime();
//
//        $clock = $item->id_history != 6 ? $clock : 0;

        $time = in_array($item->id_history, [6]) ? 0 : $date->getsum();
        $time2= in_array($item->id_history, [6]) ? 0 : $date->betweenTime();

        ?>

        <tr>
            <td>
                <small><?= MyDate::getDate($item->date) ?>. <strong><?= $item->user->login ?></strong></small>
            </td>
            <td><?= $item->history->name ?></td>
            <td><?= $item->usercomment->username ?></td>
            <td><?= $item->runtime ?></td>
            <td><?php echo isset($item->problem->name) ? \app\modules\admin\models\Problem::getById($item->id_object)->name.'. '.ucfirst($item->problem->name) : null; ?></td>
<!--            <td>--><?//= MyDate::normalizeTime($clock) ?><!-- </td>-->
            <td><?=  MyDate::normalizeTime($time2)?> </td>
        </tr>
        <?php $summ = $summ + $time2; ?>
    <?php endforeach; ?>
    <?php if ($summ > 0): ?>
        <tr class="alert-">
            <td colspan="5"><strong>Общее затраченное время :</strong></td>
            <td><strong><?= MyDate::normalizeTime($summ) ?></strong></td>
        </tr>
        <tr class="alert-info">
            <td colspan="5">
            <strong>Время выполнения:</strong></td>
            <td><strong><?=  MyDate::normalizeTime($his->getLastRuntime()) ?></strong> </td>
        </tr>
    <?php endif; ?>

</table>

