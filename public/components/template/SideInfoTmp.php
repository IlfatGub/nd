<?php


/**
 * $type
 * Правый Сайдбар
 * Общаф информация
 *
 */
namespace app\components\template;

use yii\base\Widget;

class SideInfoTmp extends Widget
{
    public $id;

    public function init()
    {
        parent::init();
        if ($this->id === null) {
            $this->id = 0;
        }
    }


    public function run()
    {
        return $this->render('sideinfo');
    }
}