<?php

namespace app\modules\admin\models;

use Yii;

class HistoryStatus extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'history';
    }


    public function rules()
    {
        return [
            [['id', 'name'], 'safe'],
        ];
    }

}
