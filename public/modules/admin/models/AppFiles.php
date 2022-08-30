<?php

namespace app\modules\admin\models;

use app\models\AppProject;
use Yii;


/**
 * Работа с Sitdesk
 *
 * @property string path

 * @property int id_app
 * @property int id_project
 * @property int id_user
 * @property int type Тип записи, в зависимости от заявки или проекта
 *
 */
class AppFiles extends \yii\db\ActiveRecord
{

    const DEFAULT_PATH = 'uploads/document';

    public static function tableName()
    {
        return 'appFiles';
    }


    public function rules()
    {
        return [
            [['id_app', 'id_user', 'path', 'id_project','type'], 'safe'],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id_app' => '№ Заявки',
            'id_user' => 'Создатель',
            'path' => 'Путь к файлу',
        ];
    }

    public function getPath($id)
    {
        return AppFiles::find()->where(['id_app' => $id])->all();
    }

    public function existsFolder()
    {
        $path = self::DEFAULT_PATH . '/' . date('Y-m-d');

        $year = date('Y');
        $date = date('m-d');
        $path = self::DEFAULT_PATH . '/' . $year;

        if (!file_exists(self::DEFAULT_PATH . '/' . $year)) {
            mkdir($path, 0777);
        }

        if (file_exists($path . '/' . $date)) {
            return $year . '/' . $date;
        } else {
            mkdir($path . '/' . $date, 0777);
        }
        return $year . '/' . $date;
    }

    /*
     * Добовляем к файлу рандом текс, для придания уникальности названия файла :)
     */
    public function namePath($length = 3)
    {
        $chars = 'weuoasmnvcxz';
        $numChars = strlen($chars);
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= substr($chars, rand(1, $numChars) - 1, 1);
        }
        return $string;
    }

    /*
     * Удаляем файл
     */
    public function unlinkFile($path)
    {
        $file = self::DEFAULT_PATH . '/' . $path;
        if (file_exists($file)) {
            unlink($file);
        }
    }

    public function findModel($id)
    {
        return AppFiles::findOne($id);
    }

    public static function existsFileByApp($id){
        return self::find()->where(['id_app' => $id])->exists();
    }



    /**
     * Добавляем запись
     * $type = 1 файлы по проекту
     * $type = 2 файлы по задаче
     * $type = 3 тз по проекту
     */
    public function addFiles($id_user, $id_app, $path, $type){
        $appFiles = new AppFiles();
        $appFiles->id_user = $id_user;
        $appFiles->id_app = $id_app;
        if ($type == 1){
            $appFiles->id_app = AppProject::getIdAppByParentId($id_app);
        }
        $appFiles->path = $path;
        $appFiles->type = $type;

        try{
            if($appFiles->save()){
            }else{
                $error = '';
                foreach ($appFiles->errors as $key => $value) {
                    $error .= '<br>'.$key.': '.$value[0];
                }
                echo "<pre>"; print_r($error );

            }
        }catch(\Exception $ex){
            echo "<pre>"; print_r('AppFiles. Error. Файл не добавлен');

        }

        $appFiles->save();
    }

}
