<?php

namespace app\modules\admin\models;

use Yii;
use yii\helpers\ArrayHelper;

class Podr extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'podr';
    }

    public function rules()
    {
        return [
            [['name'], 'safe'],
        ];
    }


    public function attributeLabels()
    {
        return [
            'name' => 'namr',
        ];
    }

    public static function getList(){
        return Podr::find()->orderBy(['sortable' => SORT_ASC])->where(['visible' => 1])->all();
    }

}
