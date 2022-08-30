<?php

    namespace app\controllers;

	 use app\models\AppProject;
   //  use app\models\AppProject;
    use app\models\AppProjectHistory;
    use app\models\Depart;
    use app\models\Sitdesk;
    use app\models\Temp;
    use app\modules\admin\models\AppAnalog;
    use app\modules\admin\models\AppComment;
    use app\modules\admin\models\AppContent;
    use app\modules\admin\models\AppFiles;
    use app\modules\admin\models\AppRemind;
    use app\modules\admin\models\Buh;
    use app\modules\admin\models\Comment;
    use app\modules\admin\models\Fio;
    use app\modules\admin\models\History;
    use app\modules\admin\models\Login;
    use app\modules\admin\models\MyDate;
    use app\modules\admin\models\Problem;
    use app\modules\admin\models\Status;
    use app\modules\admin\models\Userlog;
    use app\modules\admin\models\App;
    use yii\debug\models\search\Log;
    use yii\helpers\Html;
    use yii\rest\ActiveController;


    class ApiController extends ActiveController
    {
        public $modelClass = 'app\models\Api';

        public function actions()
        {
            $action = parent::actions();
            unset($action['index']);
            return $action;
        }

        public function actionIndex($search = null)
        {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $model = Login::find()->all();

            if (count($model) > 0) {
                $i = 0;
                $data = array();
                foreach ($model as $item) {
                    $data[$i]['login'] = $item->login;
                    $data[$i]['username'] = $item->username;
                    $data[$i]['post'] = $item->post;
                    $data[$i]['role'] = $item->role;
                    ++$i;
                }
                return ['status' => true, 'data' => $data];
            } else {
                return ['status' => false, 'data' => 'Нет данных'];

            }
        }

        /*
         * Передаем частичные данные Заявки.
         */
        public function actionApp($login = null)
        {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $model = App::find()
                ->orderBy(['app.date_ct' => SORT_DESC])
                ->joinWith(['problem', 'priority'])
                ->joinWith(['podr' => function ($q) {
                    $q->select(['id', 'name']);
                }])
                ->andWhere(['=', 'status', 1])
                ->andWhere(['=', 'app.id_user', Login::findOne(['login' => $login])->id])
                ->all();

            if (count($model) > 0) {
                $i = 0;
                $data = array();
                foreach ($model as $item) {
                    $data[$i]['iReview'] = $item->review;
                    $data[$i]['iType'] = $item->type;
                    $data[$i]['iId'] = $item->id;
                    $data[$i]['iPodr'] = $item->podr->name;
                    $data[$i]['iProblem'] = $item->problem->name;
                    $data[$i]['iDate'] = MyDate::getDate($item->date_ct);
                    $data[$i]['iPriority'] = $item->id_priority;
                    ++$i;
                }
                return ['status' => true, 'data' => $data];
            } else {
                return ['status' => false, 'data' => 'Нет данных'];

            }
        }

        /*
         * Передаем полные данные Заявки.
         */
        public function actionContent($id = null)
        {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $model = App::find()
                ->orderBy(['app.date_ct' => SORT_DESC])
                ->joinWith(['problem', 'priority'])
                ->joinWith(['appContent' => function ($q) {
                    $q->select(['id_app', 'content', 'id_fio', 'ip'])->joinwith(['fio']);
                }])
                ->joinWith(['user' => function ($q) {
                    $q->select(['id', 'login', 'username']);
                }])
                ->joinWith(['podr' => function ($q) {
                    $q->select(['id', 'name']);
                }])
                ->andWhere(['=', 'app.id', $id])
                ->all();

            if (count($model) > 0) {
                $i = 0;
                $data = array();
                foreach ($model as $item) {
                    $data[$i]['iReview'] = $item->review;
                    $data[$i]['iType'] = $item->type;
                    $data[$i]['iId'] = $item->id;
                    $data[$i]['iContent'] = isset($item->appContent->content) ? $item->appContent->content : null;
                    $data[$i]['iLogin'] = $item->user->login;
                    $data[$i]['iPodr'] = $item->podr->name;
                    $data[$i]['iProblem'] = $item->problem->name;
                    $data[$i]['iDate'] = MyDate::getDate($item->date_ct);
                    $data[$i]['iPriority'] = $item->id_priority;
                    $data[$i]['iFio'] = isset($item->appContent->fio->name) ? $item->appContent->fio->name : null;
                    $data[$i]['iIp'] = isset($item->appContent->ip) ? $item->appContent->ip : null;
                    $data[$i]['iUsername'] = \app\models\Sitdesk::fio($item->user->username);
                    ++$i;
                }
                return ['status' => true, 'data' => $data];
            } else {
                return ['status' => false, 'data' => 'Нет данных'];

            }
        }

        public function actionFileRemove(){
            $id = $_POST['id'];
            $query = AppFiles::findOne($id);
            if ($query)
                $query->delete();
        }

        public function actionFilesAdd()
        {
            $app_file = new AppFiles();

            if ($_FILES['files']['error'] == 0) {
                $id = $_POST['id'];
                $type = $_POST['type'];

                $name = explode('.', $_FILES['files']['name']);

                $file_name = AppFiles::existsFolder() . '/' . array_shift($name) . '_' . AppFiles::namePath() . '.' . array_pop($name);
                $folder = 'uploads/document/' . $file_name;

                if (move_uploaded_file($_FILES['files']['tmp_name'], $folder)) {
                    $analog = AppAnalog::find()->where(['id_parent_app' => $id])->all();

                    //если еть уже созданные заявки по обращению добавляем запись для всех заявок
                    if ($analog) {
                        foreach ($analog as $item) {
                            $app_file->addFiles(50, $item->id_app, $file_name, $type);
                        }
                    }

                    // обавляем файл для заявки/обращения
                    $app_file->addFiles(50, $id, $file_name, $type);

                    echo "Файл корректен и был успешно загружен.\n";
                } else {
                    echo "Возможная атака с помощью файловой загрузки!\n";
                }

            }

        }


        /**
         * Добавление проекта
         */
        public function actionProjectAdd()
        {
            $result = '';

            $id_project = isset($_POST['id_project']) ? $_POST['id_project'] : null;

            $model = new AppProject();
            $history = new AppProjectHistory();


            if ($id_project) {
                $model = AppProject::findOne($id_project);
            }

            $model->id_project = $_POST['id_project'];
            $model->user_ct = $_POST['user_ct'];
            $model->user_cur = $_POST['user_cur'];
            $model->user_init = $_POST['user_init'];
            $model->user_exec = $_POST['user_exec'];
            $model->status = $_POST['status'];
            $model->date_cur = $_POST['date_cur'];
            $model->date_pl = $_POST['date_pl'];
            $model->base = $_POST['base'];
            $model->visible = $_POST['visible'];
            $model->name = $_POST['name'];
            $model->description = $_POST['description'];
            $model->status = $_POST['status'];

            if ($_POST['comment']) {
                $model->comment = $_POST['comment'];
                $model->date_comm = strtotime('now');
            }

            if ($id_project) {
                $model->user_ct = $model->user_ct;
                if ($model->save()) {
                    $_res = ['true', '', $model];
                }
            } else {
                $_res = $model->getSave();
            }

            $data = $_res[2];

            if ($_res[0]) {

                $history->attributes = $model->attributes;
                $history->user_ct = $_POST['user_ct'];
                $history->id_project = $data->id;
                $history->save();
            }


            return [
                'status' => $_res[0],
                'result' => $_res[1],
                'data' => $_res[2]
            ];
        }


        public function actionGetProject()
        {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
//
            $_prj = new AppProject();

            $query = AppProject::find();

            $post = $_POST[0];

            $deadline = isset($post['deadline']) ? $post['deadline'] : null;

            $permission = isset($post['permission']) ? $post['permission'] :  null;
            $parent_project = isset($post['parent_project']) ? $post['parent_project'] :  null;
            $wg = isset($post['wg']) ? $post['wg']  :  null;
            $wg_ticket = isset($post['wg_ticket']) ? $post['wg_ticket'] :  null;
            $wg_project = isset($post['wg_project']) ? $post['wg_project'] :  null;
            $post_filter = isset($post['Project']) ? $post['Project'] :  null;
            $user_exec = isset($post['user_exec']) ? $post['user_exec'] :  null;
//
//                echo "<pre>"; print_r($post_filter ); die();

            if ($permission){
                if ($parent_project){
                    $_prj->getParentProject($query); // вывод основных проект
                }elseif($wg){
                    $_prj->getWgtProject($query, $wg_ticket, $wg_project, $post_filter); // вывод заявок по рабочи группам
                }elseif($user_exec){
                    $_prj->getUserExecProject($query,$user_exec,$post_filter); // вывод основных проект
                }elseif($deadline){
                    $_prj->getDeadlineProject($query, $deadline, $post_filter); //выводим задачи у который подходит дата тестирования
                }else{ //вывод задач
                    if (is_array($post_filter))
                        $query = $_prj->filterProject($query, $post_filter);

                    if (!$post_filter)
                        $query->andWhere(['status' => [1,5,6,7,8, null]]);
                }
                $query->orderBy(['date_comm' => SORT_DESC]);
            }

            $model = $query->asArray()->all();

            return $model;
        }


        public function actionGetProjectName(){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            return AppProject::find()->select(['name'])->distinct(['name'])->where(['parent_id' => null])->all();
        }

        public function actionGetSapUser()
        {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            return Login::getLoginSap();
        }

        public function actionGetSituser()
        {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            return Login::find()->all();
        }

        public function actionTempUpdate(){

            $res = false;
            try{
                $query = Temp::find();
                $post_search = json_decode($_POST[0]['update']['search']);
                $post_upd = json_decode($_POST[0]['update']['field']);

                foreach ($post_search as $field => $item) {
                    $query->andWhere([$field => $item]);
                }

                $model = $query->one();

                foreach ($post_upd as $field => $item) {
//                    if ($field == 't16' or $field == 't13'){
                    if ($field == 't16'){
                        $model->$field = isset($model->$field) ? null: 1;
                    }else{
                        $model->$field = $item ? $item : null;
                    }
                }

                $model->date_upd = strtotime('now');

                if ( $model->save())
                    $res = true;

            }catch(\Exception $ex){
                echo "<pre>"; print_r($ex); echo "</pre>";
            }


            return ['result' => $res];
        }

        public function actionGetTemp(){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;


//            echo date('Y-m-d 00:00:00' , $_POST['date']); echo "<br>";
//            echo date('Y-m-d 23:59:59' , $_POST['date']); echo "<br>";
//            print_r($_POST);

            $query = Temp::find();
            foreach ($_POST as $field => $value){

//                if (isset($_POST['search'])){
//                    $query->orFilterWhere(['Like', 't1', $_POST['search']]);
//                    $query->orFilterWhere(['Like', 't5', $_POST['search']]);
//
//                }else{
                    if ($field == 't9' ){
//                        $query ->andWhere(['is', 't10', new \yii\db\Expression('null')]);
                    }
                    if ($field == 'date' or $field == 'search'){
//                    $d = date('Y-m-d 00:00:00', $value);
//                    $query->andFilterWhere(['>=', 'date' , strtotime($d)]);
                    }elseif($field == 't12' && $value == 'empty'){
                        $query ->andWhere(['is', 't12', new \yii\db\Expression('null')]);
                    }else{
                        if ($value == 'empty'){
                            $query->andWhere(['is', $field, new \yii\db\Expression('null')]);
                        }else{
                            $query->andWhere([$field => $value]);
                        }
                    }


//                $query->andWhere(['>=', 'date' , strtotime(date('Y-m-d 00:00:00'))]);
   //
                    //            $query->limit(3000);
//                }

                if ($_POST['date'] and !$_POST['search']){
                    $query->andFilterWhere(['>=', 'date' , strtotime(date('Y-m-d 00:00:00' , strtotime($_POST['date'])))]);
                    $query->andFilterWhere(['<=', 'date' , strtotime(date('Y-m-d 23:59:59' , strtotime($_POST['date'])))]);
                }

                if (isset($_POST['search'])){
                    $query->andWhere(['or',
                        ['Like', 't1', $_POST['search']],
//                        ['Like', 't5', $_POST['search']],
                    ]);
                }

                }




//            if ($_POST['search']) {
//                $query->andFilterWhere(['Like', 't1', $_POST['search']]);
//            }
//                    echo "<pre>"; print_r($query ); echo "</pre>"; die();
//                            echo "<pre>"; print_r($_POST['date'] ); echo "</pre>"; die();


            return $query->orderBy(['t10' =>SORT_ASC, 'date' =>SORT_DESC, 't5' =>SORT_DESC])->all();
        }

        public function actionTemp(){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            if (isset($_POST['file_username']) and isset($_POST['name_file'])){
                $model = new Temp();
                $message = '';
                $valid_count = Temp::find()
                    ->where(['t1' => trim($_POST['file_username'])])
                    ->andFilterWhere(['>=', 'date', strtotime(date('Y-m-d 00:00:00'))])
                    ->andFilterWhere(['<=', 'date', strtotime(date('Y-m-d 23:59:59'))])
                    ->count();

                if (!Temp::find()->where(['t1' => trim($_POST['file_username']), 't5' => trim($_POST['path_file'])])->andWhere(['in', 't4', [1,4]])->exists()){

                    if (trim($_POST['file_username']) == 'Бобрик Светлана Евгеньевна'){
                        if ($valid_count > 300){
                            return ['result' => false , 'message' => 'Превышен лимит запроса на восстановление файлов. Установлено ограничение 150 файлов в день.'];
                        }
                    }else{
                        if ($valid_count > 40){
                            return ['result' => false , 'message' => 'Превышен лимит запроса на восстановление файлов. Установлено ограничение 40 файлов в день.'];
                        }
                    }

                    $model->t1 = trim($_POST['file_username']);
                    $model->t2 = trim($_POST['file_login']);
                    $model->t3 = trim($_POST['file_ip']);
                    $model->date_upd = strtotime('now');
                    $model->t4 = $_POST['file_type'];
                    $model->t5 = trim($_POST['path_file']);
                    $model->t6 = trim($_POST['comment_file']);
                    $model->t8 = trim($_POST['file_responsible']);
                    $model->t15 = trim($_POST['file_ip']);

                    $fio = Depart::normalizeFio($_POST['file_username']);
                    $zsm = Depart::urlByFio(103, $fio);

                    $model->t7 = $zsm->Result[0]->subdivision;
                    $model->date =  strtotime('now');
                    $model->type =  $_POST['type'];

                    if($model->save()){
                        return ['result' => true, 'message' => 'Сохранено. '.$valid_count];
                    }
                }else{
                    $message = 'Такой файл уже добавлен';
                }
            }
            return ['result' => false, 'message' => $message];
        }

        /**
         * ручное добавление заявки пользователями
         */
        public function actionTicketAdd()
        {
            $status = Status::STATUS_NEW_TICKET; // статус для заявки
//        $status = Status::STATUS_AGREED; // статус для заявки

            $_user = 50;    //Айди пользователя. 50 Айди Является общим для тех кто оставляет заявку черех хелпдеск

            $app = new App();
            $project = new AppProject();

            $username = $_POST['username'];
            $ip = $_POST['ip'];
            $phone = $_POST['phone'];
            $text = $_POST['text'];
            $login = $_POST['login'];
            $domain = $_POST['domain'];
            $problem = $_POST['problem'];
            $note = $_POST['note'];
            $dv = $_POST['dv'];
            $id_user = $_POST['id_user']; //создатель проекта, заявки
            $id_project = $_POST['id_project'];
            $project_name = $_POST['project_name']; //наименование проекта
            $buh = isset($_POST['buh']) ? $_POST['buh'] : null;

//        echo "<pre>"; print_r($_POST ); die();
            if ($project_name) {
                $pr_num = AppProject::find()->where(['parent_id' => null])->orderBy(['project_num' => SORT_DESC])->one();

                $project->name = $project_name;
                $project->project_num = $pr_num->project_num + 1;
                $project->user_ct = $id_user;
                $project->base = $buh;
                $project->description = $text;
                $project->date_comm = strtotime('now');
                $project->save();
            }

            $id_project = $project->id;

//            if (isset($buh) and !$id_project) {
//                $status = Status::STATUS_AGREED; // статус для заявки (с соглаосванием)
//            } else {
                $status = Status::STATUS_NEW_TICKET; // статус для заявки (без согласования)
//            }

            $app->api_login = $id_user;
            $app->id_problem = $problem;
            $app->id_podr = $domain;
            $app->id_project = $id_project;
            $app->id_depart = App::getDepartId($domain, $username);
            $app->type = 3;
            if ($id_project)
                $app->type = $app::TYPE_PROJECT;

            $app->status = $status;
            $app->id_user = $_user;

            if($id_project)
                $app->id_user = 56;

            if ($model = $app->add()) {
                $appContetn = new AppContent();
                $appContetn->id_app = $model->id;
                $appContetn->username = $username;
                $appContetn->ip = $ip;
                $appContetn->phone = $phone;
                $appContetn->content = $text;
                $appContetn->dv = $dv;
                $appContetn->note = $note;
                $appContetn->id_user = $_user;
                $appContetn->buh = $buh;
                $appContetn->add();

                $history = new History();
                $history->id_app = $model->id;
                $history->id_user = $_user;

                $history->status = $status;
                $history->comment = 50;                 //Айди пользователя, оставивщий комментарий
                $history->runtime = History::TIME_3;    //время на выполнение услуги
                $history->setHistory();

                $project->id_app = $model->id;
                $project->save();

//            // В зависимости от заявки отправляем письмо ответственному диспетчеру сектора
                if (!isset($buh)) {
//                // 58 айди хелпедск учетки
//                Sitdesk::appMail($model->id, 58, 6);
                } else {
//                // 58 айди хелпедск учетки
//                Sitdesk::appMail($model->id, 35, 6);
                }

            }

            return $model;
        }


        public function actionGetUserTicketCount(){
            try{
                $model = new App();
                return $model::find()->select(['COUNT(*) AS id', 'status'])->andWhere(['api_login' => $_POST['login']])->groupBy(['status'])->all();
            }catch(\Exception $ex){
                echo "<pre>"; print_r($ex ); echo "</pre>";
            }
        }

        /**
         * @param $login
         * @return array
         * Список заявок пользователя
         */
        public function actionGetUserTicket()
        {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;


//            print_r($_POST);

            $login = $_POST['login'];
            $status = $_POST['status'];
            $tickets = $_POST['tickets'];
            $projects = $_POST['projects'];

            if ($status == 'project') {
                $tic = App::find()->joinWith(['appContent', 'user'])->where(['app.id' => json_decode($tickets)])->andWhere(['app.type' => 3])->orderBy(['app.date_ct' => SORT_DESC])->all();
                $prj = App::find()->joinWith(['appContent', 'user'])->where(['id_project' => json_decode($projects)])->andWhere(['app.type' => 3])->orderBy(['app.date_ct' => SORT_DESC])->all();
                $model = array_merge($tic, $prj);
            } else {
                $query = App::find()->joinWith(['appContent', 'user'])->andWhere(['api_login' => $login])->andWhere(['type' => 3])->orderBy(['date_ct' => SORT_DESC]);

                if ($status) {
                    $query = $query->andWhere(['status' => $status == 1 ? [1, 2, Status::STATUS_NEW_TICKET, 12, Status::STATUS_AGREED] : 3]);
                } else {
                    $query = $query->andWhere(['in', 'status', [1, 2, 3, Status::STATUS_NEW_TICKET, Status::STATUS_AGREED]]);
                }
                $model = $query->all();
            }


            $result = array();
            $i = 0;


            foreach ($model as $item) {
                $analog = AppAnalog::find()->where(['id_parent_app' => $item->id])->orFilterWhere(['id_app' => $item->id])->count();
                $problem = Problem::findOne($item->id_problem);

                $result[$i]['id'] = $item->id;
                $result[$i]['date_ct'] = $item->date_ct;
                $result[$i]['id_project'] = isset($item->id_project) ? $item->id_project : null;
                $result[$i]['id_user'] = $item->id_user;
                $result[$i]['username'] = $item->user->username;
                $result[$i]['status'] = $item->no_exec ? 100 : $item->status;
                $result[$i]['date_cl'] = $item->appContent->date_cl;
                $result[$i]['content'] = $item->appContent->content;
                $result[$i]['problem'] = $problem->name;
                $result[$i]['api_login'] = $item->api_login;
                $result[$i]['buh'] = $item->appContent->buh;
                $result[$i]['id_project'] = $item->id_project;
                $result[$i]['project_name'] = isset($item->appProject->name) ? $item->appProject->name : null;
                $result[$i]['comment'] = AppComment::getCount($item->id);
                $result[$i]['analog'] = $analog ? $analog : 1;
                $i++;
            }

            return $result;
        }


        /**
         * @param $login
         * @return array
         * Список заявок пользователя
         */
        public function actionGetProjectTicket()
        {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $id_project = $_POST['id_project'];

            $query = App::find()
                ->joinWith(['appContent', 'user'])
                ->andWhere(['id_project' => $id_project])
                ->andWhere(['app.id_user' => 50])
                ->orderBy(['date_ct' => SORT_DESC]);

            $result = array();
            $i = 0;

            foreach ($query->all() as $item) {
                $analog = AppAnalog::find()
                    ->where(['id_parent_app' => $item->id])
                    ->orFilterWhere(['id_app' => $item->id])
                    ->count();

                $problem = Problem::findOne($item->id_problem);
                $result[$i]['id'] = $item->id;
                $result[$i]['date_ct'] = $item->date_ct;
                $result[$i]['id_project'] = isset($item->id_project) ? $item->id_project : null;
                $result[$i]['id_user'] = $item->id_user;
                $result[$i]['username'] = $item->user->username;
                $result[$i]['status'] = $item->no_exec ? 100 : $item->status;
                $result[$i]['date_cl'] = $item->appContent->date_cl;
                $result[$i]['content'] = $item->appContent->content;
                $result[$i]['problem'] = $problem->name;
                $result[$i]['buh'] = $item->appContent->buh;
                $result[$i]['comment'] = AppComment::getCount($item->id);
                $result[$i]['analog'] = $analog ? $analog : 1;
                $result[$i]['api_login'] = $item->api_login;
                $i++;
            }

            return $result;
        }


        /**
         * @param $login
         * @return array
         * Список баз 1с
         */
        public function actionGetBase()
        {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $base = Buh::getList();

            if ($base) {
                return [
                    'status' => true,
                    'data' => $base,
                ];
            } else {
                return [
                    'status' => false,
                ];
            }

        }

        public function actionGetBaseByName(){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $name = $_POST['name'];
            return Buh::findOne(['name' => $name, 'visible' => 1]);
        }

        public function actionProjectCreate(){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $id = $_POST[0]['id'];

//            echo "<pre>"; print_r($id); die();

            if ($id){

                $model = new AppProject();
                $result = $model->addProject($id);

                return $result;
            }

        }


        /**
         * @param $id
         * @return array
         * Полная информация по заявке
         */
        public function actionCommentUpd()
        {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $id = $_POST['id'];
            $field = $_POST['field'];
            $text = $_POST['text'];

            $model = AppComment::findOne($id);

            $model->$field = Comment::getId(Html::encode(trim($text)));

            if ($res = $model->getSave('Запись обновлена')){
                AppProject::updateAll(['date_comm' => strtotime('now')], ['id_app' => $model->id_app]);
            }

            if ($res[0] == true) {
                return [
                    'status' => true,
                    'data' => $res[1],
                ];
            } else {
                return [
                    'status' => false,
                    'data' => $res[1],
                ];
            }
        }


        /**
         * @param $id
         * @return array
         * Полная информация по заявке
         */
        public function actionProjectUpd()
        {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $id = $_POST['id'];
            $field = $_POST['field'];
            $text = $_POST['text'];

            $model = AppProject::findOne($id);

            $app = new App();

            $message = 'Запись обновлена';

            //Закрываем заявку
            if ($field == "close-ticket"){
                $app->id = $model->id_app;
                $app->closeTicket();

                return [
                    'status' => true,
                    'data' => 'Заявка закрыта',
                ];
            }

            //Удаляем заявку
            if ($field == "dell-project"){
                $app->delAll($model->id_app);
                $model->delete();

                return [
                    'status' => true,
                ];
            }

            //Изменяем исполнителя в зявке если меняется исполнитель в проекте
            if ($field == 'user_exec') {
                $app = App::findOne($model->id_app);
                $id_user = $app->id_user;
                $app->id_user = $text;
                if($app->save() and $text <> $id_user->id_user ){
                    History::add($app, History::STATUS_ANALOG_CONS); // записываем как "На расммотрении"
                }
            }


            //При изменении названия проекта, поменяем и роидетльский айди
            if ($field == 'name'){
                $_p = AppProject::findOne(['name' => $text], ['parent_id' => null]);
                $model->parent_id = $_p->id;
                $model->project_num = $_p->project_num;
            }


            //Изменяем систему в зявке если меняется система в проекте
            if ($field == 'base') {
                if (!isset($model->parent_id))
                    AppProject::setProjectBase($text, $model->id_app);

                AppProject::setTicketBase($text, $model->id_app);
            }

            //Изменяем исполнителя в зявке если меняется исполнитель в проекте
            if ($field == 'description') {
                AppContent::updateAll(['content' => $text], ['=', 'id_app', $model->id_app]);
            }

            //Изменяем номер проекта во всех задачах
            if ($field == 'project_num') {
                AppProject::updateAll([$field => $text], ['=', 'parent_id', $model->id]);
            }

            if ($field == 'status' && ($text == 3 || $text == 4) ){
                $app->id = $model->id_app;
                $app->closeTicket();

                $message = 'Запись обновлена. Заявка закрыта';
            }

            $model->$field = trim(trim($text, ', '));
            $res = $model->getSave($message);

            if ($res[0] == true) {
                return [
                    'status' => true,
                    'data' => $res[1],
                ];
            } else {
                return [
                    'status' => false,
                    'data' => $res[1],
                ];
            }
        }


        /**
         * @param $id
         * @return array
         * Полная информация по заявке
         */
        public function actionGetTicket()
        {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $id = $_POST['id'];

            $model = App::find()
                ->joinWith(['appContent', 'user', 'appProject' => function ($q) {
                    $q->joinwith(['user']);
                }])
                ->where(['app.id' => $id])
                ->asArray()
                ->one();


//        $model = $model[0];
//        $result = array();
//        $i = 0;
//
//        $comment = AppComment::getCommentByApp($id);
//
//        $result['id'] = $model->id;
//        $result['date_ct'] = $model->date_ct;
//        $result['id_user'] = $model->id_user;
//        $result['id_project'] = $model->id_project;
//        $result['parent_project'] = AppProject::findOne($model->id_project)->parent_id;
//        $result['username'] = $model->user->username;
//        $result['status'] = $model->status;
//        $result['date_cl'] = $model->appContent->date_cl;
//        $result['content'] = $model->appContent->content;
//        $result['project_init'] = $model->appProject->user_init;
//        $result['project_init'] = $model->appProject->user_init;
//        $result['project_name'] = $model->appProject->name;
//        $result['project_date_pl'] = $model->appProject->date_pl;
//        $result['project_comment'] = $model->appProject->comment;
////        $result['comment'] = $comment;

            if ($model) {
                return [
                    'status' => true,
                    'data' => $model,
                ];
            } else {
                return [
                    'status' => false,
                ];
            }
        }


        /**
         * @param $id
         * @return array
         * Добавляем комменатрий
         *
         * $type = 1 Запись АДминистратора проектов
         */
        public function actionCommentAdd()
        {

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $id_app = $_POST['id_app'];
            $id_user = $_POST['id_user'];
            $comment = $_POST['comment'];
            $api_login = $_POST['api_login'];
            $type = isset($_POST['type']) ? $_POST['type'] : null;

            $model = new AppComment();
            $model->comment = Comment::getId($comment);
            $model->id_app = $id_app;
            $model->text = $comment;
            $model->id_user = $id_user;
            $model->api_login = $api_login;
            $model->date = MyDate::getTimestamp(date('Y-m-d H:i:s'));

            $app = App::findOne($id_app);
            if ($app->id_project and $type <> 1){
                $app_project = AppProject::findOne(['id_app' => $id_app]);
                $app_project->comment = $comment;
                $app_project->getSave();
            }

            if ($type == 1) {
                $model->type = $type;
                $model->user_visible = null;
            } else {
                $model->user_visible = 1;
                $model->type = null;
            }

            if ($model->save()) {
                if ($_pr = AppProject::findOne(['id_app' => $id_app])) {
                    $_pr->date_comm = strtotime('now');
                    $_pr->save();
                }

//                $remind = new AppRemind(['id_app' => $id_app, 'comment' => $comment]);
//                if ($type != 1)
//                    Sitdesk::mailRemind($id_app, 5);
//                $remind->commentRemind();

                return [
                    'status' => true,
                ];
            } else {
                return [
                    'status' => false,
                ];
            }

        }

        public function actionSendMail()
        {
            $id_app = $_POST['id_app'];
            $type = isset($_POST['type']) ? $_POST['type'] : 5;

            echo Sitdesk::mailRemind($id_app, $type);
        }

        /**
         * @return array
         * выводим список всех проблем
         */
        public function actionGetProblemList()
        {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return Problem::getListItil();
        }


        /**
         * @return array
         * выводим список всех проблем
         */
        public function actionGetProblemById()
        {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $id = $_POST['id'];

            return Problem::find()
                ->where(['id' => $id])
                ->one();
        }

        /**
         * @return array|\yii\db\ActiveRecord[]
         * выводим оснонвые тип проблем(Класс проблем)
         */
        public function actionGetProblemMain()
        {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $type = $_POST['type'];
            $buh = $_POST['buh'];

            $query = Problem::find()->select(['id', 'name', 'color', 'icon'])
                ->andFilterWhere(['is', 'parent_id', new \yii\db\Expression('null')])
                ->andFilterWhere(['=', 'type', $type]);

            if ($buh == 'buh') {
                $query->andFilterWhere(['=', 'role', 3]);
            } else {
                $query->andFilterWhere(['is', 'role', new \yii\db\Expression('null')]);
            }

            return $query->all();
        }


        /**
         * @param $id
         * @return array|\yii\db\ActiveRecord[]
         * Выводим дочерние элементы
         */
        public function actionGetProblemParent($id = null)
        {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $id = $_POST['id'];
            $type = $_POST['type'];

            return Problem::find()->select(['id', 'name', 'runtime', 'time'])
                ->andFilterWhere(['parent_id' => $id])
                ->andFilterWhere(['=', 'type', 1])
                ->andFilterWhere(['visible' => 1])
                ->orderBy(['name' => SORT_ASC])
                ->all();
        }


        /**
         * @param $id
         * @return array|\yii\db\ActiveRecord[]
         * Выводим файлы к заявке
         */
        public function actionGetFiles()
        {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $id = $_POST['id'];
            $type = $_POST['type'] ? $_POST['type'] : null;
            $count = $_POST['count'] ? $_POST['count'] : null;

            if ($type == 1)
                $id = AppProject::getIdAppByParentId($id);


            if ($type == 2)
                $type = [2,null];

            $query = AppFiles::find()->where(['id_app' => $id, 'type' => $type]);

            if ($count) {
                $model = $query->count();
            } else {
                $model = $query->all();
            }

//        if (isset($type))
//            return AppFiles::find()->where(['id_project' => $id])->all();


            return $model;
        }


        public function actionGetTicketComment()
        {

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;


            if ($_POST) {
                $id_app = $_POST['id'];
                $type = $_POST['type'];

                $comment = AppComment::getCommentByApp($id_app, $type);

                return $comment;
            }
        }

        /**
         * @return array
         * Получаем все заявки по оюрщаению
         */
        public function actionGetAppeolTicket()
        {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            if ($_POST) {
                $id_app = $_POST['id'];

                $data = [];
                $a = [];

                $analog = new \app\modules\admin\models\AppAnalog(['id_app' => $id_app]);
                $a = $analog->getAnalog();

                if (is_array($a)) {
                    $res = App::find()->joinWith(['appContent'])->where(['app.id' => $a])->all();
                    foreach ($res as $item) {
                        if ($item->id != $id_app) {
                            $history = new History(['id_app' => $item->id]);

//                        $comment = AppComment::getCommentByApp($item->id);

                            $problem = Problem::findOne($item->id_problem);

                            $data[$item->id]['id'] = $item->id;
                            $data[$item->id]['sector'] = Login::getSectorName(Login::findOne($item->id_user)->depart);
                            $data[$item->id]['status'] = $item->status;
                            $data[$item->id]['id_project'] = $item->id_project;
                            $data[$item->id]['date_cl'] = $item->appContent->date_cl;
                            $data[$item->id]['content'] = $item->appContent->content;
                            $data[$item->id]['runtime'] = MyDate::normalizeTime($history->getLastRuntime());
//                        $data[$item->id]['comment'] = $comment;
                            $data[$item->id]['problem'] = $problem->name;
                            $data[$item->id]['id'] = $item->id;
                        }
                    }
                }
            }

            return $data;

        }


        /**
         */
        public function actionReturnToWork()
        {
            if ($_POST) {
                $id_app = $_POST['id'];
                $model = App::findOne($id_app);
                $model->status = Status::STATUS_NEW_TICKET;
                $model->save();

            }
        }


        public function actionChirpStack(){
           die("asdas");
        }

    }
