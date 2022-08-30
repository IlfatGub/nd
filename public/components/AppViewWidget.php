<?php

namespace  app\components;
use app\modules\admin\models\App;
use yii\base\Widget;

class AppViewWidget extends Widget{

    public $iReview;
    public $iType;
    public $iId;
    public $iContent;
    public $iLogin;
    public $iPodr;
    public $iProblem;
    public $iDate;
    public $iPriority;
    public $iFio;
    public $iIp;
    public $iUsername;
    public $iBuh;
    public $iApiLogin;

    public function init () {
        parent::init();
    }

    public function run()
    {
        return $this->render('appView',
            [
                'iReview' => $this->iReview,
                'iType' => $this->iType,
                'iId' => $this->iId,
                'iContent' => $this->iContent,
                'iLogin' => $this->iLogin,
                'iPodr' => $this->iPodr,
                'iProblem' => $this->iProblem,
                'iDate' => $this->iDate,
                'iPriority' => $this->iPriority,
                'iFio' => $this->iFio,
                'iIp' => $this->iIp,
                'iUsername' => $this->iUsername,
                'iBuh' => $this->iBuh,
                'iApiLogin' => $this->iApiLogin,
            ]);
    }
}