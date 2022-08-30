<?php

namespace app\models;

use yii\db\ActiveRecord;
use app\modules\admin\models\Host;
use app\modules\admin\models\Users;
use app\modules\admin\models\IpUser;
use app\modules\admin\models\MacUser;
use app\modules\admin\models\Dolzh;

class Api extends ActiveRecord
{
    const SCENARIO_INDEX = 'index';

    public $id_user_fio;
    public $search;


    public function rules()
    {
        return [
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['index'] = ['id_fio'];
        return $scenarios;
    }

}
