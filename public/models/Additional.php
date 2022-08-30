<?php

namespace app\models;

use Yii;

/**
 * Дополнительная заявка, выводиться правее заявки.
 */

class Additional extends \yii\db\ActiveRecord
{
    public $imageFile;

    public static function tableName()
    {
        return 'additional';
    }

    public function rules()
    {
        return [
            [['id_app', 'id_user'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id_user' => 'Пользователь',
            'id_app' => 'Заявка',
        ];
    }

    /**
     * Получаем все допольнительные заявки для пользователя
     */
    public static function getAdditionalListByUser(){
        return self::find()->where(['id_user' => Yii::$app->user->id])->asArray()->all();
    }
}
