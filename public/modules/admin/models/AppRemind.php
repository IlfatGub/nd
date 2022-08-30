<?php

namespace app\modules\admin\models;

use app\models\Sitdesk;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Напоминиаение заявки в ожидании
 *
 * @property int $id
 * @property int $id_app
 * @property int $date
 * @property int $time
 * @property int $serial_app
 * @property int $user_comment
 * @property int $in_work
 * @property string $comment
 *
 */
class AppRemind extends \yii\db\ActiveRecord
{
    public $comment;
    public $datetime;

    public static function tableName()
    {
        return 'appRemind';
    }

    public function rules()
    {
        return [
            [['id_app', 'date', 'time', 'serial_app', 'user_comment', 'in_work'], 'integer'],
            [['id_app', 'time', 'serial_app', 'user_comment', 'in_work'], 'default', 'value' => null],
            [['comment', 'datetime'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'time' => 'Время напоминания',
            'serial_app' => 'Последовательная заявка',
            'in_work' => 'Перевести в работу',
            'user_comment' => 'Коменнтарий пользователя',
        ];
    }


    //проверка на наличие записи по столбцу id_app
    public function existsIdApp()
    {
        return self::find()->where(['id_app' => $this->id_app])->exists();
    }

    public function getByIdApp()
    {
        return self::find()->where(['id_app' => $this->id_app])->all();
    }


    //проверка на наличие записей для напоминаний по времени
    public function existsTime()
    {
        return self::find()->where(['not', ['time' => null]])->exists();
    }

    //проверка на наличие записи по столбцу serial_app
    public function existsSerialApp()
    {
        return self::find()->where(['serial_app' => $this->serial_app])->exists();
    }

    public function getByIdSerialApp()
    {
        return self::find()->where(['serial_app' => $this->serial_app])->one();
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

    /**
     * @return int
     * Удаляем последовательную заявку
     */
    public function delSerialApp()
    {
        if ($this->existsSerialApp()) {
            return self::deleteAll(['serial_app' => $this->serial_app]);
        }
        return true;
    }


    /**
     * Напоминаине после завершения последовательной заявки
     * Перводим заявку в Работу если указано
     * Оповещаем письмом
     */
    public function SerialRemind()
    {
        if ($this->existsSerialApp()) {
            $remind = AppRemind::findOne(['serial_app' => $this->serial_app]);
            $model = App::findOne($remind->id_app);

//            $app = new App(['id' => $remind->id_app]);
//            $app = App::findOne([$remind->id_app]);

//            echo "<pre>"; print_r($remind );
//            echo "<pre>"; print_r($remind ); die();

            if ($remind->in_work == 1) {

                $model->status = 1;
                $model->save();

                History::add($model, History::STATUS_WORK);

//                $app->setStatusWork(); // меняем статус последовательной заявик
                $this->delSerialApp(); // удаляем изнапоминания
            }

//            die('aasd');
//            Sitdesk::appMail($remind->id_app, $model->getIdUser());
            Sitdesk::mailRemind($remind->id_app, 2);
        }
    }


    /**
     * Напоминаине после добавления пользователем комментария
     * Оповещаем письмом
     */
    public function commentRemind()
    {
        if ($this->existsSerialApp()) {
            $remind = $this->getByIdApp();
            foreach ($remind as $item) {
                $app = new App(['id' => $item->id_app]);
                if ($item->user_comment == 1) {
                    Sitdesk::mailRemind($item->id_app);
                }
            }
        }
    }


    public function setAnalogRemind(){

    }


    /**
     * @return int
     * меняем статус заявки последовательной заявки
     */
    public function setSerialApp()
    {
        if ($this->existsSerialApp()) {
            $remind = $this->getByIdSerialApp();

            $app = App::findOne($remind->id_app);

            Sitdesk::mailRemind($remind->id_app, 2);

            if ($remind->in_work == 1) {
                $app->setStatusWork(); // меняем статус заявки
                self::findOne($remind->id)->delete(); // удаляем изнапоминания
            }
        }
        return true;
    }


    /**
     * Оповещение по времени
     * Через каждый выбранный час
     * Если не выбран пункт "Перевод в работу", напоминает по циклу пока не выйдят из ожидания
     */
    public function getTimeRemind()
    {
        if ($this->existsTime()) {
            //Берем все напоминания
            $remind = AppRemind::find()->where(['not', ['time' => null]])->all();

            foreach ($remind as $item) {

                $app = new App(['id' => $item->id_app]);

                if ($item->time > 0) {
                    // Время создания напоминаяни + часы
                    $time = $item->date + $item->time * 60 * 60;

                    if ($time < strtotime('now')){
                        $r = self::findOne($item->id);
                        $r->date = strtotime('now'); // после напоминиая устанавливаем для напоминиая текущую дату, что бы повтороно напомнить через указанное время
                        $r->save();

                        Sitdesk::mailRemind($item->id_app, 2); //отправка письма

                        if ($item->in_work == 1) {
                            $app->setStatusWork(); // меняем статус заявки
                            self::findOne($item->id)->delete(); // удаляем изнапоминания
                        } else {
//                            $rem = AppRemind::findOne(['id_app' => $item->id_app]);
//                            $rem->date = $rem->date + $item->time * 60 * 60;
//                            $rem->save();
                        }
                    }

                }
            }
        }
    }



    /**
     * Оповещение о просроченных заявках
     */
    public function getOverdueRemaind()
    {

        $model = App::find()->joinWith(['appContent'])->select(['app.id', 'app.status', 'app.id_user', 'appContent.date_cl'])->where(['app.status' => 1])->orWhere(['app.status' => 12])->asArray()->all();
        $users = ArrayHelper::map($model, 'id', 'id', 'id_user');

        $res = array();
        foreach ($users as $id_user => $apps) {
            foreach ($apps as $app) {
                $history_time = new History(['id_app' => $app]);
                $end_date = $history_time->endDate();

                if ($end_date < strtotime('now')){
                    $res[$id_user][] = $app;
                }
            }
        }

        echo "<pre>";
        print_r($res);

//        print_r($user);


        die('просроченные заявки');
        if ($this->existsTime()) {
            $remind = AppRemind::find()->where(['not', ['time' => null]])->all();

            foreach ($remind as $item) {
                $app = new App(['id' => $item->id_app]);
                if ($item->time > 0) {
                    echo '12312';

                    $time = $item->date + $item->time * 60 * 60;
                    $r = self::findOne($item->id);
                    $r->date = strtotime('now');
                    $r->save();

                    Sitdesk::appMail($item->id_app, $app->getIdUser());

                    if ($item->in_work == 1) {
                        $app->setStatusWork(); // меняем статус последовательной заявик
                        self::findOne($item->id)->delete(); // удаляем изнапоминания
                    } else {
                        $rem = AppRemind::findOne(['id_app' => $item->id_app]);
                        $rem->date = $rem->date + $item->time * 60 * 60;
                        $rem->save();
                    }
                }
            }
        }
    }

}
