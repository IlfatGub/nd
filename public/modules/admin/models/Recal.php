<?php

namespace app\modules\admin\models;

use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use Yii;

/**
 * This is the model class for table "recal".
 *
 * Напомнинаия, Справочник
 *
 * @property int $id
 * @property int $type Значения для настройки
 *
 * $type = 0 - Default
 * $type = 1 - Скрываем шапку
 *
 * @property string $text Текст
 * @property string $id_user   АйДи того кто добавил
 *

 */

class Recal extends \yii\db\ActiveRecord
{

    public $login;
    public static function tableName()
    {
        return 'recal';
    }

    public function rules()
    {
        return [
            [['id_user','text'], 'safe'],
            [['text'], 'string', 'max' => '255'],
            [['recal'], 'integer'],
        ];
    }


    public function attributeLabels()
    {
        return [
            'text' => 'text',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(Login::className(), ['id' => 'id_user']);
    }

    /*
     * Вывод напоминаний пользователя.
     */
    public function recalList(){
        return  Recal::find()->where(['id_user' => Yii::$app->user->id])->orderBy(['date' => SORT_DESC])->select(['text', 'id'])->all();
    }
}
