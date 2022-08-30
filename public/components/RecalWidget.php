<?php

namespace  app\components;
use app\models\Sitdesk;
use app\modules\admin\models\App;
use app\modules\admin\models\Recal;
use yii\base\Widget;

/**
 *
 * Виджет для вывода напомминаний
 */

class RecalWidget extends Widget{

    public $id;

    public function init () {
        parent::init();
        if($this->id === null){
            $this->id = 0;
        }
    }

    public function run()
    {


        $recal = Recal::find()
            ->joinWith(['user' => function ($q) {
                $q->select(['id', 'username']);
            }])
            ->filterWhere(['in', 'recal.id_user', Sitdesk::userDepartArray()])
            ->orderBy(['date' => SORT_DESC])
//            ->select(['text', 'recal.id', 'user.id', 'user.username'])
            ->all();

        return $this->render('recal',
            [
                'recal' => $recal,
            ]);
    }
}