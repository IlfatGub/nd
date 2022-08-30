<?php

namespace app\controllers;

//session_start();

use app\models\Depart;
use app\modules\admin\models\AppTemp;
use app\modules\admin\models\Buh;
use app\modules\admin\models\FioCase;
use app\modules\admin\models\Help;
use app\modules\admin\models\MyDate;
use app\modules\admin\models\Podr;
use app\modules\admin\models\Sitmap;
use http\Url;
use yii\helpers\Html;
use app\models\Sitdesk;
use app\modules\admin\models\Problem;
use Yii;
use app\modules\admin\models\Login;
use yii\rbac\Assignment;
use yii\web\Response;

//date_default_timezone_set('Asia/Yekaterinburg');

class AdmController extends BehaviorController
{

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionProblem($delete = null, $type = null, $id = null, $text = null)
    {
        $model = new Problem();

        if (isset($delete)) {
            $var = Problem::findOne($delete);
            $var->scenario = Problem::SCENARIO_UIPDATE;
            $var->visible = 0;
            $var->save();
        } elseif ($type) {
            $pr = Problem::findOne($id);
            if ($type == 'runtime') {
                $pr->runtime = $text;
            } elseif ($type == 'role') {
                $pr->role = $text;
                Problem::updateAll(['role' => $text], ['parent_id' => $id]);
            }
            $pr->save();

        } else {
            if ($model->load(Yii::$app->request->post())) {
                $model->add();
            }
        }

        $list = Problem::find()->where(['visible' => 1])->orderBy(['id' => SORT_DESC])->all();

        return $this->renderAjax('problem', [
            'model' => $model,
            'list' => $list
        ]);
    }


    public function actionService2($delete = null, $type = null, $id_problem = null, $id_temp = null, $parent_id = null)
    {

        $this->layout = 'main_service';
        $model = new Problem();

        if (isset($delete)) {
            $var = AppTemp::find()
                ->where(['id_problem' => $id_problem])
                ->andWhere(['id_temp' => $delete])
                ->andWhere(['type' => AppTemp::getType($type)])
                ->one();
            $var->visible = 0;
            $var->save();
        } elseif ($type) {
            $temp = new AppTemp();
            $temp->id_problem = $id_problem;
            $temp->id_temp = $id_temp;
            $temp->type = $type;
            $temp->add();
        } else {
            if ($model->load(Yii::$app->request->post())) {
                $model->add(Problem::SERVICE_ROLE_SAP);
            }
        }

        $parent = Problem::find()->andFilterWhere(['is', 'parent_id', new \yii\db\Expression('null')])->andWhere(['visible' => 1])->andWhere(['role' => 3])->all();


        if(isset($parent_id)){
            $pr1 = Problem::find()->select(['id'])->andWhere(['role' => 3])->andWhere(['visible' => 1])->andWhere(['parent_id' => $parent_id])->column();
            $pr2 = Problem::find()->select(['id'])->andWhere(['role' => 3])->andWhere(['visible' => 1])->andWhere(['parent_id' => $pr1])->column();
            $pr_merge = array_merge($pr1, $pr2);
            array_push($pr_merge, $parent_id);
            $list = Problem::find()->andWhere(['role' => 3])->andWhere(['visible' => 1])->andWhere(['id' => $pr_merge])->all();
//            echo "<pre>"; print_r($list); die();
        }else{
            $list = Problem::find()->andWhere(['role' => 3])->andWhere(['visible' => 1])->all();
        }

        $temp = AppTemp::find()->andWhere(['is', 'visible', null])->all();

        return $this->render('service2', [
            'model' => $model,
            'list' => $list,
            'temp' => $temp,
            'parent' => $parent
        ]);
    }

    public function actionService($delete = null, $type = null, $id = null, $text = null)
    {

        $model = new Problem();

        if (isset($delete)) {
            $var = Problem::findOne($delete);
            $var->scenario = Problem::SCENARIO_UIPDATE;
            $var->visible = 0;
            $var->save();
        } elseif ($type) {
            $pr = Problem::findOne($id);
            if ($type == 'runtime') {
                $pr->runtime = $text;
            } elseif ($type == 'db') {
                $pr->db = $text;
            }elseif ($type == 'podr') {
                $pr->podr = $text;
            }elseif ($type == 'user') {
                $pr->user_id = $text;
            }
            $pr->save();
        } else {
            if ($model->load(Yii::$app->request->post())) {
                $model->add(Problem::SERVICE_ROLE_SAP);
            }
        }

        $list = Problem::find()->andWhere(['role' => 3])->andWhere(['visible' => 1])->all();

        return $this->renderAjax('service', [
            'model' => $model,
            'list' => $list
        ]);
    }


