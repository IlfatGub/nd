<?php

/**
 * Выводи м логи пользователя
 */

namespace  app\components;
use app\modules\admin\models\App;
use app\modules\admin\models\AppComment;
use app\modules\admin\models\AppFiles;
use app\modules\admin\models\Recal;
use yii\base\Widget;

class LogsWidget extends Widget{

    public $search;
    public $limit;

    public function init () {
        parent::init();
        if($this->search === null){
            $this->search = array();
        }
        if($this->limit === null){
            $this->limit = 0;
        }
    }

    public function run()
    {

        return $this->render('logs', [
            'search' => $this->search,
            'lim' => $this->limit
        ]);
    }
}