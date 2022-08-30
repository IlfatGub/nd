<?php

namespace app\modules\admin\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "appSearchHistory".
 *
 * Модель для сохранения поиска
 * История поиска
 * Сохраняем поиск из logs, AD
 *
 * $type 1  - Logs
 * $type 2  - AD
 *
 * @property int $type источник запроса для поиска
 * @property int $id_user Пользователь
 *
 * @property string $search Текст запроса
 * @property string $text полноценный текст, после поиска
 */

class AppSearchHistory extends \yii\db\ActiveRecord
{

    public $arr;

    public static function tableName()
    {
        return 'appSearchHistory';
    }

    public function rules()
    {
        return [
            [['search', 'type',  'text'], 'string'],
            [['id_user'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'search' => 'Текст запроса',
            'type' => 'Тип',
            'id_user' => 'пользователь',
            'text' => 'Текст',
        ];
    }


    public static function getHistory(){
        return self::find()->where(['id_user' =>  Yii::$app->user->id])->orderBy(['type' => SORT_ASC])->limit(10)->all();
    }


    public static function history(){
        foreach (self::getHistory() as $item){
//            echo $item->type.' '.$item->search."<br>";
            echo $item->text;
        }
    }

    public static function record($search, $text, $type){
        $serachHistory = new self();
        $serachHistory->search = $search;
        $serachHistory->type = $type;
        $serachHistory->text = $text;
        $serachHistory->id_user = Yii::$app->user->id;
        $serachHistory->save();
    }




//    /*
//     * Добавялем элемент
//     */
//    public function add(array $arr = null){
//        $arr = array();
//        array_unshift($arr, ['content' => $this->search, 'type' => $this->type]);
//        $this->arr = $arr;
//
//
//        //Записываепм
//        self::queryAdd();
//    }

//    //Записываем , еслм имееться уже такая запись с пользователем то выбираем его
//    public function queryAdd(){
//        $m = self::find()->where(['id_user' => $this->id_user]);
//        if($m->exists()){
//            self::record($m->one());
//        }else{
//            self::record();
//        }
//    }

//    //Записываем
//    public function record($model = null){
//        if(!$model){
//            $model = new self();
//        }
//
//        array_unshift( json_decode($model->search), ['content' => $this->search, 'type' => $this->type]);
//        $model->search = json_encode($model->search);
//        $model->save();
//    }


    //Вывод ошибки при сохранени
    public function getSave($message){
        if($this->save()){
            \Yii::$app->session->setFlash(
                'message',
                [
                    'type'      => 'success',
                    'message'   => $message,
                ]
            );
            return true;
        }else{
            $error = '';
            foreach ($this->errors as $key => $value) {
                $error .= '<br>'.$key.': '.$value[0];
            }
            \Yii::$app->session->setFlash(
                'message',
                [
                    'type'      => 'danger',
                    'message'   => $error,
                ]
            );
            return false;
        }
    }

}
