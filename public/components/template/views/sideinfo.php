<?php
use app\components\RecalWidget;
use app\models\Sitdesk;
use app\components\AssistsWidget;

?>

<label class="side-button-2" for="side-checkbox1">+</label>
<!--            Заявки коллег   -->
<?php if (Yii::$app->user->identity->assist and (\app\models\Sitdesk::countAssist() > 0) or (Yii::$app->user->identity->settings_recal == 1) or (Yii::$app->user->identity->settings_url == 1)) : ?>

    <?php if (Yii::$app->user->identity->assist and (\app\models\Sitdesk::countAssist() > 0)) { ?>
        <div class="block-assist bg-sitdesk-block vertical-top">
            <?= AssistsWidget::widget() ?>
        </div>
    <?php } ?>
    <!--            Заявки коллег   -->

    <!--            Ссылки на наши ресурсы -->
    <?php if (Yii::$app->user->identity->settings_url == 1) { ?>
        <div class="block-recal bg-sitdesk-block vertical-top mb-2">
            <button type="button" class="btn btn-info btn-sm mb-1 col-12">Ссылки на наши ресурсы</button>
            <a  target="_blank" href="http://logs.snhrs.ru" class="btn btn-outline-secondary btn-sm mb-1 col-12"> Logs </a>
            <a  target="_blank" href="http://sit.snhrs.ru/knowledge" class="btn btn-outline-secondary btn-sm mb-1 col-12"> База знаний </a>
            <a  target="_blank" href="http://tel.snhrs.ru" class="btn btn-outline-secondary btn-sm mb-1 col-12"> Телефоный справочник </a>
            <a  target="_blank" href="http://support.zsmik.com" class="btn btn-outline-secondary btn-sm mb-1 col-12"> Техническая поддержка ОБИТиС </a>
            <a  target="_blank" href="http://sit.snhrs.ru/netprint" class="btn btn-outline-secondary btn-sm mb-1 col-12"> NetPrint </a>
            <a  target="_blank" href="http://sit.snhrs.ru/pcname" class="btn btn-outline-secondary btn-sm mb-1 col-12"> PcName </a>
            <a  target="_blank" href="http://snhrsnet.snhrs.ru/index.php/?org_id=16" class="btn btn-outline-secondary btn-sm mb-1 col-12"> SnhrsNet </a>
        </div>
    <?php } ?>
    <!--            Ссылки на наши ресурсы -->

    <!--            Напоминания-->
    <?php if (Yii::$app->user->identity->settings_recal == 1) { ?>
        <div class="block-recal bg-sitdesk-block vertical-top">
            <?= RecalWidget::widget() ?>
        </div>
    <?php } ?>
    <!--            Напоминания-->
<?php endif; ?>