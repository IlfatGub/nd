<?php

namespace app\models;

use yii\base\Component;
use yii\base\Model;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;


class Storage extends Component{

    static public $filename;


    /**
     * @param UploadedFile $file
     * @return bool
     */
    public function saveUploadedFile(UploadedFile $file){
        $path = self::preparePath($file);

        if($path && $file->saveAs($file)){
            return $this->filename;
        }
        return false;
    }

    /**
     * @param UploadedFile $file
     */
    public function preparePath(UploadedFile $file){
        $this->filename =  self::getFilename($file);

        $path = $this->getStoragePath() . $this->filename;

        $path = FileHelper::normalizePath($path);
        if(FileHelper::createDirectory(dirname($path))){

        }
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function getFilename(UploadedFile $file){
        $hash = sha1_file($file->tempName);

        $name = substr_replace($hash, '/', 2, 0);
        $name = substr_replace($name, '/', 5, 0);

        return $name . '.' . $file->extension;
    }

    /**
     * @return bool|string
     */
    public function getStoragePath(){
        return Yii::getAlias(Yii::$app->params['storagePath']);
    }

}