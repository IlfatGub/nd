<?php

namespace app\modules\admin\models;

use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use Yii;

class Fio extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'fio';
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

    /*
     * Выводим АйДи ФИО
     */
    public function getId($name){

        if(count(explode(' ',$name)) > 2){
            //Доболвяем ФИО в FioCase вместе с ФИО дательного падежа
            $fioCase = FioCase::find()->where(['name' => $name])->count();
            if($fioCase < 1){
                $model = new FioCase();
                $model->name = $name;
                $model->parent_id = null;
                $model->save();

                FioCase::setFioCase($name);
            }
        }

        //Доболвяем ФИО в Fio
        $count = Fio::find()->where(['name' => $name])->count();
        if($count > 0){
            return Fio::findOne(['name' => $name])->id;
        }else{
            $fio = new Fio();
            $fio->name = $name;
            $fio->save();
            return Fio::findOne(['name' => $name])->id;
        }
    }

    public function getList(){
        return Fio::find()->orderBy(['id' => SORT_ASC])->all();
    }

}
