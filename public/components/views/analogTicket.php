<?php
/**
 * Created by PhpStorm.
 * User: 01gig
 * Date: 21.11.2018
 * Time: 15:20
 */

use app\modules\admin\models\Status;
use app\modules\admin\models\Login;
use app\models\Sitdesk;

//echo "<pre>";
//    print_r($app);
//echo "</pre>";


?>
<?php if (!isset($_GET['app'])): ?>

<div class="col-lg-12 col-md-12 col-xs-12 bg-sitdesk-block block-comment mb-3">
<?php foreach ($model as $item): ?>
    <?php if ($item->id != $_GET['id']): ?>
        <span class="badge alert-info col-12">Аналогичная заявка <a
                    href="<?= \yii\helpers\Url::to(['index', 'id' => $item->id]) ?>"><?= $item->id ?></a></span>

        <span class="badge alert-light col-12" style="font-size: 10pt">
    <div style="float: left"><?= Sitdesk::Fio(Login::findOne($item->id_user)->username); ?></div>
    <div style="float: right">Статус: <?= Status::Name($item->status) ?></div>
</span>
        <?php if ($comment) { ?>
            <table style="font-size: 8pt" class="table table-sm col-md-12">
                <?php foreach ($comment as $c) { ?>
                    <?php if ($c->id_app == $item->id): ?>
                        <tr>
                            <td><?= \app\modules\admin\models\MyDate::getDate($c->date) ?></td>
                            <td><?= \app\models\Sitdesk::Fio($c->user->username, 1) ?></td>
                            <td><?= $c->comments->name ?></td>
                        </tr>
                    <?php endif; ?>
                <?php } ?>
            </table>
        <?php } ?>
    <?php endif; ?>
<?php endforeach; ?>
</div>
<?php endif; ?>
