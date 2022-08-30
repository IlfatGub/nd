<?php

namespace app\modules\admin\models;

use Yii;
use yii\helpers\ArrayHelper;


/**
 * @param $problem "Тип проблемы соответствующией к слову, при нажати автоматически выбираеться тип приблемы в поле "Тип проблемы""
 * @param $name "Слова при нажатии которых, в поле "Описание" добавляется соответсвующее слово в конец"
 */

class Help extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'help';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'unique'],
            [['parent_id', 'problem'], 'integer'],
        ];
    }

    /*
     * Вывод списка слов без дочерних оюъектов
     */
    public static function getHelp(){
        return ArrayHelper::map(Help::find()->where(['parent_id' => null])->all(),'id','name');
    }

    /*
     * Вывод объектов по parent_id
     * @param integer $parent_id
     * return array
     */
    public static function getHelpByParentId($parent_id){
        return Help::find()->where(['parent_id' => $parent_id])->all();
    }


    /*
     * Вывод объекта по id
     * @param integer $id
     * return string
     */
    public static function getById($id){
        return Help::findOne($id);
    }

    /*
     * Вывод имени по id
     * @param integer $id
     * return string
     */
    public static function getNameById($id){
        if(isset($id)):
            return self::getById($id)->name;
        endif;
        return false;
    }

    /*
     * Удаляем объект по id
     * @param integer $id
     * return string
     */
    public static function deleteById($id){
        if(isset($id)):
            if(self::getById($id)->delete()) :
                self::deleteParentIdById($id);
            endif;
        endif;
        return false;
    }

    /*
     * Очищаем parent_id, если был удален родитель
     * @param integer $id
     */
    public static function deleteParentIdById($id){
        if(Help::find()->where(['parent_id' => $id])->exists()):
            Help::updateAll(['parent_id' => null], ['parent_id' => $id]);
        endif;
    }


    /*
     * Вывод объекта по name
     * @param string $name
     * return string
     */
    public static function getHelpByName($name){
        return Help::findOne(['name' => $name]);
    }


    /*
     * Проверка на наличие дочерних объектов
     * @param $id
     * return bool
     */
    public static function validateParent($id){
        $count = Help::find()->where(['parent_id' => $id])->count();
        if($count > 0):
            return true;
        else:
            return false;
        endif;
    }



}
