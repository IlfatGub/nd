<?php


/**
 * 01gig
 * Виджет для вывода допольнительных заявок на экран
 *
 */

namespace app\components;

use app\modules\admin\models\App;
use app\modules\admin\models\AppComment;
use app\modules\admin\models\Login;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

class AdditionalWidget extends Widget
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
        return $this->render('additional');
    }
}