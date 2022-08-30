<?php
    //session_start();

    use app\components\AssistsWidget;
    use app\components\RecalWidget;
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


    $absent = Yii::$app->user->identity->absent; // Отствутю / на месте
    $user_id = Yii::$app->user->id; // ID
	 echo "<br>";
	 echo "<br>";
	 echo "<br>";
	 echo "<br>";
	 echo $_SERVER['REMOTE_ADDR'];	
	 echo "<br>";
	 echo $_SERVER['HTTP_X_FORWARDED_FOR'];	
	 echo "<br>";
	 echo $_SERVER['HTTP_CLIENT_IP'];
	 echo $_SERVER['HTTP_X_Real_Ip'];
	 echo "<br>";
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

<style>
    .navbar {
        /*max-height: 50px !important;*/
        z-index: 100;
    }

    .dropdown-menu {
        z-index: 1000;
    }

</style>
<?php if (isset(Yii::$app->user->identity)) { ?>

    <nav class="navbar fixed-top navbar-expand-lg navbar-light <?= $absent == 1 ? 'alert-danger' : 'alert-info' ?> text-white py-0 "
         id="hd-nav">

        <a class="navbar-brand text-center logo-width" href="<?= Url::home() ?>"><strong> Sitdesk </strong></a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse ml-4 " id="navbarSupportedContent">

            <!--        ФИО залогинившегося пользователя. Список Пользователей-->
            <ul class="navbar-nav mr-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle ml-2 pl-2  " href="#" id="navbarDropdown" role="button"
                       data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">
                        <?= Sitdesk::fio(Yii::$app->user->identity->username) ?>
                    </a>


                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a href="<?= Url::to(['index', 'search' => 'Все']) ?>"
                           class="dropdown-item alert-warning"><strong>Все</strong></a>
                        <a href="<?= Url::to(['index', 'search' => 'sit']) ?>" class="dropdown-item alert-info"><strong>сектор
                                СИТ</strong></a>
                        <a href="<?= Url::to(['index', 'search' => 'sap']) ?>" class="dropdown-item alert-info"><strong>сектор
                                КИС</strong></a>
                        <a href="<?= Url::to(['index', 'search' => 'ss']) ?>" class="dropdown-item alert-info"><strong>сектор
                                Связи</strong></a>
                        <?php foreach (Login::getLoginList() as $item) { ?>
                            <a href="<?= Url::to(['index', 'search' => $item->username]) ?>"
                               class="dropdown-item"><?= $item->username ?></a>
                        <?php } ?>
                    </div>
                </li>

                <!--        Кнопка отсутствую       -->
                <li class="nav-item ">
                    <div class="side-button-1-wr">
                        <label class="side-button-1 mb-0 m-1">
                            <div class="side-b side-open"><span id="hd-absent"
                                                                class="btn btn-sm <?= $absent == 1 ? 'btn-outline-success' : 'btn-outline-danger' ?> side-panel-btn px-5"> <?= $absent == 1 ? 'На месте' : 'Отсутствую' ?> </span>
                            </div>
                        </label>
                    </div>
                </li>
                <!--        Кнопка отсутствую       -->

            </ul>


            <!--        ФИО залогинившегося пользователя. Список Пользователей-->
            <ul class="navbar-nav">
                <?php if (Yii::$app->user->can('SuperAdmin')) : ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle ml-2 pl-2 ml-lg-2 pl-lg-2" href="#" id="navbarDropdown"
                           role="button" data-toggle="dropdown"
                           aria-haspopup="true" aria-expanded="false" title="Дополнительные опции">
                            Доп. опции
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <?= Html::button('Услуги', ['value' => Url::to(['adm/problem']), 'class' => 'dropdown-item btn btn-info btn-md modalButton dropdownButton',]) ?>
                            <?= Html::button('Услуги(1с)', ['value' => Url::to(['adm/service']), 'class' => 'dropdown-item btn btn-info btn-md modalButton dropdownButton',]) ?>
                            <?= Html::a('Разрезы Услуги(1с)', Url::to(['adm/service2']), ['class' => 'dropdown-item btn btn-info  dropdownButton']); ?>
                            <?= Html::button('Подразделения', ['value' => Url::to(['adm/podr']), 'class' => 'dropdown-item btn btn-info  modalButton dropdownButton',]) ?>
                            <?= Html::button('Пользователи', ['value' => Url::to(['adm/login']), 'class' => 'dropdown-item btn btn-info  modalButton dropdownButton',]) ?>
                            <?= Html::button('Системы 1С', ['value' => Url::to(['adm/buh']), 'class' => 'dropdown-item btn btn-info  modalButton dropdownButton',]) ?>
                            <?= Html::a('Аутентификация', Url::to(['rbac/role']), ['class' => 'dropdown-item btn btn-info  dropdownButton']); ?>
                            <?= Html::a('Карта организации', Url::to(['adm/sitmap']), ['class' => 'dropdown-item btn btn-info  dropdownButton']); ?>
                        </div>
                    </li>
                <?php endif ?>
                <?php if (Yii::$app->user->can('Disp')) { ?>
                    <?= Html::a('Закрытые заявки', Url::to(['/site/close']), ['class' => 'nav-link btn btn-danger text-white', 'title' => 'Закрытые заявки']) ?>
                <?php } ?>
            </ul>

            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle ml-2 pl-2 ml-lg-2 pl-lg-2" href="#" id="navbarDropdown"
                       role="button" data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false" title="Дополнительные опции">
                        Отчет
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <?= Html::button('Отчет', ['value' => Url::to(['/site/report']), 'class' => 'dropdown-item btn btn-info btn-md modalButton dropdownButton',]) ?>
                        <?= Html::a('Выгрузка', Url::to(['/site/export']), ['class' => 'dropdown-item btn btn-info  dropdownButton']); ?>
                    </div>
                </li>
            </ul>

            <ul class="navbar-nav mr-5">
                <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle ml-2 pl-2 ml-lg-2 pl-lg-2" href="#" id="navbarDropdown"
                           role="button" data-toggle="dropdown"
                           aria-haspopup="true" aria-expanded="false" title="Дополнительные опции">
                            Меню
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <?= Html::button('Услуги', ['value' => Url::to(['adm/problem']), 'class' => 'dropdown-item btn btn-info btn-md modalButton dropdownButton',]) ?>
                            <?= Html::button('Услуги(1с)', ['value' => Url::to(['adm/service']), 'class' => 'dropdown-item btn btn-info btn-md modalButton dropdownButton',]) ?>
                            <?= Html::a('Разрезы Услуги(1с)', Url::to(['adm/service2']), ['class' => 'dropdown-item btn btn-info  dropdownButton']); ?>
                            <?= Html::a('<span class="nav-link text-muted">Системы 1С</span>', ['adm/buh'], ['title' => 'Системы 1с']) ?>


                            <?= Html::a('<span class="nav-link text-muted">Настройки</span>', ['/site/settings'], ['title' => 'Настройки сайта']) ?>
                            <?= Html::a('<span class="nav-link text-muted">Карта</span>', ['adm/sitmap'], ['title' => 'Карта']) ?>
                            <?= Html::a('<span class="nav-link text-muted">Уведомления</span>', ['site/notify'], ['title' => 'Уведолмения']) ?>
                            <?= Html::a('<span class="nav-link text-muted">Проблема с Excel</span>', ['site/error-excel'], ['title' => 'Проблема с Excel']) ?>
                            <?= Html::a('<span class="nav-link text-muted">Перевод домена</span>', ['site/domain-translation'], ['title' => 'Уведолмения']) ?>
                            <?php if (Yii::$app->user->can('Disp') or Yii::$app->user->can('SuperAdmin')) : ?>
                                <?= Html::button('Помощь диспетчеру', ['value' => Url::to(['adm/help']), 'class' => 'nav-link btn btn-link text-muted  modalButton',]) ?>
                            <?php endif ?>
                            <?= Html::button('Статистика', ['value' => Url::to(['/site/stat']), 'class' => 'nav-link btn btn-link text-muted  modalButton', 'title' => 'Статистика']) ?>
                            <?= Html::a('<span class="nav-link text-muted ">Выйти</span>', ['/site/logout'], ['title' => 'Выйти']) ?>
                            <?= Html::a('AD', Url::to(['adm/ldap']), ['class' => 'dropdown-item btn btn-info  dropdownButton']); ?>
                            <?php if (Yii::$app->user->can('SuperAdmin')) : ?>
                                <?= Html::a('Создание учеток', Url::to(['adm/adduser']), ['class' => 'dropdown-item btn btn-info  dropdownButton']); ?>
                            <?php endif ?>

                        </div>
                </li>
            </ul>


            <ul class="navbar-nav">
                <li class="nav-item ">
                    <div class="side-button-1-wr">
                        <label class="side-button-1 mb-0 m-1 hd-side" for="side-checkbox" id="info">
                            <div class="side-b side-open"><span
                                        class="btn btn-outline-secondary side-panel-btn fa fa-info"></span></div>
                        </label>
                    </div>
                </li>
                <li class="nav-item ">
                    <div class="side-button-1-wr">
                        <label class="side-button-1 mb-0 m-1 hd-side" for="side-checkbox" id="phone">
                            <div class="side-b side-open"><span
                                        class="btn btn-outline-secondary side-panel-btn fa fa-phone"></span></div>
                        </label>
                    </div>
                </li>
            </ul>

        </div>
    </nav>

<?php } ?>

