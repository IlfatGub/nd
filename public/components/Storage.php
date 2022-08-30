<?php
namespace  app\components;

use Yii;
use yii\base\Component;
use yii\helpers\Json;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;


class Storage extends Component{

    private $filename;

    /**
     * @param UploadedFile $file
     * @return bool
     */
    public function saveUploadedFile(UploadedFile $file){

        $path = $this->preparePath($file);
        if($path && $file->saveAs($path)){
            return $this->filename;
        }
    }

    /**
     * @param UploadedFile $file
     */
    public function preparePath(UploadedFile $file){
        $this->filename = $this->getFilename($file);

        $path = $this->getStoragePath() . $this->filename;

        $path = FileHelper::normalizePath($path);
        if(FileHelper::createDirectory(dirname($path))){
//            $file->saveAs($path);
            return $path;
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

    /**
     * @param Json $array
     * @return mixed
     */
    public function getFile($array){
        return isset($array) ? Json::decode($array) : false;
    }


}