<?php

namespace app\modules\admin\models;

use app\models\Sitdesk;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Аналогичные заявки
 *
 * @property int $id
 * @property int $id_parent_app
 * @property int $id_app
 *
 */


class AppAnalog extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'appAnalog';
    }


    public function rules()
    {
        return [
            [['id_app', 'id_parent_app', 'id'], 'integer'],
        ];
    }


    public function exists(){
         if($this->appExitsts()  or $this->parentExists())
             return true;
         return false;
    }



    public function getMainId($id){

        if (self::find()->where(['id_app' => $id])->exists()){
            return self::findOne(['id_app' => $id])->id_parent_app;
        }else{
            return $id;
        }
    }


    /**
     * Меняем статус обращения
     * при открывании закрытой заявки по обращению, меняем статус обращения "В работу"
     */
    public function setAnalogStatusActive(){
        if ($this->existsIdApp()){
            $id_parent_app = self::findOne(['id_app' => $this->id_app])->id_parent_app;

            $model = App::findOne($id_parent_app);
            if(isset($model->api_login)){
                $model->status = 1;
                $model->save();
            }
        }
    }


    /**
     * Меняем статус обращения
     * если все заявки по обращению закрыты
     * меняем статус обращения на выполнено
     */
    public function setAnalogStatus(){
        $res = true;

        if ($this->existsIdApp()){
            $id_parent_app = self::findOne(['id_app' => $this->id_app])->id_parent_app;

            $query = self::find()->where(['id_parent_app' => $id_parent_app]);

            $count = $query->count();

            if ($count > 0){
                if ($count > 1){
                    $model = $query->all();
                    $id_apps = ArrayHelper::map($model, 'id_app', 'id_app');
                    unset($id_apps[$this->id_app]);
                    $apps = App::find()->where(['id' => $id_apps])->all();
                    foreach ($apps as $item){
                        if($item->status <> 3)
                            $res = false;
                    }
                }

                if ($res){
                    $model = App::findOne($id_parent_app);
                    if ($model->type == 3){
                        $model->status = 3;
                        $model->save();

                        //Добавляем дату закрытия обарашения
                        $content = AppContent::findOne(['id_app' => $id_parent_app]);
                        $content->date_cl = strtotime("now");
                        $content->save();

                        //Отправляем письмо пользователю
                        Sitdesk::sendUserMail($id_parent_app, $model->no_exec);
                    }
                }
            }
        }

    }

    public function getParentAnalogApp(){
        $id = $this->id_app;
        if ($this->parentExists()){
            $parent = $this->getParent();
            $_parent = ArrayHelper::map($parent, 'id_parent_app','id_parent_app');
            $_app = ArrayHelper::map($parent, 'id_app','id_app');
            foreach ($parent as $item){
                $this->id_app = $item->id_app;
                if($this->parentExists()){
                    $_app = array_merge($_app, ArrayHelper::map($this->getParent(), 'id_app','id_app'));
                }
            }
            $this->id_app = $id;
            $_parent =  array_merge($_parent, $_app);
            return  $_parent;
        }else{
            return null;
        }
    }

    public function getAnalogApp(){
        $id = $this->id_app;
        if ($this->existsIdApp()){
            $app = $this->getApp();
            $_parent = ArrayHelper::map($app, 'id_parent_app','id_parent_app');
            $_app = ArrayHelper::map($app, 'id_app','id_app');
            foreach ($app as $item){
                $this->id_app = $item->id_parent_app;
                if($this->parentExists()){
                    $_parent = array_merge($_parent, ArrayHelper::map($this->getParent(), 'id_app','id_app'));
                }
            }
            return array_merge($_parent, $_app);
        }else{
            return null;
        }
    }

    public function getAnalog(){
        $parent = $this->getParentAnalogApp();
        $app = $this->getAnalogApp();

        if($parent and $app){
            $result = array_merge($parent, $app);
        }elseif($parent){
            $result = $parent;
        }elseif($app){
            $result = $app;
        }else{
            $result = null;
        }
        if (!empty($result)){
            return array_unique($result);
        }else{
            return false;
        }

    }

    //проверка на наличие записи по столбцу id_app
    public function existsIdApp(){
        return self::find()->where(['id_app' => $this->id_app])->exists();
    }

    //удаляем все вхождения по id_app
    public function delIdApp(){
        if ($this->existsIdApp()){
            self::deleteAll(['id_app' => $this->id_app]);
        }
        return true;
    }

    public function parentExists(){
        return self::find()->where(['id_parent_app' => $this->id_app])->exists();
    }
    public function getParent(){
        return self::find()->where(['id_parent_app' => $this->id_app])->all();
    }

    public function appExitsts(){
        return self::find()->where(['id_app' => $this->id_app])->exists();
    }
    public function getApp(){
        return self::find()->where(['id_app' => $this->id_app])->all();
    }

    public function getByApp(){
        return self::findOne(['id_app' => $this->id_app]);
    }

    public function getByIdAppAll(){
        return self::find()->where(['id_parent_app' => $this->getByApp()->id_parent_app])->all();
    }

    public function getByIdParentAll(){
        return self::find()->where(['id_parent_app' => $this->id_app])->all();
    }

}
