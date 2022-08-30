<?php

namespace  app\components;
use app\modules\admin\models\App;
use yii\base\Widget;

class AppWidget extends Widget{

    public $id;
    public $search;

    public function init () {
        parent::init();
        if($this->id === null){
            $this->id = 0;
        }
        if($this->search === null){
            $this->search = 0;
        }
    }

    public function run()
    {
        $search = isset($_GET['search']) ? $_GET['search'] : null;

        $active =  App::appView(1,0, $search);

        $pending =  App::appView(2,0, $search);

        $close =  App::appView(3, 0, $search);

        return $this->render('app',
            [
                'active' => $active,
                'pending' => $pending,
                'close' => $close,
            ]);
    }
}