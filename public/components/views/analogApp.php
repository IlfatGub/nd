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


?>
<?php if ($model->type) : ?>
    <?php if (Sitdesk::chekIdByDv($model->type, $model->id)): ?>
        <div class="col-lg-12 col-md-12 col-xs-12 bg-sitdesk-block block-comment mb-3">
            <span class="badge alert-info col-12">Аналогичная заявка <a
                        href="<?= \yii\helpers\Url::to(['index', 'id' => $id]) ?>">Заявка</a></span>

            <span class="badge alert-light col-12" style="font-size: 10pt">
    <div style="float: left"><?= Sitdesk::Fio(Login::findOne($app->id_user)->username); ?></div>
    <div style="float: right">Статус: <?= Status::Name($app->status) ?></div>
</span>
            <?php if ($comment) { ?>
                <table style="font-size: 8pt" class="table table-sm col-md-12">
                    <?php foreach ($comment as $item) { ?>
                        <tr>
                            <td><?= \app\models\Sitdesk::Fio($item->user->username, 1) ?></td>
                            <td><?= $item->comments->name ?></td>
                            <td><?= \app\modules\admin\models\MyDate::getDate($item->date) ?></td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } ?>
        </div>
    <?php endif ?>
<?php endif ?>