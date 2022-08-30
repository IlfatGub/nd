<?php

namespace app\modules\admin\models;


class Status extends \yii\db\ActiveRecord
{


    const STATUS_ACTIVE_APP = 500;
    const STATUS_NEW_TICKET = 11;   // новая заявка
    const STATUS_AGREED = 14;       // на согласовании

    public static function tableName()
    {
        return 'status';
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
            'name' => 'name',
        ];
    }

    public static function Name($id){
        switch ($id){
            case 1: return 'В работе'; break;
            case 2: return 'В ожидании'; break;
            case 3: return 'Закрыт'; break;
            case 11: return 'Новая'; break;
            case 12: return 'На рассмотрение'; break;
            case 14: return 'На согласовании'; break;
        }
    }


    public static  function getStatus(){
        return [
            1 => 'В работе',
            2 => 'В ожидании',
            3 => 'Закрыт',
            11 => 'Новая',
            12 => 'На рассмотрение',
            14 => 'На согласовании',
        ];
    }


    public static function getStatusActive(){
        return [1, 2, 11, 12];
    }


    public static  function getStatusReport(){
        return [
            1 => 'В работе',
            2 => 'В ожидании',
            3 => 'Закрыт',
            11 => 'Новая',
            12 => 'На рассмотрение',
            14 => 'На согласовании',
            self::STATUS_ACTIVE_APP => 'Не закрытые',
        ];
    }


}
