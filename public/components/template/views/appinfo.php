<?php
/**
 * Created by PhpStorm.
 * User: 01gig
 * Date: 15.05.2020
 * Time: 15:43
 *
 * @var $model \app\modules\admin\models\App;
 */

use app\modules\admin\models\FioCase;

$domains = $model->appContent->fio->name;
$logs = FioCase::getDomains($domains);
?>


<?php
$class = $text = '';

if ($type == 1){
    $class = 'e8edff';
    $text = "Зарегестрирована новая заявка";
}

if ($type == 2){
    $class = 'BAEEBA';
    $text = "Напоминание по заявке";
}

if ($type == 3){
    $class = 'F0AD4E';
    $text = "Перенапавленная заявка";
}

if ($type == 4){
    $class = '8CD3EC';
    $text = "Изменен статус заявки";
}

if ($type == 5){
    $class = 'F8D7DA';
    $text = "Пользователь оставил комменатрий";
}

if ($type == 6){
    $class = 'F8D7DA';
    $text = "Обращение от пользователя";
}
?>


<style>
    /*table {border: 1px solid #69c;}*/
    table{
        font-size: 11pt;
        min-width: 1000px !important;
        max-width: 1000px;
    }
    td {
        font-weight: normal;
        color: black;
        border-bottom: 1px dotted black;
        border-right: 1px dotted black;
        padding: 3px 6px;

    }
    tr:hover td {background: #<?=$class?>;}

    .top{
        background: #<?=$class?>;
    }
    .t1{
        background: #<?=$class?>;
        color: white;
        text-align: center;
    }

</style>


<table style="max-width: 500px !important;">

    <tr class="top">
        <td colspan="2" class="<?=$class?>"><strong><?= $text ?></strong> </td>
    </tr>

    <tr>
        <td style="width: 150px"><strong>Номер</strong></td>
        <td style="min-width: 250px"><a href='http://newdesk.zsmik.com/index?id=<?=$model->id?>'><?=$model->id?></a></td>
    </tr>

    <tr>
        <td><strong>Приоритет</strong></td>
        <td><?= $model->priority->name ?></td>
    </tr>


    <tr>
        <td><strong>Услуга</strong></td>
        <td><?= $model->problem->name ?></td>
    </tr>

    <tr>
        <td><strong>Подразделение</strong></td>
        <td><?= $model->podr->name ?></td>
    </tr>

    <tr>
        <td><strong>ФИО</strong></td>
        <td><?= $model->appContent->fio->name ?></td>
    </tr>

    <tr>
        <td><strong>Телефон</strong></td>
        <td><?= $model->appContent->phone ?></td>
    </tr>

    <tr>
        <td><strong>IP</strong></td>
        <td><?= $model->appContent->ip?></td>
    </tr>

    <?php if(isset($model->appContent->buh)): ?>
        <tr>
            <td><strong>1c база</strong></td>
            <td><?= \app\modules\admin\models\Buh::findOne($model->appContent->buh)->name?></td>
        </tr>
    <?php endif; ?>

    <tr>
        <td><strong>Примечание</strong></td>
        <td><?= $model->appContent->note?></td>
    </tr>

    <?php if($logs) : ?>
        <tr>
            <td><strong>Logs</strong></td>
            <td><?= $logs->host.' <br> '.$logs->login.'<br>'.$logs->ip ?></td>
        </tr>
    <?php endif;?>

    <tr class="top">
        <td colspan="2" class="<?=$class?>"><strong>Описание</strong></td>
    </tr>
    <tr>
        <td colspan="2"><?= htmlentities($model->appContent->content) ?></td>
    </tr>
</table>


<?php if($comment): ?>
    <br>    <br>    <br>
<table class="table table-sm" style="max-width: 700px;">
    <tr class="">
        <td><strong>Коментарии к заявке</strong></td>
    </tr>
    <?php foreach($comment as $item): ?>
        <tr class="top">
            <td><?= date('H:i | Y-m-d', $item->date).' ' . $item->user->username ?></td>
        </tr>
        <tr>
            <td><?= $item->comments->name ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<?php //die(); ?>
