<?php

namespace app\models;

use yii\validators\Validator;

class FioValidator extends Validator
{

    public $int;


    public function init()
    {
        parent::init();
    }

    public function validateAttribute($model, $attribute)
    {
        $parts = explode(' ', $model->$attribute);
        if (strlen($model->$attribute) < 3) :
            $model->addError($model, $attribute, 'Нужно заполнить полное ФИО');
        endif;
    }

    public function clientValidateAttribute($model, $attribute, $view)
    { //want js-validation too
        $a = 1;
        $message = "Неверный ФИО. Пример: Ивано Иван Иванович";
        $message2 = "Что то коротковато для ФИО";

        if ($a < 3) :

            return <<<JS
            var fio = $.trim($('#app-fio').val());
            if(!fio)
                return false;
            var result= fio.split(' ');
            var i = result.length;
            if (i != 3){
                    messages.push("$message");
            }else{
                if((result[0].length > 2)  &&  (result[1].length > 2)  &&  (result[2].length > 2) ) {
                    return true;
                }else   {
                    messages.push("$message2");
                }
            }
JS;
        endif;
        return true;

    }
}
