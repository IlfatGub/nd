<?php
/**
 * User: 01gig
 * Блок напоминаний, отображеться у всех сотрудников одного сектора.
 * Добавить и удалить может любой из сектора
 */
use app\models\Sitdesk;
use app\modules\admin\models\MyDate;
use yii\helpers\Html;

?>


<style>
    .remove {
        display: none;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <textarea type="text" id="inpText" rows="1" class="form-control input-sm input app-input-recal" placeholder=""> </textarea>
    </div>
    <div class="col-md-0" style="visibility: hidden; position: fixed">
        <button id="add" class="btn btn-success btn-sm input col-sm-12">Add</button>
    </div>
</div>
<div class="row">
    <div class="col-md-12" id="mylist">
        <?php foreach ($recal as $item) { ?>
            <div class="alert alert-info myRecal myRecNone py-1 px-1 my-1 fs-10" role="alert">
                <a href="#" class="remove" id="<?= $item->id ?>">×</a>
                <?php if($item->type == 0) : ?>
                    <span class="badge badge-info" style="font-size: 9pt" >
                    <small><?= MyDate::getDate($item->date) ?></small>
                    <?= Sitdesk::fio($item->user->username, 1) ?>
                </span>
                <?php endif; ?>
                <?= nl2br($item->text) ?>
            </div>
        <?php } ?>
    </div>
</div>
<!--<pre>-->
<!--    --><?php
//    print_r($recal)
//    ?>
<!--</pre>-->