<div class="mt-4 pt-3">
        <div id="wrapper">
        <!-- Sidebar Меню с заявками -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">

                <li class="alert- text-dark text-center mb-3" style="margin:0 25px 0 10px">
                    <?php ActiveForm::begin(['action' => ['/site/index'], 'method' => 'get', 'options' => ['class' => 'form-inline ']]); ?>
                    <?= Html::input('search', 'search', '', [
                        'class' => 'form-control form-control-sm  col-10 col-xs-8',
                        'placeholder' => 'Поиск',
                        'id' => 'search',
                    ]) ?>
                    <button class="btn  btn-success col-2  col-xs-4 fas fa-search" type="submit"></button>
                    <?php ActiveForm::end(); ?>
                </li>

                <?php if (Yii::$app->user->identity) { ?>

                    <?php $serach = isset($_GET['search']) ? $_GET['search'] : null ?>

                    <!-- Заявки на согласовании -->
                    <?php if (Yii::$app->user->id == 1 or Yii::$app->user->id == 4 or Yii::$app->user->id == 35 or Yii::$app->user->id == 60 or Yii::$app->user->id == 62): ?>
                        <?= \app\components\TicketMenu::widget(['status' => 14, 'search' => $serach, 'name' => 'На согласовании', 'user' => 1]) ?>
                    <?php endif; ?>
                    <!-- Заявки на согласовании -->

                    <!-- Проекты -->
                    <?php if (Yii::$app->user->can('AdminProject11')): ?>
                        <?= \app\components\TicketMenu::widget(['status' => 14, 'search' => $serach, 'name' => 'Проекты', 'user' => 1, 'project' => 1]) ?>
                    <?php endif; ?>
                    <!-- Проекты -->

                    <?php if (in_array(Yii::$app->user->id, [1, 2, 35, 52, 20, 34, 42, 49, 48,61, 4, 40, 62, 3, 60, 56, 5, 70])): ?>
                        <?= \app\components\TicketMenu::widget(['status' => 11, 'search' => $serach, 'name' => 'Обращения пользователя', 'user' => 1]) ?>
                        <?= \app\components\TicketMenu::widget(['status' => 12, 'search' => $serach, 'name' => 'На рассмотрении']) ?>
                    <?php else: ?>
                        <?= \app\components\TicketMenu::widget(['status' => 12, 'search' => $serach, 'name' => 'На рассмотрении']) ?>
                    <?php endif; ?>

                    <?= \app\components\TicketMenu::widget(['status' => 1, 'search' => $serach, 'name' => 'В работе']) ?>
                    <?= \app\components\TicketMenu::widget(['status' => 2, 'search' => $serach, 'name' => 'В ожидании']) ?>
                    <?= \app\components\TicketMenu::widget(['status' => 3, 'search' => $serach, 'name' => 'Закрытые']) ?>

                    <li class="alert-primary text-dark text-center mt-3 status-ticket"  data-id="3" style="margin:0 25px 0 10px">
                        <a href="#">Закрытые</a>
                    </li>
                    <div id="status-ticket-3"></div>





                <?php } ?>
            </ul>
        </div>

        <div id="page-content-wrapper" class="col-xl-12" style="padding: 0; margin: 15px 0 0 0; height: 100%">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xl-12" style="margin: 0; padding: 0">
                        <?= $content ?>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>


