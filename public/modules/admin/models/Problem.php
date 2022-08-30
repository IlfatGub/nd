<?php

namespace app\modules\admin\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 *
 * @property string name ФИО
 * @property int $problem Тип проблем
 * @property int $parent_id АйДи
 * @property int $type АйДи
 * @property int $id АйДи
 * @property int $visible АйДи
 * @property int $runtime Время на выполнение заявки под данной категории
 * @property int $time Время на исполнение
 * @property int $role Разделение по ролям
 * @property string $color
 * @property string $icon
 *
 * @property int $db Разделение по ролям
 * @property int $podr Разделение по ролям
 * @property int $user_id Разделение по ролям
 *
 * @property string $_name Время на исполнение
 * @property string $_name_time название вместе с временем исполнения
 *
 * $type = 1. Классификаци проблем по стандартам ИТИЛ
 *
 */

class Problem extends \yii\db\ActiveRecord
{

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UIPDATE = 'update';

    const SERVICE_ROLE_SAP = 3;

    public $_name;
    public $_name_time;

    public static function tableName()
    {
        return 'problem';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'trim'],
            [['name', 'color', 'icon'], 'string', 'max' => 255],
            [['parent_id', 'type', 'id', 'visible', 'runtime', 'time' ,'runtime','role'], 'integer'],
            [['user_id', 'db', 'podr'], 'integer'],
        ];
    }

    public function afterFind()
    {
        $this->_name = $this->name;

        $this->_name_time = isset($this->runtime) ? $this->name.'/'.$this->runtime : $this->name;

        $action  = Yii::$app->controller->action->id;

        if(($action == 'problem' or $action == 'service') and Yii::$app->controller->id == 'adm' and !isset($_GET['delete']) and !isset($_GET['type'])){
            $this->name = $this->getParentName() ? $this->getParentName().'/'.$this->name : $this->name;
        }

        if(($action == 'service2')){
            $this->name = $this->getParentName() ? $this->getParentName().'/'.$this->name : $this->name;
        }

//        if(Yii::$app->controller->action->id == 'index' and Yii::$app->controller->id == 'site' and isset($_GET['id'])){
//            $this->name = $this->getParentName() ? $this->getParentName().'/'.$this->name : $this->name;
//        }

        if(Yii::$app->controller->id == 'api'){
            $this->name = $this->getParentName() ? $this->getParentName().'/'.$this->name : $this->name;
        }
    }


    public function getRuntimeDisp(){
        return isset(self::findOne($this->id)->time) ? self::findOne($this->id)->time : History::TIME_1;
    }

    public function getRuntimeUser(){
        return isset(self::findOne($this->id)->runtime) ? self::findOne($this->id)->runtime : History::TIME_3;
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_UIPDATE] = ['name' , 'visible'];

        return $scenarios;
    }


    public function attributeLabels()
    {
        return [
            'name' => 'name',
        ];
    }

    /**
     * вывод основных проблем
     */
    public static function getProblemMain($prent_id = null){
        return self::find()->andWhere(['parent_id' => $prent_id, 'visible' => '1', 'type' => [1,2]])->orderBy(['role' => SORT_DESC])->all();
    }

    public static function getProblemMainAll($prent_id = null){
        return self::find()->andWhere(['visible' => '1', 'type' => '1'])->select(['name'])->orderBy(['role' => SORT_DESC])->all();
    }


    public static function getProblemMainSap($prent_id = null){
        return self::find()->andWhere(['parent_id' => $prent_id, 'visible' => '1', 'type' => '1'])->andFilterWhere(['=', 'role', 3])->all();
    }

    /**
     * Меняем Базу 1с(Проблему типо базы 1с)
     * если существует
     */
    public function setBase($base, $new_base){
        if ($model = $this::findOne(['name' => $base])){
            $model->name = $new_base;
            $model->save();
        }
    }

    public function name($id){
        return Podr::findOne($id)->name;
    }
    /*
     * Вывод объекта по id
     * @param integer $id
     * return string
     */
    public static function getById($id){
        return Problem::findOne($id);
    }
    /*
     * Вывод названия по id
     * @param integer $id
     * return string
     */
    public static function getNameById($id){
        if(isset($id)):
            return self::getById($id)->name;
        endif;
    }

    public static function getList(){
        return ArrayHelper::map(self::getListAll(), 'id', 'name');
    }

    public static function getListAll(){
        return Problem::find()->orderBy(['name' => SORT_ASC])->where(['visible' => 1])->all();
    }



    public static function getListItil($role = null){
        return ArrayHelper::map(
            Problem::find()
//                ->orderBy(['name' => SORT_ASC])
                ->where(['visible' => 1])
                ->andWhere(['role' => $role])
                ->all(), 'id', 'name');

    }

    /**
     * @param null $name
     * @param null $parent_id
     * @return int|string
     * количесво записей
     */
    public function exists($name = null, $parent_id = null){

        $this->parent_id = $this->parent_id ? $this->parent_id : null;

        $this->parent_id = $parent_id ? $parent_id : $this->parent_id;
        $this->name = $name ? $name : $this->name;

//        echo "<pre>"; print_r($this->name);
//        echo "<pre>"; print_r($this->parent_id); die();

        return Problem::find()
            ->where(['name' => $this->name])
            ->andWhere(['parent_id' => $this->parent_id])->exists();
    }

    /*
     * Добавляем запись
     * @var  $model  \app\modules\admin\models\Problem;
     */
    public function add($role = null){
        $pos = strripos($this->name, ';'); //разделитель для множественного добавления
        if($pos === false){
            if(!$this->exists($this->name, $this->parent_id)){
                $this->visible = 1;
                $this->type = 1;
                $this->role = $role;
                $this->save();
            }
        }else{
            $arr = explode(';', $this->name); // разбиваем строку в массив
            foreach ($arr as $item){
                if(!$this->exists($item, $this->parent_id)){
                    $new = new Problem();
                    $new->visible = 1;
                    $new->type = 1;
                    $new->role = $role;
                    $new->parent_id = $this->parent_id;
                    $new->name = strtolower($item);
                    $new->save();
                }
            }
        }
    }

    /*
     * Получаем название категории родителя
     */
    public function getParentName(){
        if (isset($this->parent_id)){
            return Problem::findOne($this->parent_id)->name;
        }

        return false;
    }


    /*
     * Получаем АйДИ категории родителя
     */
    public function getParentId(){
        if (isset($this->parent_id)){
            return Problem::findOne($this->parent_id)->id;
        }

        return false;
    }


    /**
     * @return Problem|bool|null
     * получаем запись по АйДи
     */
    public function getProblemById(){
        if (isset($this->id)){
            return Problem::findOne($this->id);
        }
        return false;
    }


}
