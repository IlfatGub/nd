<?php

namespace app\models;

use Yii;
use yii\base\Model;

class Documentation extends Model
{

    public static function tableName()
    {
        return 'documentation';
    }



    public function rules()
    {
        return [
            [['name', 'description'], 'required'],
            [['name'], 'string'],
            [['description'], 'string'],
            [['image'], 'file', 'maxFiles' => 10],
        ];
    }



    public function attributeLabels()
    {
        return [
            'name' => 'Название',
            'description' => 'Описание',
            'image' => 'Изображение',
        ];
    }



}