    public function actionBuh($delete = null, $id = null, $text = null)
    {

        $this->layout = 'main_empty';

        $model = new Buh();
        $problem = new Problem();


        if ($text){
            $upd = Buh::findOne($id);
            $problem->setBase($upd->name, $text); //Меняем название базыв типе проблем
            $upd->name = $text;
            $upd->save();
        }

        if (isset($delete)) {
            $model->del($delete);
        } else {
            if ($model->load(Yii::$app->request->post())) {
                $model->add(trim(Html::encode($model->name)));
            }
        }

        $list = Buh::find()->where(['visible' => 1])->orderBy(['name' => SORT_DESC])->all();

        return $this->render('buh', [
            'model' => $model,
            'list' => $list
        ]);
    }

    public function actionCreatelogin()
    {
        $model = new Login();

        if ($model->load(Yii::$app->request->post())) {
            $model->login = Html::encode($model->login);
            $model->close = 0;
            $model->username = '-';
            $model->post = '-';
            $model->count = 10;
            $model->menu = 0;
            $model->settings_comment = 0;
            $model->visible = 0;
            $model->assist = 0;
            $model->role = 100;
            $model->depart = 1;
            $model->comment_list = 'Выполнено, Не берут трубку';
            $model->save();
        }

        $list = Login::find()->orderBy(['visible' => SORT_ASC, 'role' => SORT_DESC])->all();

        return $this->renderAjax('login', [
            'model' => $model,
            'list' => $list
        ]);
    }

    public function actionLogin($id = null, $vis = null, $text = null, $type = null)
    {
        $model = new Login();

        if (isset($text)) {
            $login = Login::findOne($id);
            if ($type == 1) {
                $login->login = $text;
            } elseif ($type == 2) {
                $login->username = $text;
            } elseif ($type == 3) {
                $login->post = $text;
            } elseif ($type == 4) {
                $login->role = $text;
            } elseif ($type == 5) {
                $login->depart = $text;
            }
            $login->save();
        }

        if (isset($vis)) {
            $login = Login::findOne($id);
            $login->visible = $vis;
            $login->save();

        }

        $list = Login::find()->orderBy(['visible' => SORT_ASC, 'depart' => SORT_ASC])->andWhere(['<>', 'visible', '100'])->all();

        return $this->renderAjax('login', [
            'model' => $model,
            'list' => $list
        ]);
    }

    public function actionPodr($delete = null)
    {
        $model = new Podr();

        if (isset($delete)) {
//            Podr::deleteAll(['id' => $delete]);
            $var = Podr::findOne($delete);
            $var->visible = 0;
            $var->save();
        } else {
            if ($model->load(Yii::$app->request->post())) {
                $model->name = Html::encode($model->name);
                $model->visible = 1;
                $model->sortable = Podr::find()->count() + 1;
                $model->save();
            }
        }

        $list = Podr::find()->where(['visible' => 1])->orderBy(['sortable' => SORT_ASC])->all();

        return $this->renderAjax('podr', [
            'model' => $model,
            'list' => $list
        ]);
    }

    public function actionPodrsort()
    {
        $model = new Podr();
        $arr = explode(',', $_GET['a']);
        print_r($arr);
        $i = 0;
        foreach ($arr as $item) {
            if ($item > 0) {
                $i++;
                $post = Podr::findOne($item);
                $post->sortable = $i;
                $post->save();
            }
        }

        return true;
    }


    public function actionSitmap($del = null, $upd = null)
    {
        $model = new Sitmap();
        if (isset($del)) {
            Sitmap::findOne($del)->delete();
            unset($del);
        } elseif (isset($upd)) {
            $model = Sitmap::findOne($upd);
            $model->isNewRecord = false;
            if ($model->load(Yii::$app->request->post())) {
                $model->save();
                return $this->redirect('sitmap');
            }
        } else {
            if ($model->load(Yii::$app->request->post())) {
                $model->save();
                return $this->redirect('sitmap');
            }
        }

        $sitmap = Sitmap::find()->all();
        return $this->render('sitmap', [
            'model' => $model,
            'sitmap' => $sitmap,
        ]);
    }

