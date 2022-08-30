<?php

    namespace app\modules\admin\models;

    use Yii;
    use yii\helpers\ArrayHelper;

    date_default_timezone_set('Asia/Yekaterinburg');


    /**
     * Работа с Sitdesk
     *
     * @property string date время изменения
     * @property int comment АйДи на ком заявка после изменения
     * @property int id_user АйДи пользователя который внес изменения
     * @property int id_app АйДи заявки
     * @property int back вернуть заявку тому кто перенапривал после выполнения заявки
     * @property int runtime время выполния заявки, рассмотрения
     *
     *
     * @property int $date_do время выполния заявки, рассмотрения
     * @property int $id_problem время выполния заявки, рассмотрения
     * @property int $id_object время выполния заявки, рассмотрения
     * @property int $type время выполния заявки, рассмотрения
     *
     *
     * $type = 1 Создание заявки
     * $type = 2 Задано время заявке
     *
     * status 1 Создан
     * status 2 Изменен
     * status 3 Перенаправлен
     * status 4 В работу
     * status 5 Отложить
     * status 6 Закрыт
     * status 7 Закрыт(Через почту)
     * status 8 Изменен статус
     *
     */
    class History extends \yii\db\ActiveRecord
    {

        public $status;

        const STATUS_CREATE = 1;
        const STATUS_ASIDE = 2;
        const STATUS_CLOSE = 3;

        const STATUS_WORK = 1;
        const STATUS_HISTORY_ASIDE = 5; // Статус для истори. Отложено
        const STATUS_HISTORY_CLOSE = 6; //Статус для истории. Закрыто

        const STATUS_NEW = 11; // Статус новая
        const STATUS_AGREED = 14; // На согласовании

        const STATUS_HISTORY_WORK = 4;

        const STATUS_REDIRECT_AUTO = 9;
        const STATUS_WORK_AUTO = 10;
        const STATUS_EXEC_AUTO = 13;
        const STATUS_CONSIDERATION = 12; //статус, на рассмотретнии
        const STATUS_REDIRECT_NOW = 900; //статус , записываем как Перенаправлен

        const STATUS_ANALOG_NEW = 901;  //статус для аналогичной заявки, записываем как Новая
        const STATUS_ANALOG_CONS = 902; //статус для аналогичной заявки, записываем как На рассмотрении

        const TIME_START = 30;
        const TIME_1 = 60;
        const TIME_3 = 480;


        public static function tableName()
        {
            return 'appHistory';
        }

        public function rules()
        {
            return [
                [['id_app', 'date', 'date_do', 'comment', 'date', 'back', 'status', 'runtime'], 'integer'],
                [['id_object', 'id_problem', 'type'], 'integer'],
            ];
        }

        public function attributeLabels()
        {
            return [
                'id_app' => 'Подр./Отдел',
                'user_ct' => 'Создатель',
                'date_cl' => 'Дата закрытия',
                'content' => 'описание',
                'id_fio' => 'ФИО',
                'ip' => 'ip',
                'phone' => 'телефон',
                'dv' => 'Посмотрен',
            ];
        }

        public function getUser()
        {
            return $this->hasOne(Login::className(), ['id' => 'id_user']);
        }

        public function getUsercomment()
        {
            return $this->hasOne(Login::className(), ['id' => 'comment']);
        }

        public function getHistory()
        {
            return $this->hasOne(HistoryStatus::className(), ['id' => 'id_history']);
        }

        public function getProblem()
        {
            return $this->hasOne(Problem::className(), ['id' => 'id_problem']);
        }


        //проверка на наличие записи по столбцу id_app
        public function existsIdApp()
        {
            return self::find()->where(['id_app' => $this->id_app])->exists();
        }

        /**
         * @return int
         * Удаляем все связанное с заявкой
         */
        public function delIdApp()
        {
            if ($this->existsIdApp()) {
                return self::deleteAll(['id_app' => $this->id_app]);
            }
            return true;
        }

        public function getRuntime()
        {
            return self::find()->where(['id_app' => $this->id_app])->andWhere(['>', 'runtime', 1])->one();
        }

        //Проверяем на наличие упоминания о возварщении заявки обратно
        public function existsBack()
        {
            return self::find()->where(['id_app' => $this->id_app])->andWhere(['>', 'back', 0])->exists();
        }

        //Получаем АйДи пользователя кому необходимо вернуть заявку
        public function getUserBack()
        {
            $model = self::find()->where(['id_app' => $this->id_app])->andWhere(['>', 'back', 0])->orderBy(['date' => SORT_DESC])->one();
            return $model->back;
        }

        //изменяем статус
        public function setBackNull()
        {
            $model = self::find()->where(['id_app' => $this->id_app])->andWhere(['>', 'back', 0])->orderBy(['date' => SORT_DESC])->one();
            $model->back = null;
            $model->save();
        }


        /*
         * @var $model \app\modules\admin\models\App;
         */
        public static function first($model)
        {

            $object = Problem::getById($model->id_problem)->parent_id;

            $h = new History();
            $h->id_app = $model->id;
            $h->id_user = Yii::$app->user->id;
            $h->id_history = 11;
            $h->comment = $model->id_user;
            $h->id_problem = $model->id_problem;
            $h->id_object = isset($object) ? $object : null;
            $h->date = strtotime('now') - 10;

            $h->back = null;
            $h->date_do = null;
            $h->runtime = History::TIME_START;
            $h->type = null;
            $h->save();

            self::add($model, 11);
        }


        public static function getName($status)
        {
            switch ($status) {
                case 1 :
                    return 'Создан';
                    break;
                case 2 :
                    return 'Изменен';
                    break;
                case 3 :
                    return 'Перенаправлен';
                    break;
                case 4 :
                    return 'В работу';
                    break;
                case 5 :
                    return 'Отложить';
                    break;
                case 6 :
                    return 'Закрыт';
                    break;
                case 7 :
                    return 'Закрыт(Через почту)';
                    break;
                case 8 :
                    return 'Изменен статус';
                    break;
                case 9 :
                    return 'Перенаправлен(автоматически)';
                    break;
                case 10:
                    return 'В работу(автоматически)';
                    break;
                case 11:
                    return 'Новая';
                    break;
                case 12:
                    return 'На рассмотрение';
                    break;
                case 13:
                    return 'На рассмотрение (автоматически)';
                    break;
                case 14:
                    return 'На согласовании (автоматически)';
                    break;
            }
            return false;
        }


        public static function getNameByStatus($status)
        {
            switch ($status) {
                case 1:
                    return 4;
                    break;
                case 2:
                    return 5;
                    break;
                case 3:
                    return 6;
                    break;
                case 900:
                    return 3;
                    break;
                case 901:
                    return 11;
                    break;
                case 902:
                    return 12;
                    break;
            }
        }

        /*  */
        public static function getTimePriority($priority)
        {
            switch ($priority) {
                case 1:
                    return (float)0.5;
                    break;
                case 2:
                    return (float)1;
                    break;
                case 3:
                    return (float)1.5;
                    break;
            }
        }

        /**
         * @var $runtime History
         */
        public function getLastRuntime()
        {
            $history = History::find()
                ->where(['id_app' => $this->id_app])
                ->andWhere(['>', 'runtime', 1])
                ->orderBy(['date' => SORT_DESC])->one();

            return isset($history->runtime) ? $history->runtime : null;
        }

        /**
         * @var $runtime History
         */
        public function getLastRuntimeDate()
        {
            $history = History::find()
                ->where(['id_app' => $this->id_app])
                ->andWhere(['>', 'runtime', 1])
                ->orderBy(['date' => SORT_DESC])->one();

            return isset($history->date) ? $history->date : null;
        }


        /**
         * @return false|int
         * Получаем конечную время для выполнения завки
         */
        public function endDate()
        {

            $wh = new \app\modules\admin\models\MyDate();
            $_aside = 0; // промежуток времени, при котором заявка была в статусе "отложено";
//        $runtime = $this->getLastRuntime(); //время на выполнение заявки, в минутах
            $runtime = 960; //время на выполнение заявки, в минутах

            $clock = (int)($runtime / 60);
            $min = (int)(($runtime - $clock * 60) / 10);

            //Получаем время когда зявка была в статусе "Отложено"
            $aside = History::find()->where(['id_app' => $this->id_app])->andWhere(['id_history' => 5])->all();
            foreach ($aside as $item) {
                if ($item->date_do) {
                    $_aside = $item->date_do - $item->date;
                }
            }

            //проверяем на наличие статуса "На рассмотрении". Если его нет то конечную дату высчитываем от начала заведения заявки
            if (self::existsStatusConsideration()) {
                $date_from = date('Y-m-d H:i:s', self::getStatusConsideration() + $_aside);
            } else {
                $date_from = date('Y-m-d H:i:s', App::findOne($this->id_app)->date_ct + $_aside);
            }

            $date_till = $wh->addHours($date_from, $clock);

            if ($min > 0) {
                $date_from = date('Y-m-d H:i:s', $date_till);
                $date_till = $wh->addMins($date_from, $min);
            }


            return $date_till;
        }


        /**
         * @return mixed
         * Получаем первую дату статуса "На рассмотрении" из истории заявки
         */
        public function getStatusConsideration()
        {
            return self::find()->where(['id_app' => $this->id_app])->orderBy(['date' => SORT_DESC])->andWhere(['id_history' => 12])->one()->date;
        }

        /**
         * @return mixed
         * Получаем первую дату статуса "На рассмотрении" из истории заявки
         */
        public function existsStatusConsideration()
        {
            return self::find()->where(['id_app' => $this->id_app])->orderBy(['date' => SORT_DESC])->andWhere(['id_history' => 12])->exists();
        }


        /**
         * Получаем время перевода в работу
         * разница между статусом "На рассмотрении" и статусом "В работу"
         *
         */
        public function getTimeFromConsToWork()
        {


            //начальное время статуса в работе
            $s_query = self::find()->where(['id_app' => $this->id_app])->orderBy(['date' => SORT_ASC])->andWhere(['id_history' => 4]);

            //получваем дату
            $firtst_t = self::getStatusConsideration();

            if ($s_query->exists()) {
                $second_t = $s_query->one()->date; //получваем дату

                // время принятия на работу не должно превышать 120 минут
                if ($firtst_t and $second_t) {
                    if (($second_t - $firtst_t) > 3600 * 2)
                        return 1;
                }
            } else {
                //Если заявка не переведена "В работу" то проверяем с текущей датой.
                if ($firtst_t + 3600 * 2 < strtotime("now"))
                    return 1;
            }

            return 0;
        }


        /**
         * @var $model \app\modules\admin\models\App;
         * @var $status =  первоначальный статус заявки
         * @var $_status = статус на который была изменена заявка
         * @var $old_problem = первоначальный тип услуги
         * @var $back = возвращаем заявку
         */
        public static function add($model, $status = null, $old_problem = null, $back = null)
        {

            $_status = $model->status;
            $runtime = null;
            $type = null;

            /* Если заявка в ожидании помечаем ее, это время не должно учитываться, при учитывании общего времени */
            if ($_status == 2) {
                $type = 10;
            }

            /* Изменяем статус для ИСТОРИИ  */
            if (in_array($_status, [1, 2, 3])) {
                $_status = self::getNameByStatus($_status);
            }
            if (in_array($status, [900, 901, 902])) { //Перенаправление заявки
                $_status = self::getNameByStatus($status);
                $back = null;
            }

            /**
             * Добавляем время выполнения заявки
             * если заявка переходит из статуса НОВАЯ в статус "На рассмотрение"
             * если специалист изменил тип УСЛУГИ
             */
            if ($status == self::STATUS_NEW or ($model->id_problem <> $old_problem and $old_problem <> null) or $status == self::STATUS_ANALOG_CONS) {
                if ($status != self::STATUS_REDIRECT_NOW and $status != self::STATUS_ANALOG_NEW) {
                    $problem = new Problem();
                    $problem->id = $model->id_problem;
                    $runtime = $problem->getRuntimeUser() * History::getTimePriority($model->id_priority);
                }
            }

            /* Последняя запись истории по данной заявке */
            $lastHistory = History::find()->limit(1)->where(['id_app' => $model->id])->orderBy(['date' => SORT_DESC])->one();

            /* Получаем объект УСЛУГИ */
            $object = Problem::getById($model->id_problem)->parent_id;

            $h = new History();
            $h->id_app = $model->id;
            $h->id_user = Yii::$app->user->id ? Yii::$app->user->id : 50;
            $h->id_history = $_status;
            $h->comment = $model->id_user;
            $h->id_problem = $model->id_problem;
            $h->id_object = isset($object) ? $object : null;
            if ($status == self::STATUS_ANALOG_NEW) {
                $h->date = strtotime('now') + 10;
            } else {
                $h->date = strtotime('now');
            }

            $h->back = $back;
            $h->date_do = null;
            $h->runtime = $runtime;
            $h->type = $type;

            if ($h->save() and $lastHistory) {
                $lastHistory->date_do = $h->date;
                $lastHistory->save();
            }
        }


        public function setHistory()
        {
            $history = new History();
            $history->id_app = $this->id_app;
            $history->id_user = isset($this->id_user) ? $this->id_user : App::findOne($this->id_app)->id_user;
            $history->id_history = $this->status;
            $history->comment = $this->comment;
            $history->date = isset($this->date) ? $this->date : MyDate::getTimestamp(date('Y-m-d H:i:s'));
            $history->back = $this->back;
            $history->runtime = $this->runtime;
            $history->save();
        }

        public static function newHistory($id, $user, $status, $login = null, $back = null, $runtime = null)
        {
            $history = new History();
            $history->id_app = $id;
            $history->id_user = isset($user) ? $user : App::findOne($id)->id_user;
            $history->id_history = $status;
            $history->comment = $login;
            $history->date = MyDate::getTimestamp(date('Y-m-d H:i:s'));
            $history->back = $back;
            $history->runtime = $runtime;
            $history->save();
        }


        /**
         * @var $history History;
         */
        public static function UserStat($history)
        {
            $app_stat = array();
            $app_stat_full = array();
            $app_full = array();

            $count_app = ArrayHelper::map($history, 'id_app', 'id_history', 'user.username');

            $_date = ArrayHelper::map($history, 'date', 'date_do', 'id_app'); //группируем даты по АйДи заявке
            $_history = ArrayHelper::map($history, 'date', 'id_history', 'id_app'); //группируем дату и статус по АйДи заявке

            foreach ($_date as $key => $item) {
                $sum = 0;
                foreach ($item as $date_to => $date_do) {
                    if ($date_do) {
                        $date = new MyDate();
                        $date->date_to = $date_to;
                        $date->date_do = $date_do;

                        $status = History::getName($_history[$key][$date_to]); // наименование Статуса

                        $app_stat_full[$status][] = $date->getSum(); //Каждый статус по отдельности
                        $app_stat[$status] = isset($app_stat[$status]) ? $app_stat[$status] + $date->getSum() : $date->getSum(); // общее время каждого статуса

                        $sum = $sum + $date->getSum();
                    }
                }
                $app_full[$key] = [$app_stat_full, $app_stat];
            }
            return $app_full;
        }

        /**
         * @param $s1
         * @param $s2
         * @return float
         *
         * $s1 Общее количество заявок
         * $s2 Общее количество просроченных заявок
         *
         * Процент заявок,
         * выполненных в рамках установленных сроков согласно регламенту
         * «Регламент - Оказание услуг ИТ и связи ОБИТиС».
         */
        public function kpi($s1, $s2)
        {
            $r = $s1 / 100;
            $r2 = $s1 - $s2;
            return ceil($r2 / $r);
        }


        //Вывод ошибки при сохранени
        public function getSave($message = null)
        {
            try {

                if ($this->save()) {

                } else {
                    $error = '';
                    foreach ($this->errors as $key => $value) {
                        $error .= '<br>' . $key . ': ' . $value[0];
                    }
                    echo "<pre>";
                    print_r($error);
                }
            } catch (\Exception $ex) {
                print_r($ex);
            }
        }

    }
