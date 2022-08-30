<?php

namespace app\controllers;

use app\components\ldap\LdapUser;
use app\models\Ldap;

class LdapController extends BehaviorController
{

    public function actionUser($dn="OU=СУ-1,OU=2 ZSMIK,DC=zsmik,DC=com", $domain){

        $model = new Ldap(['scenario' => Ldap::SCENARIO_ADDUser]);
        $model->connectToRead($domain);

        echo LdapUser::widget(['model' => $model->getUserForContainer($dn, $domain)]);
    }


}