    public function actionLdap($fio = null)
    {
        $model = new Sitdesk();
        $text = '';

        if ($model->load(Yii::$app->request->post()) or isset($fio)) {
            if ($fio and !isset($_POST['Sitdesk']['type']) and !isset($_POST['Sitdesk']['server'])) {
                $_POST['Sitdesk']['type'] = 1;
                $_POST['Sitdesk']['server'] = 3;
            }
            echo $_POST['Sitdesk']['type'];
            echo $_POST['Sitdesk']['server'];
            $search = isset($fio) ? $fio : $_POST['Sitdesk']['ldap'];
            $search = str_replace('; ', ';', $search);
            $search = trim($search, ';');
            $text = array_map('trim', explode(';', $search)); //Текст для поиска в АД. Ввиде массива
            $type = $_POST['Sitdesk']['type']; //Тип посика. По Гурппам/Пользователи
            $srvConf = array(); //Настройки сервера
            $fio = '';
            $i = 0;
            $arr = array();

				$srv = $_POST['Sitdesk']['server'];
            if ($srv != 5) {
                $srvConf = Sitdesk::domainConfig($srv);
            } else {
                $snhrs = Sitdesk::domainConfig(1);
                $nhrs = Sitdesk::domainConfig(2);
                $zsmik = Sitdesk::domainConfig(3);
                $consalt = Sitdesk::domainConfig(4);
                $arr = array_merge($arr, Sitdesk::ldap($snhrs[0], $text, $snhrs[1], $snhrs[2], $snhrs[3]));
                $arr = array_merge($arr, Sitdesk::ldap($nhrs[0], $text, $nhrs[1], $nhrs[2], $nhrs[3]));
                $arr = array_merge($arr, Sitdesk::ldap($zsmik[0], $text, $zsmik[1], $zsmik[2], $zsmik[3]));
                $arr = array_merge($arr, Sitdesk::ldap($consalt[0], $text, $consalt[1], $consalt[2], $consalt[3]));


                $text = count($arr) == 1 ? null : $arr;
                return $this->render('ldap', [
                    'search' => $_POST['Sitdesk']['ldap'],
                    'server' => $_POST['Sitdesk']['server'],
                    'type' => $_POST['Sitdesk']['type'],
                    'model' => $model,
                    'text' => $text,
                ]);
            }

            $arr = Sitdesk::ldap($srvConf[0], $text, $srvConf[1], $srvConf[2], $srvConf[3], $type);


            $text = count($arr) == 1 ? null : $arr;

            return $this->render('ldap', [
                'search' => $search,
                'server' => $_POST['Sitdesk']['server'],
                'type' => $_POST['Sitdesk']['type'],
                'model' => $model,
                'text' => $text,
            ]);
        }

        return $this->render('ldap', [
            'model' => $model,
            'text' => $text,
        ]);
    }


    /*
     * Помошь диспетчеру. Слова
     * @param integer $delete
     */
    public function actionHelp($delete = null)
    {
        $model = new Help();

        //Удаляем объект, очщиаем parent_id
        if ($delete):
            $model->deleteById($delete);
        endif;

        if ($model->load(Yii::$app->request->post())) :
            $model->name = Html::encode($model->name);
            $model->save();
        endif;

        $list = Help::find()->orderBy(['parent_id' => SORT_ASC])->all();

        return $this->renderAjax('help', [
            'model' => $model,
            'list' => $list,
        ]);
    }

    /**
     * При вводе ФИО в поле "ФИО", получаем логи из logs.snhrs.ru и заполняем поле "IP"
     * @param string $fio
     */
    public function actionFio($fio = null)
    {
        $fio = trim($fio);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = FioCase::getDomains($fio);      // Получаем данные с logs.snhrs.ru
        $phone = Sitdesk::getPhone($fio, 1)[0];       // Получаем данные с phone.snhrs.ru
        $ip = $data->ip; //Выдергиваем ip
        $phone = !empty($phone->in) ? preg_replace('/\s+/', ' ', $phone->in) : preg_replace('/\s+/', ' ', $phone->out);

        if (isset($ip)) {
            return [$data->ip, Depart::getDepartIdByFio($fio), $phone];
        } else {
            return null;
        }
    }

    /**
     * При вводе тел. номера в поле "ТЕЛ", получаем ФИО, заполняем его в поле "ФИО" и для ФИО выполняем событие "blur"
     * @param string $phone
     */
    public function actionPhone($phone = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $phone = Sitdesk::getFullname($phone);       // Получаем данные с phone.snhrs.ru
        $fullname = $phone->fullname; //Выдергиваем ip
        if (isset($fullname)) {
            return [$fullname];
        } else {
            return null;
        }
    }


    public function actionAdduser()
    {
        isset($_GET['d']) ?: $this->redirect(\yii\helpers\Url::toRoute(['adduser', 'd' => 3]));
        return $this->render('adduser');
    }


    public function actionApiDepart($fio)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $fio = str_replace(" ", "%20",$fio);

        $url_zsm = "http://10.224.182.2/zsmik_zup_hav/hs/SitDesk/?type=Подразделение&name=$fio";
        $zsm = json_decode(Sitdesk::curl_buh($url_zsm));

        $url_zsm_podr = "http://10.224.182.2/zsmik_zup_hav/hs/SitDesk/?type=Структура&name=FULL";
        $zsm_podr = json_decode(Sitdesk::curl_buh($url_zsm_podr));


        if ($zsm->Result){
            foreach ( $zsm->Result as $item) {
                echo $item->ID;
            }
            echo "<pre>"; print_r($zsm->Result);

        }else{
            echo 'Данных нет';
        }

        if ($zsm_podr->Result){
            echo "<pre>"; print_r($zsm_podr); die();

        }else{
            echo '2';
        }

        die();


        return json_decode(curl_exec($ch));
    }

}


