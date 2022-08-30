<?php

namespace app\modules\admin\models;

use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use Yii;

class Call extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'call';
    }

    public function rules()
    {
        return [
            [['count','date'], 'integer'],
        ];
    }


    /*
     * Доболвяем звонок(Справочная)
     */
    public function add()
    {
        $date = MyDate::getTimestamp(date('Y-m-d'));
        $call = Call::find()->where(['date' => $date])->count();
        if($call > 0){
            $i = isset(Call::findOne(['date' => $date])->count) ? Call::findOne(['date' => $date])->count : 0;
            $calls = Call::findOne(['date' => $date]);
            $calls->date = $date;
            $calls->count = $i + 1;
            $calls->save();
        }else{
            $call = new Call();
            $call->date = $date;
            $call->count = 1;
            $call->save();
        }

    }

    /*
     * Вывод звонков(справочная)
     */
    public function getCount(){
        return isset(Call::findOne(['date' => MyDate::getTimestamp(date('Y-m-d'))])->count) ? Call::findOne(['date' => MyDate::getTimestamp(date('Y-m-d'))])->count : 0;
    }
}
