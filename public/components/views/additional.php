<?php
    /**
     * Created by PhpStorm.
     * User: 01gig
     * Date: 13.04.2019
     * Time: 12:16
     */

    use app\models\Additional;
    use app\modules\admin\models\App;
    use app\modules\admin\models\AppFiles;
    use yii\helpers\Html;
    use yii\helpers\Url;

    $additional = Additional::getAdditionalListByUser();
    $additional = App::getActiveApp();


?>
<?php if (Yii::$app->user->identity->settings_active == 1): ?>

    <?php if ($additional): ?>
        <div id="sitdesk-upd-app-active">
            <div style="border: 1px dashed silver;   padding: 0 3px; max-width: 550px" class=" alert-primar1y ">
                <?php foreach ($additional as $item) {
                    $app = \app\modules\admin\models\AppContent::getContentByApp($item['id']);
                    $conetnt = $app[0]['content'];
                    $conetnt = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $conetnt);
                    $conetnt = str_replace(array("\r\n\r\n", "\r\n\r\n\r\n"), "\r\n", $conetnt);
                    $conetnt = str_replace(array('  ', '    ', '    '), "", $conetnt);
                    ?>

                    <?php
                    $row = 1;
                    if (strlen($conetnt) > 200) $row = 2;
                    if (strlen($conetnt) > 300) $row = 3;
                    if (strlen($conetnt) > 400) $row = 5;
                    if (strlen($conetnt) > 500) $row = 6;
                    if (strlen($conetnt) > 600) $row = 7;
                    if (strlen($conetnt) > 700) $row = 8;
//    if (strlen($conetnt) > 800)         $row = 11;
//    if (strlen($conetnt) > 900)         $row = 12;
//    if (strlen($conetnt) > 1000)        $row = 13;
//    if (strlen($conetnt) > 1100)        $row = 14;

                    ?>

                    <table class="table table-sm table-bordered  fs-8 mb-1 mt-1">
                        <tr>
                            <td class="alert-info" style="width: 10%">
                                <?= Html::a($item['id'], ['index', 'id' => $item['id']]) ?>
                            </td>
                            <td class="alert-info" style="width: 40%">
                            <span class="btn btn-sm sitdesk-btncopy fas fa-copy myCopyf p-0 m-0" title="Копировать"
                                  data-clipboard-text="<?= $app[0]['fio']['name'] ?>"></span>
                                <?= $app[0]['fio']['name'] ?>
                            </td>
                            <td class="alert-info" style="width: 25%">
                            <span class="btn btn-sm sitdesk-btncopy fas fa-copy myCopyf p-0 m-0" title="Копировать"
                                  data-clipboard-text="<?= $app[0]['ip'] ?>"></span>
                                <?= $app[0]['ip'] ?>
                            </td>
                            <td class="alert-info" style="width: 25%">
                                <?= $app[0]['phone'] ?>
                                <?= Html::a('<span class="glyphicon  fas fa-ban float-right"></span>', [Url::toRoute(['admin/comment/add', 'text' => 'Выполнено', 'id' => $item['id']])], ['title' => 'Выполнено']) ?>
                            </td>
                        </tr>
                        <?php if ($app[0]['dv']): ?>
                            <tr class="alert-warning">
                                <td colspan="4"><?= $app[0]['dv'] ?> </td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td colspan="4" class="bg-sitdesk-block">
                            <textarea rows="<?= $row ?>" style="width: 100%; border: 0px"
                                      class="bg-sitdesk-block"><?= $conetnt ?></textarea>
                                <!--                    <textarea rows="-->
                                <? //=$row?><!--"  style="width: 100%; border: 0px" class="bg-sitdesk-block">-->
                                <? //=preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $conetnt)?><!--</textarea>-->
                            </td>
                        </tr>
                        <?php if (AppFiles::existsFileByApp($item['id'])): ?>
                            <tr>
                                <td colspan="5"><?= \app\components\DocumentWidget::widget(['id_app' => $item['id'], 'type' => 1]) ?></td>
                            </tr>
                        <?php endif; ?>
                    </table>
                <?php } ?>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

