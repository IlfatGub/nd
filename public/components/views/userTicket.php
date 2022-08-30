<?php
    /**
     * Created by PhpStorm.
     * User: 01gig
     * Date: 13.04.2019
     * Time: 12:16
     */

    use yii\helpers\Html;


?>
<?php if (Yii::$app->user->identity->id == 1): ?>
    <?php if (Yii::$app->user->identity->settings_userticket == 1): ?>
        <?php if ($model): ?>
            <div id="sitdesk-upd-app-active">
                <div style="border: 1px dashed silver;   padding: 0 3px; max-width: 550px"
                     class=" alert-primar1y mt-2 bg-sitdesk-block ">
                    <!--    <div class=" alert-primar1y mt-2 bg-sitdesk-block ">-->

                    <table class="table table-sm table-bordered  fs-8 mb-1 mt-1">
                        <tr>
                            <td class="alert-primary">
                                Заявки от пользоавтеля
                                <div class="float-right"><a
                                            href="<?= \yii\helpers\Url::to(['settings', 't' => 10]) ?>">Settings</a>
                                </div>
                            </td>
                        </tr>


                    </table>
                    <?php foreach ($model as $item): ?>
                        <table class="table table-sm table-bordered  fs-8 mb-1 mt-2">
                            <tr>
                                <td class="alert-secondary" style="width: 10%; color: black">
                                    <?= Html::a($item->id, ['index', 'id' => $item->id]) ?>
                                </td>
                                <td class="alert-secondary" style="width: 20%; color: black">
                                    <?= date('Y-m-d h:i:s', $item->date_ct) ?>
                                </td>
                                <td class="alert-secondary" style="width: 20%; color: black">
                                    <?= \app\models\Sitdesk::fio($item->user->username, 1) ?>
                                </td>
                                <td class="alert-secondary" style="width: 25%; color: black">
                                    <?= $item->appContent->ip ?>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="4" style="background-color: #fff;">
                                    <?= $item->appContent->content ?>
                                </td>
                            </tr>
                        </table>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>
