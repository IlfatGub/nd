<?php

namespace app\models;


use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use Yii;
use yii\helpers\ArrayHelper;


/**
 *
 * Отделы, заполняеться через 1с
 *
 * @property int id
 * @property string id_depart
 * @property string name
 * @property string id_parent
 *
 */


class Depart extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'depart';
    }

    public function rules()
    {
        return [
            [['id_depart', 'id_parent', 'name'], 'string'],
        ];
    }


    public function init()
    {
        parent::init();
        $this->name = trim($this->name);
        $this->id_parent = trim($this->id_parent);
        $this->id_depart = trim($this->id_depart);
    }

    public function attributeLabels()
    {
        return [
            'name' => 'namr',
        ];
    }


    /**
     * @param $org
     * @param null $id_depart
     * @return mixed
     * url API для обращения
     */
    public static function url($org, $id_depart = null){
        $url_full = '';
        switch ($org) {
            case 30: //Зсмик
                $url = "http://10.224.182.4/zsmik_zup/hs/SitDesk/?type=Структура&name=";
                $url_full = isset($id_depart) ? $url.$id_depart : $url."FULL";
                break;
            case 103: //НХРС
                $url = "http://10.224.100.11/nhrs_zup_work/hs/SitDesk/?type=Структура&name=";
                $url_full = isset($id_depart) ? $url.$id_depart : $url."FULL";
                break;
            case 53: //РМЗ
                $url = "http://zsm-sms01.zsmik.com/rmz_uso/hs/SitDesk/?type=Структура&name=";
                $url_full = isset($id_depart) ? $url.$id_depart : $url."FULL";
                break;
            case 52: //СНХРС
                $url = "http://10.224.182.2/zsmik_zup_hav/hs/SitDesk/?type=Структура&name=";
                $url_full = isset($id_depart) ? $url.$id_depart : $url."FULL";
                break;
            case 31: //ИТЦ
                $url = "http://10.224.182.2/zsmik_zup_hav/hs/SitDesk/?type=Структура&name=";
                $url_full = isset($id_depart) ? $url.$id_depart : $url."FULL";
                break;
            case 87: //Консалт
                $url = "http://10.224.100.11/nhrs_zup_work/hs/SitDesk/?type=Структура&name=";
                $url_full = isset($id_depart) ? $url.$id_depart : $url."FULL";
                break;
        }

        return json_decode(Sitdesk::curl_buh($url_full));
    }

    /**
     * @param $org
     * @param null $id_depart
     * @return mixed
     * url API для обращения
     */
    public static function urlByFio($org, $fio){
        $url='';
        switch ($org) {
            case 30: //Зсмик
                $url = "http://10.224.182.4/zsmik_zup/hs/SitDesk/?type=Подразделение&name=$fio";
                break;
            case 103: //НХРС
                $url = "http://10.224.100.11/nhrs_zup_work/hs/SitDesk/?type=Подразделение&name=$fio";
                break;
            case 53: //РМЗ
                $url = "http://zsm-sms01.zsmik.com/rmz_uso/hs/SitDesk/?type=Подразделение&name=$fio";
                break;
            case 52: //СНХРС
                $url = "";
                break;
            case 31: //ИТЦ
                $url = "";
                break;
             case 87: //Консалт
                 $url = "http://10.224.100.11/nhrs_zup_work/hs/SitDesk/?type=Подразделение&name=$fio";
                 break;
        }
        return json_decode(Sitdesk::curl_buh($url));
    }


    /**
     * @param $fio
     * @return mixed
     * Определяем подразделение по ФИО
     */
    public static function getDepartIdByFio($fio){
        $id_org = [30, 103, 53];

        foreach ($id_org as $key){
            $id_dep = self::urlByFio($key,  self::normalizeFio($fio));

            if (isset($id_dep->Result))
                return $key;
        }
    }


    /**
     * @param $fio
     * @return mixed
     * Приводим ФИО в нормальный вид, для отправки запроса 1с АПИ
     */
    public static function normalizeFio($fio){
        $fio = str_replace(" ", "%20",$fio);
        $fio = str_replace("ё", "е",$fio);
        return $fio;
    }


    /**
     * @param $org
     * @param null $id_depart
     * @param null $type
     * @return array|null
     * Список отделов по Организации
     */
    public static function getDepartByOrg($org, $id_depart = null, $type = null){

        $model = $sort = null;

        $podr = self::url($org, $id_depart);
        $model = $podr->Result ? $podr->Result : null;

        $option = '';

        if (count($model) > 0) {
            $sort = ArrayHelper::map($model, 'ID', 'subdivision');
            $sort = array_map('trim', $sort);
            asort($sort);
            $data = true;
            $option .= "<option value=''>Необходимо выбрать...</option>";
            foreach ($sort as $key => $item) {
                if (strlen($item) > 4)
                    $option .= "<option value = '" . $key . "'>" . $item . "</option>";
            }
        } else {
            $data = false;
            $option .= "<option></option>";
        }

        $result = [
            "select" => $option,
            "data" => $data,
        ];

        return $type ? $sort : $result;
    }



    public static function getDepart(){
        return self::find()->all();
    }

    /*
     * Выводим АйДи ФИО
     */
    public function getId(){

        if ($this->existsId()){
            $upd = self::findOne(['id_depart' => $this->id_depart]);
            $upd->name = trim($this->name);
            $upd->id_parent = $this->id_parent;
            $upd->save();

            return $upd->id;

        }else{
            $model = new self();
            $model->name = trim($this->name);
            $model->id_parent = $this->id_parent;
            $model->id_depart = $this->id_depart;
            $model->save();

            return $model->id;
        }

    }

    public function getList(){
        return Fio::find()->all();
    }

    public function existsId(){
        return self::find()->where(['id_depart' => $this->id_depart])->exists();
    }

}
