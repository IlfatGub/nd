<?php

namespace app\models;


/**
 * Пользователи
 *
 * @property int id
 * @property int t1
 * @property int t2
 * @property int t3
 * @property int t4
 * @property int t5
 * @property int t6
 * @property int t7
 * @property int date
 * @property int type
 *
 */



use Intervention\Image\ImageManager;
use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\web\UploadedFile;

class About extends \yii\db\ActiveRecord
{
    public $imageFile;

    public static function tableName()
    {
        return 'documentation';
    }

    public function rules()
    {
        return [
            [['name', 'description'], 'required'],
            [['name'], 'string'],
            [['name'], 'unique'],
            [['description'], 'string'],
            [['role'], 'string'],
            [['image'], 'file', 'maxFiles' => 10],
            [['date_ct'], 'safe'],
//            [['image'], 'file', 'extensions' => 'png, jpg, jpeg, gif'],
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



    /**
     * Resize image if needed
     */
    public function resizePicture(UploadedFile $file)
    {
        $width = Yii::$app->params['imageSize']['maxWidth'];
        $height = Yii::$app->params['imageSize']['maxHeight'];


        $manager = new ImageManager(array('driver' => 'imagick'));

        $image = $manager->make($file->tempName);     //    /tmp/11ro51

        $image->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->save();        //    /tmp/11ro51
    }

}
