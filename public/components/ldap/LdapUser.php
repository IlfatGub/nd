<?php
/**
 * 01gig
 */

namespace app\components\ldap;

use yii\base\Widget;

class LdapUser extends Widget
{
    public $model;

    public function init()
    {
        parent::init();
        if ($this->model === null) {
            $this->model = 0;
        }
    }

    public function run()
    {
        return $this->render('ldapuser',
            [
                'model' => $this->model
            ]);
    }
}