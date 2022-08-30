<?php
//session_start();

use app\models\Sitdesk;
use app\modules\admin\models\MyDate;
use app\modules\admin\models\Status;
use yii\helpers\Html;
use app\assets\AppAsset;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\components\AppWidget;
use kartik\typeahead\TypeaheadBasic;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use app\modules\admin\models\Login;

AppAsset::register($this);

//Yii::$app->session->open();
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<!--<meta http-equiv="X-UA-Compatible" content="IE=edge" />-->
<head>
    <meta charset="UTF-8"/>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=EDGE"/>
    <meta http-equiv="X-UA-Compatible" content="IE=7;"/>
    <meta http-equiv="X-UA-Compatible" content="IE=8;"/>
    <meta http-equiv="X-UA-Compatible" content="IE=9;"/>
    <meta http-equiv="X-UA-Compatible" content="IE=10;"/>
    <link rel="shortcut icon" href="<?= Yii::$app->request->baseUrl ?>/image/icon.jpeg" type="image/x-icon"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title> <?= Yii::$app->name ?> </title>
    <?php $this->head() ?>
    <?php header("Content-type: text/html; charset=utf-8");
    mb_internal_encoding('UTF-8'); ?>
</head>

<body>
<?php $this->beginBody() ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12" style="margin: 0; padding: 0">
            <?= $content ?>
        </div>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>


