<?php

namespace  app\components;
use app\modules\admin\models\App;
use yii\base\Widget;

/**
 * Class TicketMenu
 * @package app\components
 *
 * @property int $agreed        Согласование.
 *
 */
class TicketMenu extends Widget{

    public $status;
    public $search;
    public $name;
    public $user;
    public $project;

    public function init () {
        parent::init();
        if($this->status === null){
            $this->status = 0;
        }
        if($this->search === null){
            $this->search = 0;
        }
        if($this->name === null){
            $this->name = 0;
        }
        if($this->user === null){
            $this->user = null;
        }
        if($this->project === null){
            $this->project = null;
        }
    }

    public function run()
    {
        $search = isset($_GET['search']) ? $_GET['search'] : null;

        $model =  App::appView($this->status, 0,  $this->user, $search, $this->project);

        return $this->render('ticketMenu',
            [
                'model' => $model,
                'name' => $this->name,
                'status' => $this->status,
            ]);
    }
}