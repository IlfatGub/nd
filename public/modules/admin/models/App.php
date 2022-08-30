<?php

    namespace app\modules\admin\models;

    use app\models\AppProject;
    use app\models\Depart;
    use app\models\FioValidator;
    use app\models\Sitdesk;
    use Yii;
    use yii\helpers\ArrayHelper;
    use yii\web\UploadedFile;


    /**
     * @property string $api_login Логин тех кто заводит заявку через апи
     *
     * @property int $status  Статус заявки
     * @property int $app  Статус заявки
     *
     * @property int $id
     * @property int $id_user
     * @property int $_id_user
     *
     * @property int $id_class      Класс проблем
     * @property int $id_object     Предмет проблем
     * @property int $id_problem    Тип проблем
     * @property int $type          Тип заявки
     * @property int $id_depart     Отдел
     * @property int $stupid        Заявка, пользователь котый мог выполнить сам.
     * @property int $agreed        Согласование.
     * @property int $no_exec       не Выполнено
     * @property int $id_project    заявка по проекту
     *
     * @property string date_ct
     * @property string $comment
     *
     * $type = 1   1c
     * $type = 3   обращение от пользователя
     * $type = 4   заявка созданная по обращение от пользователя
     * $type = 5   Проект
     *
     * $agreed = 1   Согласовано
     * $agreed = 2   Отклонен
     */
    class App extends \yii\db\ActiveRecord
    {

        public static function tableName()
        {
            return 'app';
        }

        public $fio;
        public $ip;
        public $phone;
        public $content;
        public $comment;
        public $text;
        public $note;
        public $dv;
        public $buh;
        public $documentFiles;
        public $folder;
        public $cnt;
        public $back;
        public $runtime;
        public $usr_podr;

        public $id_object, $id_class;
        public $id_object_name, $id_class_name;


        const TYPE_PROJECT = 5; //проект
        const TYPE_PROJECT_TICKET = 6; // заявка по проекту

        const SCENARIO_REMIND = 'remaind';

        public function rules()
        {
            return [
                [['id_user', 'id_podr', 'id_problem'], 'required'],
                [['id_podr','id_project', 'id_problem', 'date_ct', 'type', 'id_priority', 'status', 'review', 'note', 'documentFiles'], 'safe'],
                [['content', 'id_fio', 'ip', 'phone', 'dv', 'buh', 'id_podr', 'id_depart'], 'safe'],
                [['fio'], 'string'],
                [['api_login', 'comment'], 'string'],
                [['back', 'runtime', 'id_object', 'id_class', 'usr_podr', 'stupid', 'no_exec', 'cnt'], 'integer'],
                [['documentFiles'], 'file', 'maxFiles' => 15],

                ['fio', FioValidator::className()],
            ];
        }


        public function afterfind()
        {
            if (!isset($_GET['app'])) {
                if ($this->id_problem) {
                    if (isset(Problem::getById($this->id_problem)->parent_id)) {
                        $ob = Problem::getById($this->id_problem);
                        $this->id_object = $ob->parent_id;
                        $this->id_object_name = Problem::getById($this->id_object)->name;

                        $cl = Problem::getById($this->id_object);
                        $this->id_class = $cl->parent_id;
                        $this->id_class_name = Problem::getById($this->id_class)->name;
                    }
                }
            }

        }

        public function attributeLabels()
        {
            return [
                'id_podr' => 'Подразделение',
                'id_problem' => 'Тип проблемы',
                'date_ct' => 'Дата создания',
                'id_priority' => 'Приоритет',
                'id_user' => 'Исполнитель',
                'status' => 'Статус',
                'review' => 'Посмотрен',
                'id_class' => 'Тип проблемы',
                'fio' => 'ФИО',
                'phone' => 'Телефон',
                'back' => 'Вернуть заявку',
                'content' => 'Описание',
                'type' => '№ служебки',
                'note' => 'Примечание',
                'buh' => 'Система 1С',
                'stupid' => 'Лишняя заявка',
                'id_project' => 'Проект',
            ];
        }


        public function getPodr()
        {
            return $this->hasOne(Podr::className(), ['id' => 'id_podr']);
        }

        public function getProblem()
        {
            return $this->hasOne(Problem::className(), ['id' => 'id_problem']);
        }

        public function getPriority()
        {
            return $this->hasOne(Priority::className(), ['id' => 'id_priority']);
        }

        public function getUser()
        {
            return $this->hasOne(Login::className(), ['id' => 'id_user']);
        }

        public function getAppContent()
        {
            return $this->hasOne(AppContent::className(), ['id_app' => 'id']);
        }

        public function getAppStatus()
        {
            return $this->hasOne(Status::className(), ['id' => 'status']);
        }

        public function getDepart()
        {
            return $this->hasOne(Depart::className(), ['id' => 'id_depart']);
        }

        public function getAppComment()
        {
            return $this->hasMany(AppComment::className(), ['id_app' => 'id']);
        }

        public function getAppBuh()
        {
            return $this->hasOne(Buh::className(), ['id' => 'buh']);
        }

        public function getAppFiles()
        {
            return $this->hasOne(AppFiles::className(), ['id_app' => 'id']);
        }
        public function getAppProject()
        {
            return $this->hasOne(AppProject::className(), ['id' => 'id_project']);
        }
        public function getHistory()
        {
            return $this->hasOne(History::className(), ['id_app' => 'id'])->andWhere(['>', 'appHistory.runtime', 0])->orderBy(['appHistory.runtime' => SORT_DESC]);
        }


        //приводим описание в нормальный вид
        public function getContent(){
            $this->content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $this->content);
            $this->content = str_replace(array("\r\n\r\n"), "\r\n", $this->content);
            $this->content = str_replace(array('  ', '    ', '    '), "", $this->content);
            return $this->content;
        }


        public function closeTicket(){
            $upd = App::findOne($this->id);
            $upd->status = History::STATUS_CLOSE;
            if($res = $upd->getSave('Заявка закрыта')){
                History::add($upd, History::STATUS_CLOSE); //запись истории
                return true;
            }
            return false;
        }

        /*
         * Получаем АйДи отдела
         *
         */
        public static function getDepartId($org, $fio = null)
        {

            if (isset($fio)) {
                $fio = Depart::normalizeFio($fio);

                $id = $name = $parent = null;

                $zsm = Depart::urlByFio($org, $fio);

                if ($zsm->Result) {
                    foreach ($zsm->Result as $item) {
                        $id = $item->ID;
                        $name = $item->subdivision;
                        $parent = $item->Parent_ID;
                    }
                    if ($name) {
                        $depart = new Depart();
                        $depart->id_depart = $id;
                        $depart->id_parent = $parent;
                        $depart->name = $name;

                        return $depart->getId();
                    }
                } else {
                    return null;
                }
            }
            return null;
        }


        /**
         * Согласовываем заявку
         * меняем статус, ставим метку согласования
         */
        public function agreed(){
            $this->status = 11;
            $this->save();
        }

        /**
         * Последняя завяка на согласовании
         */
        public function agreedTicketID(){
            $app =  App::find()->where(['status' => 13])->orderBy(['date_ct' => SORT_DESC])->one();
            return isset($app->id) ? $app->id : null;
        }

        /*
         * Изменяем статус заявки
         * id - АйДи заявки
         */
        public static function settttStatus($id, $status, $comment = null)
        {
            $model = App::findOne($id);
            $model->status = $status;
            $model->save();

            $history = new History();
            $history->id_app = $id;
            $history->id_user = Yii::$app->user->id;
            $history->comment = $model->id_user;

            if ($comment <> 1) {
                if ($status == 3) {
                    if ($history->existsBack()) {
                        $user_back = $history->getUserBack();

                        $app = App::findOne($id);
                        $app->id_user = $user_back;
                        $app->status = 1;
                        if ($app->save()) {
                            $history->status = History::STATUS_WORK_AUTO;
                            $history->comment = $user_back;
                            $history->setHistory();

                            $history->status = History::STATUS_REDIRECT_AUTO;
                            $history->comment = $user_back;
                            $history->setHistory();
                        }

                        $history->setBackNull();

                        Sitdesk::appMail($id, $user_back, 1);

                    }
                    $content = AppContent::findOne(['id_app' => $id]);
                    $content->setDateCl();
                }
            }
        }


        /**
         * переводим заявку в работу
         */
        public function setStatusWork()
        {

            $model = self::findOne($this->id);
            $model->status = History::STATUS_WORK;
            $model->save();


            History::add($model, History::STATUS_WORK);

            echo "<pre>";
            print_r($model);
            die();

        }

        public function getIdUser()
        {
            return self::findOne($this->id)->id_user;
        }

        public function setStatusRedirect()
        {
            $this->status = 12;
            return $this->setStatus();
        }

        public function setStatus()
        {
            $model = App::findOne($this->id);
            $model->status = $this->status;


            $history = new History();
            $history->id_app = $this->id;
            $history->id_user = Yii::$app->user->id;
            $history->comment = isset($this->id_user) ? $this->id_user : $model->id_user;
            $history->runtime = $this->runtime;
            $history->status = $this->status;

            if ($this->status == 3) {
                if ($history->existsBack()) {
                    $user_back = $history->getUserBack();

                    $model->id_user = $user_back;
                    $model->status = 12;

                    if ($model->save()) {
                        $history->status = History::STATUS_WORK_AUTO;
                        $history->comment = $user_back;
                        $history->setHistory();

                        $history->status = History::STATUS_EXEC_AUTO;
                        $history->comment = $user_back;
                    }
                    $history->setBackNull();
                    Sitdesk::appMail($this->id, $user_back, 1);
                } else {
                    $history->status = History::STATUS_HISTORY_CLOSE;
                }
                $content = AppContent::findOne(['id_app' => $this->id]);
                $content->setDateCl();
            } elseif ($this->status == 1) {
                $history->status = History::STATUS_WORK;
            }
            $history->setHistory();
            $model->save();
        }


        /**
         * Получаем все активные(В работе) заявки
         */
        public static function getActiveApp()
        {
            return self::find()->where(['status' => [1, 12]])->andWhere(['id_user' => Yii::$app->user->id])->select('id')->orderby(['date_ct' => SORT_DESC])->asArray()->all();
//        return self::find()->where(['status' => 1])->andWhere(['id_user' => 4])->select('id')->asArray()->all();
        }

        /*
         * Получаем АйДи заявки которую нужно открыть после переход на сайт, или удаление заявки.
         * когда переменная АйДи в строке запроса пуста
         * Если в строке запроса есть поисковая строка SEARCH, то АйДи ищет с дополнительным поисковым параметром
         */
        public function getIdApp($search = null)
        {
            $userId = Yii::$app->user->id;
            $userCount = Yii::$app->user->identity->count;
            $userOrder = ['app.date_ct' => SORT_DESC];
            $join = ['podr', 'problem', 'user', 'priority'];
            if (isset($search)) {
                $act = App::find()->joinWith($join)->orderBy($userOrder)
                    ->joinWith(['appContent' => function ($q) {
                        $q->joinwith(['fio']);
                    }])
                    ->orWhere(['like', 'fio.name', $_GET['search']])->orWhere(['like', 'appContent.content', $_GET['search']])->orWhere(['like', 'appContent.note', $_GET['search']])->orWhere(['like', 'username', $_GET['search']])->orWhere(['like', 'appContent.dv', $_GET['search']])
                    ->andFilterWhere(['=', 'status', 1]);
                $countActive = $act->count();
                if ($countActive > 0) {
                    $id = $act->limit(1)->one();
                    return $id->id;
                } else {
                    $pen = App::find()->joinWith($join)->orderBy($userOrder)
                        ->joinWith(['appContent' => function ($q) {
                            $q->joinwith(['fio']);
                        }])
                        ->orWhere(['like', 'fio.name', $_GET['search']])->orWhere(['like', 'appContent.content', $_GET['search']])->orWhere(['like', 'appContent.note', $_GET['search']])->orWhere(['like', 'username', $_GET['search']])->orWhere(['like', 'appContent.dv', $_GET['search']])
                        ->andFilterWhere(['=', 'status', 2]);
                    $countPending = $pen->count();
                    if ($countPending > 0) {
                        $id = $pen->limit(1)->one();
                        return $id->id;
                    } else {
                        $id = App::find()->joinWith($join)->orderBy($userOrder)
                            ->joinWith(['appContent' => function ($q) {
                                $q->joinwith(['fio']);
                            }])
                            ->orWhere(['like', 'fio.name', $_GET['search']])->orWhere(['like', 'appContent.content', $_GET['search']])->orWhere(['like', 'appContent.note', $_GET['search']])->orWhere(['like', 'username', $_GET['search']])->orWhere(['like', 'appContent.dv', $_GET['search']])
                            ->andFilterWhere(['=', 'status', 3])->limit($userCount)->limit(1)->one();
                        return isset($id->id) ? $id->id : null;
                    }
                }
            } else {
                if (Yii::$app->user->can('Disp')) {
                    $act = App::find()->joinWith($join)->orderBy($userOrder)->andFilterWhere(['=', 'status', 1]);
                    $countActive = $act->count();
                    if ($countActive > 0) {
                        $id = $act->limit(1)->one();
                        return $id->id;
                    } else {
                        $pen = App::find()->joinWith($join)->orderBy($userOrder)->andFilterWhere(['=', 'status', 2]);
                        $countPending = $pen->count();
                        if ($countPending > 0) {
                            $id = $pen->limit(1)->one();
                            return $id->id;
                        } else {
                            $id = App::find()->joinWith($join)->orderBy($userOrder)->andFilterWhere(['=', 'status', 3])->limit($userCount)->limit(1)->one();
                            return isset($id->id) ? $id->id : null;
                        }
                    }
                } else {
                    $act = App::find()->where(['=', 'id_user', $userId])->joinWith($join)->orderBy($userOrder)->andFilterWhere(['=', 'status', 1]);
                    $countActive = $act->count();
                    if ($countActive > 0) {
                        $id = $act->limit(1)->one();
                        return $id->id;
                    } else {
                        $pen = App::find()->where(['=', 'id_user', $userId])->joinWith($join)->orderBy($userOrder)->andFilterWhere(['=', 'status', 2]);
                        $countPending = $pen->count();
                        if ($countPending > 0) {
                            $id = $pen->limit(1)->one();
                            return $id->id;
                        } else {
                            $id = App::find()->where(['=', 'id_user', $userId])->joinWith($join)->orderBy($userOrder)->andFilterWhere(['=', 'status', 3])->limit($userCount)->limit(1)->one();
                            return isset($id->id) ? $id->id : null;
                        }
                    }
                }
            }
        }

        /*
         * Вывод всей информации заявки.
         */
        public static function appList($id)
        {
            return App::find()->where(['app.id' => $id])->joinWith(['depart', 'appContent' => function ($q) {
                $q->joinwith(['fio']);
            }])->one();
        }

        /*
         * Выводим заявки(в работу/в ожидании/закрыто)
         * Если пользователь вошел под "Диспетчер" то выводим все завяки, всех пользователей
         * Если выводим закрытые заявки, то проверяем настройки пользователя, на количество вывода закрытых заявок
         *
         * $type, определяет что выводить, массив из заявок или колчиество заявок.
         * $type = 0 - массив
         * $type = 1 - количество
         *
         * $user = null запрос по заявкам авторизированного пользователя
         * $user <> null запрос по всем заявкам
         *
         * Если есть строка поиска то добовляем посик по полям. Не учитывая пользователя. Поиск производится по всем заявкам
         */
        public function appView($status, $type, $user = null, $search = null, $project = null)
        {
            $query = App::find();

            if ($search == "Все") {
                $query->orderBy(['app.id' => SORT_DESC]);
            } else {
                $query->orderBy(['appContent.date_cl' => SORT_DESC, 'app.id' => SORT_DESC]);
            }

            $query
                ->joinWith(['problem', 'priority', 'appContent'])
                ->joinWith(['user' => function ($q) { $q->select(['id', 'login', 'username']);}])
                ->joinWith(['podr' => function ($q) { $q->select(['id', 'name']); }]);

            if ($project){
                $query->andWhere(['app.type' => 5]);
                $query
                    ->andFilterWhere(['or',
                        ['<>', 'app.status', 3],
                    ]);
            }else{
                /** для заявок со статусом 11 "Новая" выводим и обращения пользователей */
                if ($status == Status::STATUS_NEW_TICKET) {

                    //В зависимоти от отдела(ОБИТИС) вывдоиться обрашения для диспетчера
                    if (Yii::$app->user->identity->depart == 3) {
                        $query->andFilterWhere(['is not', 'appContent.buh', new \yii\db\Expression('null')])
                            ->andWhere(['app.agreed' => [1, null]]);
                    } else {
                        $query
                            ->orFilterWhere(['is', 'appContent.buh', new \yii\db\Expression('null')])
                            ->orFilterWhere(['=', 'app.agreed', 2]);
                    }

                    $query
                        ->andFilterWhere(['or',
                            ['is', 'app.type', new \yii\db\Expression('null')],
                            ['=', 'app.type', 1],
                            ['=', 'app.type', 4],
                            ['=', 'app.type', 3],
                            ['=', 'app.type', 6]
                        ]);
                } else {
                    if($status == Status::STATUS_AGREED){
                        $query->andFilterWhere(['=', 'app.type', 3]);
                    }else{
                        if ($search){
                            $query
                                ->andFilterWhere(['or',
                                    ['is', 'app.type', new \yii\db\Expression('null')],
                                    ['=', 'app.type', 1],
                                    ['=', 'app.type', 4],
                                    ['=', 'app.type', 3],
                                    ['=', 'app.type', 6]

                                ]);
                        }else{
                            $query
                                ->andFilterWhere(['or',
                                    ['is', 'app.type', new \yii\db\Expression('null')],
                                    ['=', 'app.type', 1],
                                    ['=', 'app.type', 4],
                                    ['=', 'app.type', 6]

                                ]);
                        }
                    }
                }

                if (isset($search)) {
                    switch ($search) {
                        case 'Все'://СНХРС
                            $query->andWhere(['=', 'status', $status])->limit(50);
                            break;
                        case 'sit'://НХРС
                            $query->andWhere(['=', 'status', $status])->andFilterWhere(['in', 'app.id_user', ArrayHelper::map(Login::find()->where(['depart' => 1])->all(), 'id', 'id')])->limit(250);
                            break;
                        case 'sap'://ЗСМиК
                            $query->andWhere(['=', 'status', $status])->andFilterWhere(['in', 'app.id_user', ArrayHelper::map(Login::find()->where(['depart' => 3])->all(), 'id', 'id')])->andWhere(['<>', 'app.type', 3])->limit(250);
                            break;
                        case 'ss'://ЗСМиК
                            $query->andWhere(['=', 'status', $status])->andFilterWhere(['in', 'app.id_user', ArrayHelper::map(Login::find()->where(['depart' => 4])->all(), 'id', 'id')])->limit(250);
                            break;
                        default:
                            $query = $query->joinWith(['appContent' => function ($q) {
                                $q->select(['id_app', 'content', 'id_fio', 'ip', 'buh'])->joinwith(['fio']);
                            }])
                                ->andWhere(['or',
                                        ['like', 'fio.name', $search],
                                        ['like', 'app.id', $search],
                                        ['like', 'appContent.content', $search],
                                        ['like', 'appContent.note', $search],
                                        ['like', 'appContent.dv', $search],
                                        ['like', 'login.username', $search]
                                    ]
                                )
                                ->andWhere(['=', 'status', $status]);
                    }
                } else {

                    $query->joinWith(['appContent' => function ($q) {
                        $q->select(['id_app', 'content', 'id_fio', 'ip', 'buh'])->joinwith(['fio']);
                    }]);

                    $query->andWhere(['=', 'status', $status]);

                    if (Yii::$app->user->can('Disp')) {

                    } elseif (Yii::$app->user->can('User')) {
                        if (!isset($user)) {
                            $query = $query->andWhere(['=', 'app.id_user', Yii::$app->user->id]);
                        }
                    }

                }
                if ($status == 3) {

                    $limit = Yii::$app->user->identity->count;
                    $query = $query->limit($limit);

                }
            }

            return $type == 0 ? $query->all() : $query->count();
        }

        /*
         * Меням стату заявки на просмотрено/НЕ просмотрено
         */
        public function appReview($id, $type = 1)
        {
//        $review = App::findOne($id);
//        if ($type == 1) {
//            if ($review->review == 1) {
////                if($review->id_user == isset($_SESSION['User']['id']) ? $_SESSION['User']['id'] : App::findOne($id)->id_user){
//                if ($review->id_user == Yii::$app->user->id) {
//                    $review->review = null;
//                    $review->save();
//                }
//            }
//        } else {
////            if($review->id_user == isset($_SESSION['User']['id']) ? $_SESSION['User']['id'] : App::findOne($id)->id_user){
//            if ($review->id_user == Yii::$app->user->id) {
//                $review->review = 1;
//                $review->save();
//            }
//        }
        }

        /*
         * Вывод тегов, с возможными решениями проблем
         */
        public function getTag($string)
        {
            // if ($string) {
            //     $tag = isset($string) ? $string : 'no tag';
            //     $apiData = json_decode(file_get_contents("http://sit.snhrs.ru/index.php/api/knowledge?string=" . substr(urlencode($tag), 0, 255)));
            //     return $apiData;
            // }
            return false;
        }


        /**
         * @param $id
         * @return bool
         * загрузка документов
         */
        public function upload($id)
        {
            $analog = new AppAnalog(['id_app' => $id]);
            $app_file = new AppFiles();

            foreach ($this->documentFiles as $file) {
                $folder = AppFiles::existsFolder() . '/' . $file->baseName . '_' . AppFiles::namePath() . '.' . $file->extension;
                $file->saveAs(AppFiles::DEFAULT_PATH . '/' . $folder);

                //Если заяка заведене пользователем, файл так же добавляем для этой заявки
                if ($analog->existsIdApp()){
                    $app_file->addFiles(Yii::$app->user->id, $analog->getByApp()->id_parent_app, $folder, null);
                }

                //Добавляем файл для заявки
                foreach ($id as $item) {
                    $app_file->addFiles(Yii::$app->user->id, $item, $folder, null);
                }
            }

            return true;
        }

        /*
         * По количеству переход в тексте меняем размер поля
         */
        public function contentRows($content)
        {
            $rows = substr_count($content, "\n");
            if ($rows + 1 < 6) {
                return 6;
            } elseif ($rows + 1 > 6 and $rows < 10) {
                return 10;
            } else {
                return 15;
            }
        }


        public function getProblemName()
        {
            return Problem::findOne($this->id_problem);
        }


        public function add()
        {
            try{
                $app = new App();
                $app->id_podr = $this->id_podr;
    //        $app->id_podr = $this->getDomainByName();
                $app->id_problem = $this->id_problem;
                $app->date_ct = MyDate::getTimestamp(date('Y-m-d H:i:s'));
                $app->id_priority = 2;
                $app->id_user = $this->id_user;
                $app->review = '1';
                $app->status = $this->status;
                $app->type = $this->type ? $this->type : null;
                $app->api_login = $this->api_login;
                $app->id_depart = $this->id_depart;
                $app->id_project = $this->id_project;

                $app->save();

            }catch(\Exception $ex){
                die('errorerrorerror');
            }
            return $app;
        }



        /**
         * @return int|mixed
         * Получамем АйДи подразделения по названию
         */
        public function getDomainByName()
        {
            $podr = Podr::find()->where(['visible' => 1])->all();
            foreach ($podr as $item) {
                if (mb_strtolower($item->name) == $this->id_podr) {
                    return $item->id;
                }
            }
            return 53;
        }


        /**
         * $id - АйДи заявки
         * Отмечаем заявку как глупую.
         */
        public function setStupid()
        {
            if (self::existsAppById()) {
                $model = self::findOne($this->id);
                $model->stupid = $model->stupid == 1 ? null : 1;
                $model->save();
                return true;
            }
            return false;
        }


        /**
         * $id - АйДи заявки
         * Отмечаем заявку как не выполнен
         */
        public function setExec()
        {
            if (self::existsAppById()) {
                $model = self::findOne($this->id);
                $model->no_exec = $model->no_exec == 1 ? null : 1;
                $model->save();
                return true;
            }
            return false;
        }


        public function existsAppById()
        {
            return App::find()->where(['id' => $this->id])->exists();
        }



        public function getSave( $result = 'Запись обновлена'){
            $status = false;
            $data = null;

            try{
                if($this->save()){
                    $status = true;
                    $data = $this;
                }else{
                    $error = '';
                    foreach ($this->errors as $key => $value) {
                        $error .= '<br>'.$key.': '.$value[0];
                    }
                    $result = $error;
                    echo "<pre>"; print_r($result ); echo "</pre>";
                }
            }catch(\Exception $ex){
                $result = 'Add Project. Error';
                echo "<pre>"; print_r($result ); echo "</pre>";

            }

            return [$status, $result, $data];
        }


        public function delAll($id){
            $analog = new AppAnalog(['id_app' => $id]);
            $analog->delIdApp();

            $remind = new AppRemind(['id_app' => $id]);
            $remind->delIdApp();

            $comment = new AppComment(['id_app' => $id]);
            $comment->delIdApp();

            $history = new History(['id_app' => $id]);
            $history->delIdApp();

            $history = new AppProject(['id_app' => $id]);
            $history->delIdApp();

            App::findOne($id)->delete();
            AppContent::findOne(['id_app' => $id])->delete();
        }


    }