<input type="checkbox" id="side-checkbox"/>
<div class="side-panel">
    <label class="side-button-2" for="side-checkbox">+</label>
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
                <a target="_blank" href="http://logs.snhrs.ru" class="btn btn-outline-secondary btn-sm mb-1 col-12">
                    Logs </a>
                <a target="_blank" href="http://sit.snhrs.ru/knowledge"
                   class="btn btn-outline-secondary btn-sm mb-1 col-12"> База знаний </a>
                <a target="_blank" href="http://tel.snhrs.ru" class="btn btn-outline-secondary btn-sm mb-1 col-12">
                    Телефоный справочник </a>
                <a target="_blank" href="http://support.zsmik.com" class="btn btn-outline-secondary btn-sm mb-1 col-12">
                    Техническая поддержка ОБИТиС </a>
                <a target="_blank" href="http://sit.snhrs.ru/netprint"
                   class="btn btn-outline-secondary btn-sm mb-1 col-12"> NetPrint </a>
                <a target="_blank" href="http://sit.snhrs.ru/pcname"
                   class="btn btn-outline-secondary btn-sm mb-1 col-12"> PcName </a>
                <a target="_blank" href="http://snhrsnet.snhrs.ru/index.php/?org_id=16"
                   class="btn btn-outline-secondary btn-sm mb-1 col-12"> SnhrsNet </a>
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
</div>


<div class="side-panel">

</div>


<div class="alert" id="show-message" role="alert">

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


