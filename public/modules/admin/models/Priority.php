<?php

namespace app\modules\admin\models;

use Yii;
use yii\helpers\ArrayHelper;

class Priority extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'priority';
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

    public function getList(){
        return ArrayHelper::map(Priority::find()->all(), 'id', 'name');
    }

}
