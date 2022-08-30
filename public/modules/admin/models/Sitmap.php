<?php
namespace app\modules\admin\models;

use Yii;
use yii\helpers\ArrayHelper;



class Sitmap extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'sitmap';
    }


    public function rules()
    {
        return [
            [['coordinates', 'name'], 'required'],
            [['name', 'address'], 'string', 'max' => 100],
            [['coordinates'], 'string', 'min' => 16, 'max' => 100],
            [['description'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'coordinates' => 'Координаты',
            'name' => 'Наименование',
            'address' => 'Адрес',
            'description' => 'Описание',
        ];
    }

}
