<?php

    namespace app\controllers;

    //if(isset($_SESSION)){
//    session_start();
//}

    use app\components\AppViewWidget;
    use app\components\DocumentWidget;
    use app\components\FioCaseWidget;
    use app\components\LogsWidget;
    use app\components\PhoneSearchWidget;
    use app\components\PhoneWidget;
    use app\components\template\SideInfoTmp;
    use app\components\template\StatusTable;
    use app\models\About;
    use app\models\AppNotify;
    use app\models\AppProject;
    use app\modules\admin\models\AppSearch;
    use app\models\Depart;
    use app\models\Sitdesk;
    use app\models\Storage;
    use app\models\Temp;
    use app\modules\admin\models\AppAnalog;
    use app\modules\admin\models\AppComment;
    use app\modules\admin\models\AppContent;
    use app\modules\admin\models\AppFiles;
    use app\modules\admin\models\AppRemind;
    use app\modules\admin\models\AppSearchHistory;
    use app\modules\admin\models\AppTemp;
    use app\modules\admin\models\Call;
    use app\modules\admin\models\Fio;
    use app\modules\admin\models\FioCase;
    use app\modules\admin\models\History;
    use app\modules\admin\models\Problem;
    use Yii;
    use yii\debug\models\search\Log;
    use yii\filters\AccessControl;
    use yii\helpers\Html;
    use yii\helpers\Json;
    use yii\helpers\Url;
    use yii\web\Controller;
    use yii\filters\VerbFilter;
    use app\models\LoginForm;
    use app\controllers\BehaviorController;
    use app\modules\admin\models\Login;
    use app\models\User;
    use app\modules\admin\models\App;
    use app\modules\admin\models\MyDate;
    use app\modules\admin\models\Recal;
    use yii\helpers\ArrayHelper;
    use app\modules\admin\models\Comment;
    use app\modules\admin\models\Podr;
    use yii\web\UploadedFile;

    date_default_timezone_set('Asia/Yekaterinburg');

    class SiteController extends BehaviorController
    {
        const  LDAP_HOST = "snhrs.ru";
        const  LDAP_PORT = "389";
        const  DOMAIN = "@snhrs.ru";
        //    public function beforeAction($action)
//    {
//        if(!isset($_SESSION['User']['id'])) { return $this->redirect(['/site/login']); }
//        return true;
//    }

        public function actions()
        {
            return [
                'error' => [
                    'class' => 'yii\web\ErrorAction',
                ],
                'captcha' => [
                    'class' => 'yii\captcha\CaptchaAction',
                    'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                ],
            ];
        }

        /*
         * Вывод статистики заведеных заявок
         */
        public function actionStat()
        {
//        $logins = Login::getLoginList();


            $date_to = Yii::$app->request->post() ? Yii::$app->request->post('date_to') : date('Y-m-d');
            $date_do = Yii::$app->request->post() ? Yii::$app->request->post('date_do') : date('Y-m-d');
            $_id_user = Yii::$app->request->post() ? Yii::$app->request->post('id_user') : null;


            $query = App::find()->joinWith(['appContent', 'user', 'podr'])->where(['!=', 'app.id_user', 50]);

            if (isset($id_user)) {
                $app = $query
                    ->andFilterWhere(['>=', 'date_ct', MyDate::getTimestamp($date_to . ' 00:00:00')])
                    ->andFilterWhere(['<=', 'date_ct', MyDate::getTimestamp($date_do . ' 23:59:59')])
                    ->andFilterWhere(['=', 'app.id_user', $_id_user])
                    ->all();
            } else {
                $app = $query
                    ->andFilterWhere(['>=', 'date_ct', MyDate::getTimestamp($date_to . ' 00:00:00')])
                    ->andFilterWhere(['<=', 'date_ct', MyDate::getTimestamp($date_do . ' 23:59:59')])
                    ->all();
            }

            if (isset($_org)) {
                $problem = App::find()
                    ->select(['COUNT(*) AS cnt', 'id_problem'])
                    ->joinWith(['problem'])
                    ->andFilterWhere(['>', 'date_ct', MyDate::getTimestamp($date_to . ' 00:00:00')])
                    ->andFilterWhere(['<', 'date_ct', MyDate::getTimestamp($date_do . ' 23:59:59')])
                    ->andFilterWhere(['=', 'id_podr', $_org])
                    ->groupBy(['id_problem'])
                    ->all();
            } else {
                $problem = App::find()
                    ->select(['COUNT(*) AS cnt', 'id_problem'])
                    ->joinWith(['problem'])
                    ->andFilterWhere(['>', 'date_ct', MyDate::getTimestamp($date_to . ' 00:00:00')])
                    ->andFilterWhere(['<', 'date_ct', MyDate::getTimestamp($date_do . ' 23:59:59')])
                    ->groupBy(['id_problem'])
                    ->all();
            }


            if (isset($_org)) {
                $apps = ArrayHelper::map($app, 'id', 'id');
                $history = History::find()
                    ->joinWith(['usercomment' => function ($q) {
                        $q->select(['id', 'username']);
                    }])
                    ->andWhere(['id_app' => $apps])
                    ->asArray()->all();
            } else {
                $history = History::find()
                    ->joinWith(['usercomment' => function ($q) {
                        $q->select(['id', 'username']);
                    }])
//            ->where(['id_user' => 1])
                    ->andFilterWhere(['>', 'date', MyDate::getTimestamp($date_to . ' 00:00:00')])
                    ->andFilterWhere(['<', 'date', MyDate::getTimestamp($date_do . ' 23:59:59')])
                    ->asArray()->all();
            }

//        $problem = App::find()
//            ->select(['COUNT(*) AS cnt', 'id_problem'])
//            ->joinWith(['problem'])
//            ->andFilterWhere(['>', 'date_ct', MyDate::getTimestamp($date_to . ' 00:00:00')])
//            ->andFilterWhere(['<', 'date_ct', MyDate::getTimestamp($date_do . ' 23:59:59')])
//            ->groupBy(['id_problem'])
//            ->all();


//        $history = History::find()
//            ->andFilterWhere(['>', 'date', MyDate::getTimestamp($date_to . ' 00:00:00')])
//            ->andFilterWhere(['<', 'date', MyDate::getTimestamp($date_do . ' 23:59:59')])
//            ->all();

//        $history = History::find()
//            ->joinWith(['usercomment' => function ($q) { $q->select(['id', 'username']);}])
////            ->where(['id_user' => 1])
//            ->andFilterWhere(['>', 'date', MyDate::getTimestamp($date_to . ' 00:00:00')])
//            ->andFilterWhere(['<', 'date', MyDate::getTimestamp($date_do . ' 23:59:59')])
//            ->asArray()->all();

//        echo "<pre>"; print_r($history); die();
//        echo "<pre>";
//        print_r($test);
//        echo "</pre>";

            return $this->renderAjax('stat', [
                'date_to' => $date_to,
                'date_do' => $date_do,
//            'user' => $user,
//            'countCall' => $countCall,
                'problem' => $problem,
//            'model' => $model,
                'history' => $history,
                'app' => $app,
                'org' => $_org,
            ]);
        }


        public function actionErrorExcel(){
            $model = Temp::find()->where(['type' => Temp::TYPE_Excell])->all();

            return $this->render('error-excel', [
                'model' => $model,
            ]);
        }

        /**
         * @return string
         * Экспорт статистики
         */
        public function actionExport()
        {
            $this->layout = 'main_service';
            $searchModel = new AppSearch();
            $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

            return $this->render('export', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }


        /**
         * $id - АйДи заявки
         * Отмечаем заявку как глупую.
         */
        public function actionStupid($id)
        {
            $app = new App(['id' => $id]);

            $result = [
                "data" => $app->setStupid()
            ];

            return json_encode($result);
        }



        /**
         * $id - АйДи заявки
         * Отмечаем заявку как глупую.
         */
        public function actionExec($id)
        {
            $app = new App(['id' => $id]);

            $result = [
                "data" => $app->setExec()
            ];

            return json_encode($result);
        }



        /**
         * Кнопка - отсутствую
         */
        public function actionAbsent()
        {
            $app = new Login(['id' => Yii::$app->user->id]);

            $result = [
                "data" => $app->setAbsent()
            ];

            return json_encode($result);
        }


        /**
         * $id - тип выдгиающей сайдбара
         * Правый сайдбар
         */
        public function actionSide($id)
        {
            if ($id == 'info'){
                $data = SideInfoTmp::widget();
            }elseif ($id == 'phone'){
                $data = PhoneWidget::widget();
            }

            return $data;
        }


        /**
         * Согласование заявки
         * $id - ID
         * $s - статус соглаосвания
         * 1 - согласован
         * 2 - отклонен
         */
        public function actionAgreed($id, $s){
            $id_app = $id;

            $app = App::findOne($id);
            $app->agreed = $s;

            $app->agreed();
            $id = $app->agreedTicketID();

            $history = new History();
            $history->id_app = $id_app;
            $history->id_user = Yii::$app->user->id;
            $history->status = 11;
            $history->comment = 50;  //АйДи пользователя на ком осталсь заявка после изменения
            $history->setHistory();

            return $this->redirect(['//site/index', 'id' => isset($id) ? $id : null]);
        }

        /*
         * Вывод статистики заведеных заявок
         */
        public function actionReport()
        {
            $date_to = Yii::$app->request->post() ? Yii::$app->request->post('date_to') : date('Y-m-d');
            $date_do = Yii::$app->request->post() ? Yii::$app->request->post('date_do') : date('Y-m-d');
            $_org = Yii::$app->request->post() ? Yii::$app->request->post('org') : null;
            $_depart = Yii::$app->request->post() ? Yii::$app->request->post('depart') : null;


            if ($_depart and $_org) {
                $depart = Depart::getDepartByOrg($_org, $_depart, 1);
                $search = Depart::find()->where(['id_depart' => array_keys($depart)])->select(['id'])->column();
            } elseif ($_org) {
                $depart = Depart::getDepartByOrg($_org, null, 1);
                $search = Depart::find()->where(['id_depart' => array_keys($depart)])->select(['id'])->column();
            } else {
                $search = 999999999;
            }

            if (!$_depart and !$_org) {
                $search = null;
            }

            $app = App::find()->joinWith(['appContent', 'depart'])
                ->andFilterWhere(['>=', 'date_ct', MyDate::getTimestamp($date_to . ' 00:00:00')])
                ->andFilterWhere(['<=', 'date_ct', MyDate::getTimestamp($date_do . ' 23:59:59')])
                ->andFilterWhere(['in', 'app.id_depart', $search])
                ->andFilterWhere(['<>', 'app.id_user', 50])
                ->all();

            $problem = App::find()
                ->select(['COUNT(*) AS cnt', 'id_problem'])
                ->joinWith(['problem'])
                ->andFilterWhere(['>', 'date_ct', MyDate::getTimestamp($date_to . ' 00:00:00')])
                ->andFilterWhere(['<', 'date_ct', MyDate::getTimestamp($date_do . ' 23:59:59')])
                ->andFilterWhere(['in', 'app.id_depart', $search])
                ->andFilterWhere(['<>', 'id_user', 50])

                ->groupBy(['id_problem'])
                ->all();


					 

            return $this->renderAjax('report', [
                'date_to' => $date_to,
                'date_do' => $date_do,
                'problem' => $problem,
                'app' => $app,
                'org' => $_org,
                'depart' => $depart,
                'id_depart' => $_depart
            ]);
        }


        /*
         * Вывод заявок для Диспетчера, для отметки.
         * Ометить под одному
         * Отметить все заявки
         */
        public function actionClose()
        {

            $delete = isset($_GET['delete']) ? $_GET['delete'] : null;
            if (isset($delete)) {
                if ($delete == 'All') {
                    $content = AppContent::find()->select(['id_app'])->andWhere(['review' => 1])->all();
                    $model = App::find()
                        ->andwhere(['app.id' => ArrayHelper::getColumn($content, 'id_app')])
                        ->andwhere(['status' => 3])
                        ->andWhere(['type' => 1])
                        ->joinWith(['appContent'])
                        ->limit(150)
                        ->orderBy(['status' => SORT_DESC])
                        ->orderBy(['date_ct' => SORT_DESC])
                        ->all();
                    foreach ($model as $item) {
                        $upd = AppContent::findOne($item->appContent->id);
                        $upd->review = null;
                        $upd->save();
                    }
                } else {
                    $upd = AppContent::findOne($delete);
                    $upd->review = null;
                    $upd->save();
                }
            }

            if (AppContent::find()->select(['id'])->andWhere(['review' => 1])->exists()) {
                $content = AppContent::find()->select(['id_app'])->andWhere(['review' => 1])->all();
                $model = App::find()
                    ->andWhere(['app.id' => ArrayHelper::getColumn($content, 'id_app')])
                    ->andWhere(['status' => 3])
                    ->andWhere(['type' => 1])
                    ->joinWith(['appContent'])
                    ->joinWith(['podr'])
                    ->joinWith(['priority'])
                    ->joinWith(['problem'])
                    ->joinWith(['user'])
                    ->limit(150)
                    ->orderBy(['status' => SORT_DESC])
                    ->orderBy(['date_ct' => SORT_DESC])
                    ->all();
                $comments = AppComment::find()->joinWith(['comments'])->where(['id_app' => ArrayHelper::getColumn($content, 'id_app')])->orderBy(['date' => SORT_DESC])->joinWith(['user'])->all();
                $status = '';
            } else {
                $status = "Нет не прочтенных";
                $model = null;
                $comments = null;
            }
            return $this->render('close', ['model' => $model, 'comments' => $comments, 'status' => $status]);
        }

        /*
         * Страница настройек пользователя
         */
        public function actionSettings()
        {
            $model = Login::findOne(Yii::$app->user->id);

            if ($model->load(Yii::$app->request->post())) {
                $model->ip = str_replace(',', '.', $model->ip);
                $model->save();

                Yii::$app->user->login($model, 0);
            }

            return $this->render('settings', [
                'model' => $model,
            ]);
        }

        /*
         * Удаляем завяку, и все что с ней связано
         */
        public function actionDelete($id, $search = null)
        {
            if (Yii::$app->user->can('SuperAdmin')) {
                $app = new App();
                $app->delAll($id);
            }

            return $this->redirect(['index', 'search' => $search]);
        }


        /**
         * @throws \Exception
         * Вывод айтивных заявок
         */
        public function actionActive()
        {
            echo \app\components\AdditionalWidget::widget();
        }


        public function actionBus()
        {

            $this->layout = "main_map";

            return $this->render('bus');

        }

        /**
         * @return string
         * Настройка оповщеения
         */
        public function actionNotify()
        {
            $model = new \app\models\AppNotify();

            if ($model->load(Yii::$app->request->post())) {
                foreach ($model->user_id as $item) {
                    $model->record($item);
                }
            }

            return $this->render('notify',
                [
                    'model' => $model,
                ]);
        }

        /**
         * Обновляем уведомелния в реальном времени
         */
        public function actionAjaxNotify($del = null)
        {
            if (!isset($del)) {
                AppNotify::getNotifyActive();
            } else {
                AppNotify::findOne($del)->setVisible();
            }
        }

        /*
         * Добавление, вывод
         */
        public function actionIndex($search = null)
        {

            $id = isset($_GET['id']) ? $_GET['id'] : null;

            if (Yii::$app->user->identity){
                $userId = Yii::$app->user->id;
            }else{
                return $this->redirect(['//site/login']);
            }
            $model = new App();

            //Меняем на просмотренный
            if ($id) {
                App::appReview($_GET['id'], 1);
            }

            /* Если нет АйДи в строке запроса, приваимваем АйДи , последней активной заявки, или же последней закрытой заявки*/
            if (!isset($_GET['id']) and !isset($_GET['app'])) {
                $_GET['id'] = App::getIdApp(isset($_GET['search']) ? $_GET['search'] : null);
            }


            if (Yii::$app->user->id == 1){
                if (isset($_POST['AppProject'])){
                    $model = AppProject::findOne(['id_app' => $id]);
                    $model->load(Yii::$app->request->post());
                    $model->date_pl = strtotime($model->date_pl);
                    $model->date_cur = strtotime($model->date_cur);
                    $model->date_ct = strtotime($model->date_ct);

                    if ($model->oldAttributes['comment'] != $model->comment)
                        $model->date_comm = $model->comment ? strtotime('now') : null;
                    $model->save();

                    return $this->redirect(['//site/index', 'search' => isset($_GET['search']) ? $_GET['search'] : null, 'id' => $_GET['id']]);
                }
            }

            if ($model->load(Yii::$app->request->post())) {

                if ($_GET) {
                    if (isset($_GET['app'])) {
                        $idByApp = array();

                        $app = new App();
                        $app->id_podr = $model->id_podr;
                        $app->id_problem = $model->id_problem;
                        $app->date_ct = MyDate::getTimestamp(date('Y-m-d H:i:s'));
                        $app->id_priority = $model->id_priority;
                        $app->id_user = $model->id_user;
                        $app->review = '1';
                        $app->status = 12;
                        $app->type = $model->type ? 1 : null;
                        $app->id_depart = $model->getDepartId($model->id_podr, $model->fio);

                        if ($app->save()) {
                            $appContetn = new AppContent();
                            $appContetn->id_app = $app->id;
                            $appContetn->id_user = $userId;
                            $appContetn->content = $model->content;
                            $appContetn->note = $model->note;
                            $appContetn->id_fio = Fio::getId($model->fio);
                            $appContetn->ip = $model->ip == '10.224.' ? '' : Html::encode(str_replace(',', '.', $model->ip));
                            $appContetn->phone = Html::encode($model->phone);
                            $appContetn->dv = $model->type ? Html::encode($model->type) : null;
                            $appContetn->buh = $model->buh ? Html::encode($model->buh) : null;
                            $appContetn->review = 1;
                            $appContetn->save();

                            History::first($app);

                            Sitdesk::appMail($app->id, $model->id_user);

                            $idByApp[] = $app->id;
                        }

                        $model->documentFiles = UploadedFile::getInstances($model, 'documentFiles');
                        $model->upload($idByApp);

                    } elseif ($_GET['id']) {

                        $id_app = $_GET['id'];

                        $upd = App::findOne($id_app);

                        $old_user = $upd->id_user;
                        $old_problem = $upd->id_problem;
                        $old_status = $upd->status;

                        $upd->id_podr = isset($model->id_podr) ? $model->id_podr : $upd->id_podr ;
                        $upd->id_problem = isset($model->id_problem) ? $model->id_problem : $upd->id_problem ;
                        $upd->id_priority = $model->id_priority;
                        $upd->id_user = $model->id_user;
//                        $upd->id_depart = $model->getDepartId($model->id_podr, $model->fio);

                        if ($upd->type == 5){
                            if ($upd->buh <> $model->buh){
                                AppProject::setProjectBase($model->buh, $id_app);
//                                $__prj = AppProject::findOne(['id_app' =>$id_app]);
//                                $__prj->base = $model->buh;
//                                $__prj->save();
//
//                                $_analog = AppAnalog::find()->where(['id_parent_app' => $id_app])->all();
//                                foreach ($_analog as $_item) {
//                                    $__app = AppContent::findOne(['id_app' => $_item->id_app]);
//                                    $__app->buh = $model->buh;
//                                    $__app->save();
//
//                                    $__prj = AppProject::findOne(['id_app' => $_item->id_app]);
//                                    $__prj->base = $model->buh;
//                                    $__prj->save();
//                                }
                            }
                        }


                        /* Если после изменения исполнители отличаются, то статуст меняем на Перенаправлен */
                        if ($model->id_user == App::findOne($id_app)->id_user) {
                            if ($old_status == History::STATUS_CONSIDERATION) { //12 - на исполнение, если заявка на рассмтрении то переводим в работу
                                $upd->status = History::STATUS_WORK; //1 - в работе
                            }
                            if ($old_status == History::STATUS_NEW) { //12 - на исполнение, если заявка на рассмтрении то переводим в работу
                                $upd->status = History::STATUS_CONSIDERATION; //1 - в работе
                            }

                            try {
                                if ($upd->save()) {
                                } else {
                                    $error = '';
                                    foreach ($upd->errors as $key => $value) {
                                        $error .= $value[0];
                                    }
                                    echo "ok2";
                                    echo "<pre>"; print_r($error ); die();
                                }
                            } catch (\Exception $ex) {
                                echo "ok3";
                                echo "<pre>"; print_r($ex->getMessage( )); die();
                            }


                        } else { //Все заявки которые перенарпавляют другим спец. то переводим заявку в статус "На рассмотрении"

                            $upd->status = History::STATUS_CONSIDERATION; //12 - на исполнение
                            $upd->save();

                            Sitdesk::appMail($_GET['id'], $model->id_user, 3);
                        }

                        History::add($upd, $old_status, $old_problem, $model->back == 1 ? $old_user : null);

//                        echo $old_user;
                        /* Если после изменения исполнители отличаются, то статуст меняем на Перенаправлен */
                        if ($model->id_user <> $old_user and $old_status <> History::STATUS_NEW) {
//                        History::add($upd, History::STATUS_REDIRECT_NOW, $old_problem, $model->back == 1 ? $old_user : null); //900 - перенавлен
                        }


                        $appContetn = AppContent::findOne(['id_app' => $_GET['id']]);
                        $appContetn->id_user = Yii::$app->user->id;
                        $appContetn->content = $model->content;
                        $appContetn->note = $model->note;
                        $appContetn->id_fio = Fio::getId($model->fio);
                        $appContetn->ip = str_replace(',', '.', $model->ip);
                        $appContetn->phone = $model->phone;
                        $appContetn->dv = $model->type ? $model->type : null;
                        $appContetn->buh = $model->buh ? $model->buh : null;
                        $appContetn->save();

                        $model->documentFiles = UploadedFile::getInstances($model, 'documentFiles');
                        $idByUpd[] = $_GET['id'];
                        $model->upload($idByUpd);

                        return $this->redirect(['//site/index', 'search' => isset($_GET['search']) ? $_GET['search'] : null, 'id' => $_GET['id']]);
                    }
                }

                return $this->redirect(['//site/index', 'search' => isset($_GET['search']) ? $_GET['search'] : null]);

            }

            /* Если у пользователя нет ни одной заявки(Открытых/Закрытых/В ожидании)*/
            if ($_GET['id'] == null) {
                $model = new App();
                $comment = AppComment::commentList($_GET['id']);    //вывод коментарий заявки
                $recal = Recal::recalList();                        //вывод напоминаний
                $call = Call::getCount();                           //вывод звонков(Справочная)

                return $this->render('index',
                    [
                        'model' => $model,
                        'comment' => $comment,
                        'recal' => $recal,
                        'call' => $call
                    ]);
            }

            $model = App::appList($_GET['id']);                 //вывод всей информации завяки
            $comment = AppComment::commentList($_GET['id']);    //вывод коментарий заявки
            $recal = Recal::recalList();                        //вывод напоминаний
            $call = Call::getCount();

            return $this->render('index',
                [
                    'model' => $model,
                    'comment' => $comment,
                    'recal' => $recal,
                    'call' => $call
                ]);
        }

        /*
         * Показываем историю заявки
         */
        public function actionHistory($id = null)
        {
            if (isset($id)) {
                $history = History::find()
                    ->where(['id_app' => $id])
                    ->orderBy(['date' => SORT_DESC])
                    ->joinWith(['usercomment' => function ($q) {
                        $q->select(['id', 'username']);
                    }])
                    ->joinWith(['problem' => function ($q) {
                        $q->select(['id', 'name']);
                    }])
                    ->all();
                $status = true;
            } else {
                $history = array();
                $status = false;
            }

            return $this->renderAjax('history',
                [
                    'history' => $history,
                    'status' => $status,
                ]
            );
        }

        /*
         * Авторизация через АД
         */
        public function actionLogin()
        {
            $this->layout = 'main_empty';
            $formUser = isset($_POST['LoginForm']['username']) ? $_POST['LoginForm']['username'] : null;

            $formPass = isset($_POST['LoginForm']['password']) ? $_POST['LoginForm']['password'] : null;

            $ip = $_SERVER['REMOTE_ADDR'];

            $model = new LoginForm();

            $countLogin = Login::find()->where(['ip' => $ip])->count();
            if ($countLogin > 0) {
                Yii::$app->user->login(Login::findByIp($ip), 0);
//                if (Yii::$app->user->identity->visible == 1) {
//                    $model->denyAccess();
//                }
                return $this->redirect(Url::to(['index']));
            }

            if (isset($formUser) and isset($formPass)) {
                $domain = Login::getDomainSettings(Login::findOne(['login' => $formUser])->domain);
                $login = $formUser . '@' . $domain[1];
                $password = $formPass;
                $ldap = ldap_connect($domain[0], self::LDAP_PORT) or die("Cant connect to LDAP Server");
                ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);

                if ((ldap_bind($ldap, $login, $password))) {
                    $bind = ldap_bind($ldap, $login, $password);
                } else {
                    $model->denyPassword();
                }
                unset($domain);
                if (isset($bind)) {
//                    print_r(Login::findByLogin($formUser));
                    Yii::$app->user->login(Login::findByLogin($formUser), 0);
                    if (Yii::$app->user->id) {
                        if (Yii::$app->user->identity->visible == 1) {
                            $model->denyAccess();
                        }
//                        return $this->redirect('index');
                    } else {
                        Yii::$app->user->logout();
                    }
                    return $this->redirect(Url::to(['index']));
                } else {
                    $model->denyPassword();
                }
            }
            return $this->render('login', [
                'model' => $model,
            ]);
        }


        public function actionLogout()
        {
            Yii::$app->user->logout();

            return $this->goHome();
        }

        /**
         * Вывод ФИО из описания заявки
         * @return string
         * @throws \Exception
         */
        public function actionFiocase()
        {
            $id = $_POST['id'];
            $type = $_POST['type'];
            return FioCaseWidget::widget(
                [
                    'id' => $id,
                    'type' => $type
                ]
            );
        }

        /*
         * Меняем статус заявке
         * 1 - В работу
         * 2 - в Ожидании
         * 3 - закрыто
         */
        public function actionStatus($id, $status, $search = null)
        {
            $app = App::findOne($id);

            $history = new History(['id_app' => $id]);

            $remind = new AppRemind(['serial_app' => $id, 'id_app' => $id]);

            $analog = new AppAnalog(['id_app' => $id]);

            $save = false; //разрешение на сохранние

            /* Если заявка была в ожидании, то удаляем все напоминания */
            if ($app->status == History::STATUS_ASIDE) {
                $remind = new AppRemind(['id_app' => $app->id]);
                $remind->delIdApp();
            }

            /* Если заявка был статус закрыто,  */
            if ($app->status == History::STATUS_CLOSE) {
                $analog->setAnalogStatusActive();
            }

            if ($status == $history::STATUS_CLOSE) {

//            $remind = new AppRemind(['serial_app' => $app->id]);
//            $remind->setSerialApp();

                if (AppComment::appComment($app->id)) {
                    $save = true;

                    if ($history->existsBack()) {
                        $app->id_user = $history->getUserBack();
                        $status = $history::STATUS_CONSIDERATION;
                        $history->setBackNull();
                    } else {
                        $content = AppContent::findOne(['id_app' => $app->id]);
                        $content->date_cl = strtotime('now');
                        $content->save();

                        $remind->SerialRemind(); // проверка на последовательную заявку
                        $remind->delIdApp(); // если есть напоминания по заявке, удаляем их

                        $analog->setAnalogStatus();
                    }

                }
            } elseif ($status == 1 and ($app->id_user <> Yii::$app->user->id)) { //если заявку перевел н в работу не хоз. заявки то переходит в статус на Рассмотрение
                $status = $history::STATUS_CONSIDERATION;
                $save = true;
            } else {
                $save = true;
            }

            if ($save) {
                $app->status = $status;
                $app->save();

                History::add($app, $status);
            }

            return $this->redirect(Yii::$app->request->referrer);

//            return $this->redirect(['index', 'search' => $search]);


//        $upd->status = $status;
//
//
//        History::add($upd, $status);
//
//        $problem = new Problem();
//        $problem->id = App::findOne($id)->id_problem;
//
//
//        if ($status == 3) {
//            if (AppComment::appComment($_GET['id'])) {
//                $app->setStatus();
//            }
//            return $this->redirect(['index', 'search' => $search]);
//
//        } elseif ($status == 4) {
//
//            $app->status = 12;
////            $app->runtime = $problem->getRuntimeDisp();
//            $app->setStatus();
//
//            Comment::commentAdd($id, 'Выполнено');
//
//            return $this->redirect(['index', 'search' => $search]);
//
//        } elseif($status == 1){
//
//            $app->status = 1;
////            $app->runtime = $problem->getRuntimeUser();
//            $app->setStatus();
//
//            return $this->redirect(['index', 'search' => $search]);
//        } else{
//            $st = App::findOne($id);
//            if($st->status == 3 and Yii::$app->user->id <> $st->id_user){
//                Sitdesk::appMail($id, $st->id_user, 2);
//            }
//            $app->setStatus();
//            return $status == 3 ? $this->redirect(['index', 'search' => $search]) : $this->redirect(['index', 'id' => $id, 'search' => $search]);
//        }
        }

        /**
         * Выводи м логи пользователя
         */
        public function actionLogs($search, $limit = 10)
        {

            return LogsWidget::widget(['search' => $search, 'limit' => $limit]);
        }

        /**
         * изменяем видимость коментария для пользователя
         *
         */
        public function actionComvis($id, $vis)
        {
            $com = AppComment::findOne($id);
            $com->user_visible = $vis == 1 ? 1 : null;

            if ($com->getSave() and $vis == 1){

                $id_parent_app = AppAnalog::findOne(['id_app' => $com->id_app])->id_parent_app;

                $comment_text = date('Y-m-d H:i', $com->date).'. '. Comment::findOne($com->comment)->name;

                //Отправляем письмо пользователю
                Sitdesk::sendUserMail($id_parent_app, null, $comment_text);
            }
        }

        /**
         * Поиск по телефонным справочникам
         */
        public function actionPhone($search)
        {
            return PhoneSearchWidget::widget(['search' => $search]);
        }

        /**
         * Поиск везде где можно
         */
        public function actionGlobal($search, $limit = 30)
        {
            echo PhoneSearchWidget::widget(['search' => $search]);
//        echo LogsWidget::widget(['search' => $search, 'limit' =>$limit]);
        }

        /*
         * Справочная для Диспетчера
         */
        public function actionCall()
        {
            Call::add(); //Добовяем звонок(Справочная)

            return Html::a('<span class="btn  btn-sm btn-info fas fa-phone" title="Справочная"></span>', ['/site/call']);
        }

        public function actionMail()
        {

            $model = new Podr();

            $list = Podr::find()->where(['visible' => 1])->orderBy(['sortable' => SORT_ASC])->all();

            return $this->render('mail',
                [
                    'model' => $model,
                    'list' => $list,
                ]);
        }

        /**
         * @var $temp Temp
         * @return string
         */
        public function actionDomainTranslation()
        {

            $url1 = "http://10.224.182.4/zsmik_zup/hs/SitDesk/?type=Подразделение&name=";

            $api_uri = Temp::find()->where(['type' => 2])->all();
//            echo "<pre>"; print_r($api_uri ); die();
            $old_login = $result = $new_login = '';

            $username = "sitdesk";
            $password = getenv('sit_password');

            $fio = "Губайдуллин Ильфат Гилманович";

            $fio = str_replace(" ", "%20", $fio);

            if ($_POST) {
                $old_login = isset($_POST['old_login']) ? $_POST['old_login'] : null;
                $new_login = isset($_POST['new_login']) ? $_POST['new_login'] : null;

                $old_prefix = isset($_POST['old_prefix']) ? $_POST['old_prefix'] : null;
                $new_prefix = isset($_POST['new_prefix']) ? $_POST['new_prefix'] : null;

                foreach ($api_uri as $uri) {
                    $url = "$uri->t1/?type=ИзменениеУчетнойЗаписи&itwas=$old_login.$old_prefix&Hasbecome=$new_login.$new_prefix";
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

                    if ($uri->t1 == "http://10.224.182.2/zsmik_zup/hs/SitDesk"){
                        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . getenv('sit_password_new'));
                    }else{
                        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
                    }
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $result .= curl_exec($ch) . '<br>';
                }

                $temp = new Temp();
                $temp->t1 = $old_login . $old_prefix;
                $temp->t2 = $new_login . $new_prefix;
                $temp->t3 = $result;
                $temp->date = strtotime('now');
                $temp->type = 1;
                $temp->save();

            }

            return $this->render('domain-translation',
                [
                    'old_login' => $old_login,
                    'new_login' => $new_login,
                    'result' => $result,
                    'data' => Temp::find()->where(['type' => 1])->orderBy(['date' => SORT_DESC])->all(),
                ]);
        }


        public function actionAllUsers(){
            $url = 'http://10.224.100.11/nhrs_zup_work/hs/SitDesk/?type=%D0%92%D1%81%D0%B5%D0%9D%D0%BE%D0%BC%D0%B5%D1%80%D0%B0';

            $model = json_decode(Sitdesk::curl_buh($url));
            return $this->render('all-users',
                ['model' => $model]);
        }

        public function actionTest()
        {
            $url = "http://10.224.100.11/nhrs_zup_work/hs/SitDesk/?type=%D0%A1%D1%82%D1%80%D1%83%D0%BA%D1%82%D1%83%D1%80%D0%B0&name=2fbaf282-348d-11eb-8233-bc305bedc274";


            $model = json_decode(Sitdesk::curl_buh($url));


            return $this->render('test',
                ['model' => $model]);
        }


        /**
         * Вывод истории по поискам logs ldap
         */
        public function actionRam()
        {
            AppSearchHistory::history();
        }

        /**
         * @param $id
         * @return false|string
         * выводим список "Тип проблем"
         *
         * $buh = бд 1с
         * $type - тип запроса, бд, подразделение
         * 1 - bd
         * 2 - podr
         *
         */
        public function actionProblem($id, $buh = null, $podr = null)
        {
            $model = array();
            if ($id) {
                $query = Problem::find()->select(['id', 'name', 'runtime', 'time', 'role'])
                    ->andFilterWhere(['=', 'parent_id', $id])
                    ->andFilterWhere(['=', 'visible', 1])
                    ->orderBy(['name' => SORT_ASC]);

                $model = $query->all();
                if (isset($buh)) {
                    $allClass = ArrayHelper::map($model, 'id', 'id');

                    $_buh = AppTemp::find()->distinct()->select(['id_problem', 'type'])->where(['id_problem' => $allClass, 'id_temp' => $buh, 'type' => 1, 'visible' => null])->all();
                    $_podr = AppTemp::find()->distinct()->select(['id_problem', 'type'])->where(['id_problem' => $allClass, 'id_temp' => $podr, 'type' => 2, 'visible' => null])->all();

                    $map_buh = ArrayHelper::map($_buh, 'id_problem', 'id_problem');
                    $map_podr = ArrayHelper::map($_podr, 'id_problem', 'id_problem');

                    $merge = array_merge($map_buh, $map_podr);

                    $arr = array_keys(array_filter(array_count_values($merge), function ($v) {
                        return $v > 1;
                    }));

                    $model = Problem::find()->where(['id' => $arr])->all();
                }
            }


            $option = '';

            if (count($model) > 0) {
                $data = true;
                $option .= "<option value=''>Необходимо выбрать...</option>";
                foreach ($model as $item) {
                    $time = $item->runtime ? ' /' . $item->runtime : '';
                    $option .= "<option value = '" . $item->id . "'>" . $item->name . $time . "</option>";
                }
            } else {
                $data = false;
                $option .= "<option></option>";
            }

            $result = [
                "select" => $option,
                "data" => $data,
            ];

            return json_encode($result);
        }

        public function actionClass($buh, $podr)
        {
            $allClass = ArrayHelper::map(Problem::getProblemMainSap(), 'id', 'id');

            $_buh = AppTemp::find()->distinct()->select(['id_problem', 'type'])->where(['id_problem' => $allClass, 'id_temp' => $buh, 'type' => 1, 'visible' => null])->all();
            $_podr = AppTemp::find()->distinct()->select(['id_problem', 'type'])->where(['id_problem' => $allClass, 'id_temp' => $podr, 'type' => 2, 'visible' => null])->all();

            $map_buh = ArrayHelper::map($_buh, 'id_problem', 'id_problem');
            $map_podr = ArrayHelper::map($_podr, 'id_problem', 'id_problem');

            $merge = array_merge($map_buh, $map_podr);

            $arr = array_keys(array_filter(array_count_values($merge), function ($v) {
                return $v > 1;
            }));

            $model = Problem::find()->where(['id' => $arr])->all();

            $option = '';

            if (count($model) > 0) {
                $data = true;
                $option .= "<option value=''>Необходимо выбрать...</option>";
                foreach ($model as $item) {
                    $time = $item->runtime ? ' /' . $item->runtime : '';
                    $option .= "<option value = '" . $item->id . "'>" . $item->name . $time . "</option>";
                }
            } else {
                $data = false;
                $option .= "<option>-</option>";
            }

            $result = [
                "select" => $option,
                "data" => $data,
            ];

            return json_encode($result);
        }

        public function actionPrUser($id)
        {

            $user = AppTemp::find()->select(['id_problem', 'id_temp'])->where(['id_problem' => $id, 'type' => 3, 'visible' => null])->all();
            $_user = ArrayHelper::map($user, 'id_temp', 'id_temp');
            $model = Login::find()->select(['id', 'username'])->where(['id' => $_user])->all();

//        echo "<pre>"; print_r($model); die();

            $option = '';

            if (count($model) > 0) {
                $data = true;
                $option .= "<option value=''>Необходимо выбрать...</option>";
                foreach ($model as $item) {
                    $option .= "<option value = '" . $item->id . "'>" . $item->username . "</option>";
                }
            } else {
                $data = false;
                $option .= "<option>-</option>";
            }

            $result = [
                "select" => $option,
                "data" => $data,
            ];

            return json_encode($result);
        }


        public function actionReportList($org)
        {
            $model = Depart::getDepartByOrg($org);

            return json_encode($model);
        }


        /*
         * Информация о сайте
         */
        public function actionInfo()
        {
            return $this->render('info');
        }

        public function actionDocument()
        {
            $id_doc = $_GET['document_del'] ? $_GET['document_del'] : null;

            if (isset($id_doc) and isset($_GET['id_app'])) {
                if (AppFiles::find()->where(['id' => $id_doc])->exists()) {
                    $file = AppFiles::findOne($id_doc);
                    $path = $file->path;
                    AppFiles::unlinkFile($path);
                    AppFiles::deleteAll(['path' => $path]);
                }
            }
            return DocumentWidget::widget(['id_app' => $_GET['id_app'], 'open' => 1]);
        }

        /*
         * О сайте.
         * Документация.
         */
        public function actionAbout()
        {

            $model = new About();
            $fileNamePath = array();
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                /* Сохраянем файл на диск*/
                $model->image = UploadedFile::getInstances($model, 'image');
                foreach ($model->image as $file):
                    $fileNamePath[] = Yii::$app->storage->saveUploadedFile($file);
                endforeach;
                $model->image = $model->image ? Json::encode($fileNamePath) : null;
                $model->date_ct = MyDate::getTimestamp($model->date_ct);
                $model->save();

                Yii::$app->session->setFlash('success', 'About created');
                return $this->redirect('about');
            }
            $list = About::find()->orderBy(['date_ct' => SORT_DESC])->all();
            return $this->render('about',
                [
                    'model' => $model,
                    'list' => $list,
                ]
            );
        }

        /**
         * @return string
         * Добавляем новую заявку, с новым пользователем и другой услугой
         */
        public function actionServiceAdd()
        {
            $model = new App();
            $analog = new AppAnalog();
            $id = $_GET['id'];

            if ($model->load(Yii::$app->request->post())) {

                if ($model->id_user) {
                    $app = App::findOne($id);
                    $content = AppContent::findOne(['id_app' => $id]);
                    $files = AppFiles::findOne(['id_app' => $id]);

                    $new_app = new App();
                    $new_app->attributes = $app->attributes;
                    $new_app->id_depart = $app->id_depart;
                    $new_app->id_user = $model->id_user;
                    $new_app->agreed = $app->agreed;
                    $new_app->id_problem = $model->id_problem;
                    $new_app->status = History::STATUS_CONSIDERATION;
                    $new_app->id_podr = $model->id_podr;
                    if ($app->api_login and $app->type == 3) {
                        $new_app->type = 4;
                    } else {
                        $new_app->type = $app->type;
                    }

                    if ($new_app->save()) {
                        $new_content = new AppContent();
                        $new_content->attributes = $content->attributes;
                        $new_content->id_app = $new_app->id;
                        $new_content->save();

                        if ($files) {
                            $new_files = new AppFiles();
                            $new_files->attributes = $files->attributes;
                            $new_files->id_app = $new_app->id;
                            $new_files->save();
                        }

                        $analog->id_app = $new_app->id;
                        $analog->id_parent_app = $analog->getMainId($app->id);
                        $analog->save();

                        if ($model->comment)
                            Comment::commentAdd($new_app->id, $model->comment);

                        History::add($new_app, History::STATUS_ANALOG_CONS); // записываем как "На расммотрении"

                        $app->status = $new_app->type == 4 ? 1 : $app->status;
                        $app->save();

                        Sitdesk::appMail($new_app->id, $new_app->id_user);
                    }
                    return $this->redirect('index');
                }
            }

            return $this->renderAjax('service',
                [
                    'model' => $model,
                    'app' => App::find()->where(['app.id' => $id])->joinWith(['appContent'])->one(),
                ]
            );
        }


        /**
         * Выводи все заявки по статусу
         */
        public function actionGetStatusTicket($status){
            return  \app\components\TicketMenu::widget(['status' => $status, 'search' => null, 'name' => 'Закрытые']);
        }

        /**
         * @return string
         * Добавляем новую заявку, с новым пользователем и другой услугой
         */
        public function actionProjectAdd($id)
        {
            $model = new AppProject();
            $analog = new AppAnalog();
            $project = AppProject::findOne(['id_app' => $id]);

            if ($model->load(Yii::$app->request->post())) {

                $app = App::findOne($id);
                $app_content = AppContent::findOne(['id_app' => $id]);

                //заявка
                $new_app = new App();
                $new_app->attributes = $app->attributes;
                $new_app->id_user = $model->user_exec;
                $new_app->date_ct = strtotime('now');
                $new_app->status = History::STATUS_CONSIDERATION;
                $new_app->type = $new_app::TYPE_PROJECT_TICKET;
                $new_app->id_depart = $app->id_depart;
                $new_app->api_login = null;


                if ($new_app->save()){
                    //контент по заявке
                    $new_content = new AppContent();
                    $new_content->attributes = $app_content->attributes;
                    $new_content->content = $model->description;
                    $new_content->id_app = $new_app->id;
                    $new_content->save();

                    //записываем как аналогичная заявка
                    $analog->id_app = $new_app->id;
                    $analog->id_parent_app = $analog->getMainId($app->id);
                    $analog->save();

                    //проект
                    $model->name = $project->name;
                    $model->base = $project->base;
                    $model->user_ct = $project->user_ct;
                    $model->id_app = $new_app->id;
                    $model->parent_id = $project->id;
                    $model->date_ct = date('Y-m-d');
                    $model->save();

                    $new_app->id_project = $model->id;
                    $new_app->save();
                }

                History::add($new_app, History::STATUS_ANALOG_CONS); // записываем как "На расммотрении"
                Sitdesk::appMail($new_app->id, $new_app->id_user);  //отправляем письмо

                return $this->redirect('index');
            }

            return $this->renderAjax('project-add',
                [
                    'model' => $model,
//                    'app' => App::find()->where(['app.id' => $id])->joinWith(['appContent'])->one(),
                    'project' => $project,
                ]
            );
        }


        public function actionTicketYourself(){

            $analog = new AppAnalog();
            $id = $_GET['id'];

            $app = App::findOne($id);
            $content = AppContent::findOne(['id_app' => $id]);
            $files = AppFiles::findOne(['id_app' => $id]);

            $new_app = new App();
            $new_app->attributes = $app->attributes;
            $new_app->id_depart = $app->id_depart;
            $new_app->id_user = Yii::$app->user->id;
            $new_app->agreed = $app->agreed;
            $new_app->id_problem = $app->id_problem;
            $new_app->status = History::STATUS_CONSIDERATION;
            $new_app->id_podr = $app->id_podr;
            if ($app->api_login and $app->type == 3) {
                $new_app->type = 4;
            } else {
                $new_app->type = $app->type;
            }

            if ($new_app->save()) {
                $new_content = new AppContent();
                $new_content->attributes = $content->attributes;
                $new_content->id_app = $new_app->id;
                $new_content->save();

                if ($files) {
                    $new_files = new AppFiles();
                    $new_files->attributes = $files->attributes;
                    $new_files->id_app = $new_app->id;
                    $new_files->save();
                }

                $analog->id_app = $new_app->id;
                $analog->id_parent_app = $analog->getMainId($app->id);
                $analog->save();

                History::add($new_app, History::STATUS_ANALOG_CONS); // записываем как "На расммотрении"

                $app->status = $new_app->type == 4 ? 1 : $app->status;
                $app->save();

                Sitdesk::appMail($new_app->id, $new_app->id_user);
            }

            return $this->redirect('index');

        }


        /**
         * @return string|\yii\web\Response
         * Добавялем аналогчиную заявку по выбранной заявке
         */
        public function actionStatusForm()
        {
            $model = new AppRemind();

            if ($model->load(Yii::$app->request->post())) {

                $id_app = $_GET['id'];
                $id_status = $_GET['status'];


                $app = App::findOne($id_app);
                $app->status = $id_status;
                if ($app->save()) {

                    History::add($app, $id_status); //записываем историю

                    //если
                    if ($model->datetime){
                        $h = (int)((strtotime($model->datetime) - strtotime('now')) / 60 / 60);
                        $h = $h < 2 ? 1 : $h;
                        $model->time = $h;
                        $model->comment = $model->comment ? $model->comment . "<br> " . 'Напоминание до '.$model->datetime : 'Напоминание до '.$model->datetime ;
                    }

                    if ($model->comment)
                        Comment::commentAdd($id_app, $model->comment);

                    if ($model->in_work != 0 or $model->user_comment != 0 or $model->time or $model->serial_app) {
                        $model->id_app = $id_app;
                        $model->date = strtotime('now');
                        $model->save();
                    }
                }

                return $this->redirect('index');

            }
            return $this->renderAjax('status-form',
                [
                    'model' => $model,
                ]
            );
        }


        /**
         * Занятость сотрудников
         */
        public function actionEmployment(){

            $_user_sit = Login::getFullStatusByDepart(Login::USER_SIT);
            $_user_sap = Login::getFullStatusByDepart(Login::USER_SAP);
            $_user_ss = Login::getFullStatusByDepart(Login::USER_SS);

            echo StatusTable::widget(['model' => $_user_sit['stat'], 'max' => $_user_sit['max']]);
            echo StatusTable::widget(['model' => $_user_sap['stat'], 'max' => $_user_sap['max']]);
            echo StatusTable::widget(['model' => $_user_ss['stat'], 'max' => $_user_ss['max']]);
        }


        /**
         * Занятость сотрудников
         */
        public function actionEmploymentUser(){

            $model = Login::getLoginList();
            $option = $color = '';


            if (count($model) > 0) {
                $data = true;

                $option .= "<option value=''>- Выбрать исполнителя -</option>";
                foreach ($model as $item) {

                    $color = $item->depart == 1 ? 'blue' : '';
//                    $color = $item->depart == 3 ? '#ffc107' : $color;
                    $color = $item->depart == 4 ? 'green' : $color;
                    $color = isset($item->absent) ? 'red' : $color;

                    $option .= "<option style='color:" . $color . "' value = '" . $item->id . "'>" . $item->username . "</option>";
                }
            } else {
                $data = false;
                $option .= "<option></option>";
            }


            $result = [
                "select" => $option,
                "data" => $data,
            ];

            return json_encode($result);
        }


        /**
         * @param $delete
         * @return \yii\web\Response
         * Удаляем комментарий
         */
        public function actionComment($delete)
        {
            $comment = new  AppComment(['id' => $delete]);
            $comment->delete();

            return $this->redirect(Yii::$app->request->referrer);
        }


        public function actionGroup(){
            $_user_sit = Login::getAppStatistics(Login::USER_SIT);
            $_user_sap = Login::getAppStatistics(Login::USER_SAP);
            $_user_ss = Login::getAppStatistics(Login::USER_SS);

            echo "<pre>"; print_r($_user_sit );
            echo "<pre>"; print_r($_user_sap );
            echo "<pre>"; print_r($_user_ss ); die();
        }


        /**
         * Напоминания
         */
        public function actionRemindTime()
        {
            $remind = new AppRemind();
            $remind->getTimeRemind();
//        echo Yii::$app->controller->id . '/' . Yii::$app->controller->action->id . '/' . Yii::$app->controller->module->id;
        }


        /**
         * Напоминания
         */
        public function actionRemind()
        {

            $remind = new AppRemind();
            $remind->getOverdueRemaind();

            echo "asdasd";
        }
    }


