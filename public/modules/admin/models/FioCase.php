<?php

namespace app\modules\admin\models;

use app\controllers\NameCaseLib\Library\NCLNameCaseRu;
use app\controllers\NameCaseLib\Library\NCLs\NCL;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use Yii;

class FioCase extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'fio_case';
    }

    public function rules()
    {
        return [
            [['name'], 'string'],
            [['parent_id'], 'integer']
        ];
    }


    public function attributeLabels()
    {
        return [
            'name' => 'name',
        ];
    }

    /*
     * Перевод ФИО в Дательный падеж
     */
    public function getFioCase($fio){
        $case = new NCLNameCaseRu();
        $gender = $case->genderDetect($fio);
        if (isset($gender)) {
            if ($gender == NCL::$MAN) {
                $fio_case = $case->q($fio, NCL::$DATELN, NCL::$MAN);
            } else {
                $fio_case = $case->q($fio, NCL::$DATELN, NCL::$WOMAN);
            }
            return $fio_case;
        }
        return false;
    }

    public function getId($fio){
        return FioCase::findOne(['name' => $fio])->id;
    }

    public function getParentId($fio){
        if(isset(FioCase::findOne(['name' => $fio])->parent_id)){
            return FioCase::findOne(['name' => $fio])->parent_id;
        }
        return false;
    }

    public function getName($id){
//        if(FioCase::findOne($id)->parent_id){
//            return FioCase::findOne(FioCase::findOne($id)->parent_id)->name;
//        }
        return  FioCase::findOne($id)->name;
    }

    public function setFioCase($fio){
        $model = new FioCase();
        $model->name = self::getFioCase($fio);
        $model->parent_id = self::getId($fio);
        $model->save();
    }

    /*
     * Запись ФИО, если его нет
     */
    public function setNewName($name){
        if(!self::validateName($name)){
            $fio = new FioCase();
            $fio->name = $name;
            $fio->parent_id = null;
            $fio->save();

            self::setFioCase($name);
        }
    }

    /*
     * Проверка на наличие ФИО в БД
     */
    public function validateName($name){
        if (FioCase::findOne(['name' => $name])){
            return true;
        }
        return false;
    }

    /**
     * Получаем домен, к которму относиться ФИО.
     * @param string $fio, Кондратьев Сергей Николаевич
     * @return string data, SNHRS\42ksn
     */
    public static function getDomains($fio){
        $fio = str_replace(" ", "%20",trim($fio));
        $apiData = json_decode(file_get_contents("http://logs.snhrs.ru/index.php/api/domain?fio=".$fio));
        return $apiData->status == 1 ? $apiData->data : null;
    }


    /**
     * Приводим в нормальный ввид Логин
     * было SNHRS\42ksn
     * стало 42ksn@snhrs.ru
     * @param string $login
     * @return string|null
     */
    public function replaceDomain($login){
        $exp = explode('\\', $login);
        if(count($exp) > 1) :
            $domain = array_shift($exp);
            $login = array_shift($exp);
            $ru = mb_strtolower($domain) == 'zsmik' ? 'zsmik.com' : mb_strtolower($domain).'.ru';
            $result = $login.'@'.$ru;
        else:
            $result = $login;
        endif;

        return $result;
    }


}
