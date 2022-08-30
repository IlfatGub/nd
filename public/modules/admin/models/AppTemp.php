<?php

namespace app\modules\admin\models;

use app\models\Sitdesk;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Напоминиаение заявки в ожидании
 *
 * @property int $id
 * @property int $id_temp    АйДи связанной таблицы
 * @property int $id_problem услуга
 * @property int $type       тип записи
 * @property int $visible    видимость
 *
 * $type = 1  БД 1с
 * $type = 2  Подразделение пользователя
 * $type = 3  Исполнитель
 */


class AppTemp extends \yii\db\ActiveRecord
{

    const SCENARIO_UPDATE = 'update';

    public static function tableName()
    {
        return 'appTemp';
    }

    public function rules()
    {
        return [
            [['id', 'id_temp', 'id_problem', 'type', 'visible'], 'integer'],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_UPDATE] = ['name' , 'visible'];

        return $scenarios;
    }


    public static function getType($type){
        if ($type == 'db') {
            $type = 1;
        }elseif ($type == 'podr') {
            $type = 2;
        }elseif ($type == 'user') {
            $type = 3;
        }
        return $type;
    }

    public function add(){
        if($this->tempExists() == 0){
            $this->type = self::getType($this->type);
            $this->save();
//            if(self::getType($this->type) <> 3){
                $this->setChild();
                $this->setParent();
                $this->setParent();
//            }
        }else{
            $temp = $this->getTemp();
            $temp->visible = null;
            $temp->save();
        }
    }

    public function setParent(){
        $parent = Problem::findOne($this->id_problem);
        if ($parent->parent_id){
            $this->id_problem = $parent->parent_id;
            if($this->tempExists() == 0){
                $new2 = new AppTemp();
                $new2->id_problem = $this->id_problem;
                $new2->id_temp = $this->id_temp;
                $new2->type = $this->type;
                $new2->save();
            }else{
                $temp = $this->getTemp();
                $temp->visible = null;
                $temp->save();
            }
        }
    }

    public function setChild(){
        $query = Problem::find()->where(['parent_id' => $this->id_problem]);
        if ($query->exists()){
            $child = $query->all();
            foreach ($child as $item){
                $this->id_problem = $item->id;
                if($this->tempExists() == 0){
                    $new2 = new AppTemp();
                    $new2->id_problem = $this->id_problem;
                    $new2->id_temp = $this->id_temp;
                    $new2->type = $this->type;
                    $new2->save();
                    $this->setChild();
                }else{
                    $temp = $this->getTemp();
                    $temp->visible = null;
                    $temp->save();
                }
            }
        }
    }

    public function tempExists(){
        return self::find()->where(['id_problem' => $this->id_problem,'id_temp' => $this->id_temp, 'type' => $this->type ])->count();
    }

    public function getTemp(){
        return self::find()->where(['id_problem' => $this->id_problem,'id_temp' => $this->id_temp, 'type' => $this->type ])->one();
    }

}
