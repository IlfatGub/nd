<?php

namespace app\modules\admin\models;

use Yii;
use yii\helpers\ArrayHelper;

class Buh extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'buh';
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

    public static function getList(){
        return ArrayHelper::map(self::find()->where(['visible' => 1])->all(), 'id', 'name');
    }

    public static function getBuh(){
        return self::find()->where(['visible' => 1])->all();
    }

    public function del($id){
        $problem = new Problem();

        if(self::validateExistsId($id)) {
            $model = Buh::findOne($id);
            $problem = Problem::findOne(['name' => $model->name]);
            $model->visible = null;
            if($model->save()){
                $problem->visible = 0;
                $problem->save();
            }

        }
    }

    public function add($name){

        $problem_parent = 697; //Родитель для 1с баз в типах проблем

        //доблавяем базу 1с
        if(self::validateExistsName($name)){
            $model = self::findOne(['name' => $name]);
            $model->visible = 1;
            $model->save();
        }else{
            $newModel = new Buh();
            $newModel->name = $name;
            $newModel->visible = 1;
            $newModel->save();
        }

        //доблавяем базу 1с в тип проблем
        if($model = Problem::find()->where(['name' => $name, 'parent_id' => $problem_parent])->one()){
            $model->visible = 1;
            $model->save();
        }else{
            $problem = new Problem();
            $problem->name = $name;
            $problem->parent_id = $problem_parent;
            $problem->type = 1;
            $problem->visible = 1;
            $problem->save();
        }



    }

    public function validateExistsName($name){
        if(Buh::find()->where(['name' => $name])->exists()){
            return true;
        }
        return false;
    }

    public function validateExistsId($id){
        if(Buh::find()->where(['id' => $id])->exists()){
            return true;
        }
        return false;
    }

    public function findModel($id){
        return Buh::findOne($id);
    }
}
