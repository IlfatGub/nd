<?php

    namespace app\modules\admin\models;

    use Yii;

    /**
     * This is the model class for table "login".
     *
     * @property int $id_app
     * @property int $id_user
     * @property int $date_cl
     * @property int $id_fio
     * @property int $buh
     * @property int $review
     *
     *
     *
     * @property string $username ФИО
     * @property string $content
     * @property string $note
     * @property string $ip
     * @property string $phone
     * @property string $dv
     *
     */
    class AppContent extends \yii\db\ActiveRecord
    {

        public $username;

        public static function tableName()
        {
            return 'appContent';
        }


        public function rules()
        {
            return [
                [['id_app', 'id_user', 'date_cl', 'content', 'id_fio', 'ip', 'dv', 'phone', 'note', 'buh'], 'safe'],
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
                'buh' => 'Система 1C',
            ];
        }

        public function getFio()
        {
            return $this->hasOne(Fio::className(), ['id' => 'id_fio']);
        }

        public function getBuhg()
        {
            return $this->hasOne(Buh::className(), ['id' => 'buh']);
        }

        /*
         * Получаем запись по АйДи заявке
         */
        public static function getContentByApp($id_app)
        {
            return self::find()->where(['id_app' => $id_app])->joinWith('fio')->asArray()->all();
        }

        /*
         * Добовляем, или обновляем Контент
         * $type 1 - Добавляем
         * $type 2 - Обновляем
         */
        public function AppContentRecord($id_app = null, $id_user, $content, $id_fio, $ip, $phone, $dv, $type)
        {
            if ($type == 1) {
                $lastId = App::find()->limit(1)->orderBy(['id' => SORT_DESC])->one();  // Получаем АЙДИ последней заявки. Что бы присвоить его для Конетнта заявки.
                History::newHistory($lastId->id, $id_user, 1);   // Добавляем запись в историю заявки. "Создан"
                $appContetn = new AppContent();
                $appContetn->id_app = $lastId->id;
            } else {
                $appContetn = AppContent::findOne(['id_app' => $id_app]);
            }
            $appContetn->id_user = $id_user;
            $appContetn->content = $content;
            $appContetn->id_fio = Fio::getId($id_fio);
            $appContetn->ip = $ip;
            $appContetn->phone = $phone;
            $appContetn->dv = $dv ? $dv : null;
            $appContetn->save();
        }


        /**
         * Изменяем дату закрытия/завершения/выполенеиня заявки
         */
        public function setDateCl()
        {
            $this->date_cl = $this->getDateTimestamp();
            $this->save();
        }

        /**
         * Получаем дату в timestamp
         */
        public function getDateTimestamp()
        {
            return strtotime(date('Y-m-d H:i:s'));
        }

        public function add()
        {
            try{
                $appContetn = new AppContent();
                $appContetn->id_app = $this->id_app;
                $appContetn->id_user = $this->id_user;
                $appContetn->content = $this->content;
                $appContetn->note = isset($this->note) ? $this->note : "Заявка от пользователя";
                $appContetn->id_fio = Fio::getId($this->username);
                $appContetn->ip = $this->ip == '10.224.' ? '' : str_replace(',', '.', $this->ip);
                $appContetn->phone = $this->phone;
                $appContetn->dv = isset($this->dv) ? $this->dv : null;
                $appContetn->buh = $this->buh;
                $appContetn->review = 1;
                $appContetn->getSAve();

            }catch(\Exception $ex){
                die('errorerrorerror');
            }


        }


        public function getSAve()
        {
            try {
                if ($this->save()) {
                    $status = true;
                    $data = $this;
                } else {
                    $error = '';
                    foreach ($this->errors as $key => $value) {
                        $error .= '<br>' . $key . ': ' . $value[0];
                    }
                    $result = $error;
                    echo "<pre>";
                    print_r($result);
                    echo "</pre>";
                }
            } catch (\Exception $ex) {
                $result = 'Add Project. Error';
                echo "<pre>";
                print_r($result);
                echo "</pre>";
            }
        }
    }
