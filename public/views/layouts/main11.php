<?php
//session_start();

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
    <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE"/>
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



<nav class="navbar navbar-expand-lg navbar-light " style="background-color: #e3f2fd;">
    <a class="navbar-brand" href="#">Navbar</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Link</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Dropdown
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">Something else here</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" href="#">Disabled</a>
            </li>
        </ul>
        <form class="form-inline my-2 my-lg-0">
            <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
    </div>
</nav>

<div id="wrapper">

    <!-- Sidebar -->
    <div id="sidebar-wrapper">

        <ul class="sidebar-nav">
                        <?php if (Yii::$app->user->identity) { ?>
                            <div class="col-lg-12  col-xs-12 text-center"  style="display: inline-block; background: #E4E4E4; font-size: 14pt; margin-bottom: 5px">
                                <li class="dropdown"    style="display: inline-block; background: #E4E4E4; font-size: 14pt; margin-bottom: 5px">
                                    <a id="drop1" href="#" role="button" class="dropdown-toggle"
                                       data-toggle="dropdown"><?= Yii::$app->user->identity->username ?><b class="caret"></b></a>
                                    <ul class="dropdown-menu" style="font-size: 10pt; left: -10px">
                                        <li role="presentation"><a href="<?= Url::to(['index', 'search' => 'Все']) ?>">Все</a></li>
                                        <li role="presentation" class="divider"></li>
                                        <?php foreach (Login::find()->where(['visible' => 0])->select(['username'])->all() as $item) { ?>
                                            <li role="presentation"><a href="<?= Url::to(['index', 'search' => $item->username]) ?>"><?= $item->username ?></a></li>
                                        <?php } ?>
                                    </ul>
                                </li>
                            </div>

                            <li class="app_menu" style="margin: 10px">
                                <div style="display: inline-block;"> <?= Html::a('<span title="Настройки" class=" btn btn-lg btn-outline-primary glyphicon glyphicon-cog"    ></span>', ['settings']) ?> </div>
                                <div style="display: inline-block;"> <?= Html::a('<span title="Домой" class="     btn btn-lg btn-outline-primary glyphicon glyphicon-home" ></span>', ['/site/index']) ?> </div>
                                <div style="display: inline-block;"> <?= Html::a('<span title="Выйти" class="     btn btn-lg btn-outline-primary glyphicon glyphicon-log-out"></span>', ['/site/logout']) ?> </div>
                                <?= Html::button('', ['value' => Url::to(['site/stat']), 'class' => 'btn btn-lg btn-outline-primary glyphicon glyphicon-time modalButton', 'title' => 'Статистика',]) ?>
                                <?php if (Yii::$app->user->can('Disp')) { ?>
                                    <?= Html::button('', ['value' => Url::to(['site/close']), 'class' => 'btn btn-lg btn-outline-primary glyphicon glyphicon-envelope modalButton', 'title' => 'Закрытые заявки']) ?>
                                <?php } ?>
                            </li>

                            <li class="app_menu_mini" style="margin: 10px; width: 100%">
                                <div class="btn-group">
                                    <button class="btn btn-info btn-lg col-xs-12 dropdown-toggle" type="button"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        -----Меню-----
                                    </button>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><?= Html::a('Настройки', ['settings']) ?></li>
                                        <li><?= Html::a('Домой', ['/site/index']) ?></li>
                                        <li><?= Html::a('Выйти', ['/site/logoutuser']) ?></li>
                                    </ul>
                                </div>
                            </li>
                            <li>
                                <div class="col-lg-10 col-md-10 col-xs-10">
                                    <?php ActiveForm::begin(['action' => ['/site/index'], 'method' => 'get']); ?>

                                    <div class="input-group">
                                        <?= Html::input('search', 'search', '', [
                                            'class' => 'form-control search-width input-sm ',
                                            'id' => 'search',
                                        ]) ?>
                                        <span class="input-group-btn">
                                            <button class="btn btn-secondary" type="button">Go!</button>
                                        </span>
                                    </div>
                                    <?php ActiveForm::end(); ?>

                                </div>
                            </li>
                        <?php } ?>

            <hr>
            <?php if (Yii::$app->user->identity and Yii::$app->user->can('User')) { ?>
                <?= AppWidget::widget(['search' => isset($_GET['search']) ? $_GET['search'] : null]) ?>
            <?php } ?>

        </ul>
    </div>

    <div id="page-content-wrapper" class="col-xl-12" style="padding: 0; margin: 15px 0 0 0;">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12" style="margin: 0; padding: 0">
                    <?= $content ?>
                </div>
            </div>
        </div>
    </div>

</div>

<?php $this->endBody() ?>
</body>

<script>
    $("#menu-toggle").click(function (e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
</script>
</html>
<?php $this->endPage() ?>
