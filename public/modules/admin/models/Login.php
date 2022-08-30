<?php

namespace app\modules\admin\models;

use app\components\template\StatusTable;
use Yii;
use yii\bootstrap;
use yii\helpers\ArrayHelper;

Yii::$app->session->open();



/**
 * Работа с Sitdesk
 *
 * @property string $username
 * @property string $login
 * @property string $domain
 * @property string $comment_list
 * @property string $post
 * @property string $ip
 *
 * @property int role
 * @property int absent
 * @property int no_hd
 * @property int count
 * @property int menu
 * @property int visible
 * @property int close
 * @property int assist
 * @property int depart
 * @property int settings_menu
 * @property int settings_phone
 * @property int settings_comment
 * @property int settings_recal
 * @property int settings_help
 * @property int settings_url
 * @property int settings_userticket
 *
 */

class Login extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
//    public $id;
    const STATUS_ACTIVE = 1;

    //Сектора
    const USER_SIT = 1;  // Сектор СИТ
    const USER_SS = 4;   // Сектор связи
    const USER_SAP = 3;  // Сектор 1с



    public $rememberMe = false;

    public static function tableName()
    {
        return 'login';
    }


    public function rules()
    {
        return [
            [['role', 'count', 'menu', 'visible', 'close', 'assist','no_hd', 'id'], 'integer'],
            [['username', 'post', 'ip'], 'string', 'max' => 255],
            [['comment_list'], 'string', 'max' => 1000],
            [['login'], 'string', 'max' => 32],
            [['domain'], 'string', 'max' => 50],
            [['settings_menu', 'settings_comment', 'depart', 'settings_phone', 'settings_recal', 'settings_help','settings_active', 'settings_url','settings_userticket'], 'integer',]
        ];
    }



    public static function getSectorName($id_sector){
        switch ($id_sector) {
            case self::USER_SIT:
                return "Отдел ИТ";
                break;
            case self::USER_SS:
                return "Сектор связи";
                break;
            case self::USER_SAP:
                return "Сектор 1С";
                break;
        }
        return true;
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findByIp($ip)
    {
        return Login::findOne(['ip' => $ip]);
    }

    public static function findByLogin($login)
    {
        return Login::findOne(['login' => $login]);
    }



    /**
     * Отсутствую
     */
    public function setAbsent()
    {
        if (self::findOne($this->id)) {
            $model = self::findOne($this->id);
            $model->absent = $model->absent == 1 ? null : 1;
            $model->save();
            return $model->absent;
        }
        return false;
    }





    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'ФИО',
            'login' => 'Логин',
            'role' => 'Роль',
            'close' => 'Закрывать заявку при "Выполнено"?',
            'assist' => 'Хочешь видеть невыполненные заявки коллег?',
            'settings_menu' => 'Изменить размер левого меню',
            'settings_phone' => 'Блок с телефонами отдела',
            'settings_recal' => 'Блок напоминаний',
            'settings_help' => 'Вывод информации из Базы Знаний',
            'settings_url' => 'Блок с ссылками на наши ресурсы',
            'settings_active' => 'Вывод активных заявок',
            'settings_userticket' => 'Вывод предыдущих заявок пользователя',
        ];
    }

    /*
     * Вывод списка зарегестрированных пользователей
     * Только активнх
     */
    public static function getLoginList(){
        if(Login::find()->where(['no_hd' => null])->select(['username'])->exists()):
            return Login::find()->where(['no_hd' => null])->orderBy(['username' => SORT_ASC])->andWhere(['depart' => [1,3,4]])->all();
//            return Login::find()->where(['no_hd' => null])->orderBy(['username' => SORT_ASC])->orderBy(['depart' => SORT_ASC])->andWhere(['depart' => [1,3,4]])->all();
        endif;
        return false;
    }

    /*
     * Вывод списка зарегестрированных пользователей
     * Только активнх
     * SAP
     */
    public static function getLoginSap(){
        if(Login::find()->where(['visible' => 0])->andWhere(['depart' => 3])->select(['username'])->exists()):
            return Login::find()->where(['visible' => 0])->andWhere(['depart' => 3])->orderBy(['username' => SORT_ASC])->all();
        endif;
        return false;
    }

    public function Username($id)
    {
        $model = Login::findOne(['id' => $id]);
        return $model->login;
    }

    public function Fio($id)
    {
        $model = Login::findOne(['id' => $id]);
        return isset($model->username) ? $model->username : '';
    }


    public static function getDomianList(){
        return [
            '1' => "СНХРС",
            '2' => "НХРС",
            '3' => "ЗСМиК",
            '4' => "Аудит-консалт",
        ];
    }

    /**
     * вывод настроек домена
     * @param $domain
     * @return array
     */
    public static function getDomainSettings($domain){

        $snhrs = ["10.224.177.30", "snhrs.ru"];
        $nhrs = ["10.224.100.1", "nhrs.ru"];
        $zsmik = ["10.224.200.1", "zsmik.com"];
        $consalt = ["10.224.90.1",  "a-consalt.ru"];

        switch ($domain){
            case '1'://СНХРС
                return $snhrs;
                break;
            case '2'://НХРС
                return $nhrs;
                break;
            case '3'://ЗСМиК
                return $zsmik;
                break;
            case '4'://Аудит-Консалт
                return $consalt;
                break;
        }
    }

    /**
     * @param $id
     * @return string
     * выводим почту
     */
    public static function getLoginMail($id){
        $model = self::findOne($id);
        return $model->login."@".self::getDomainSettings($model->domain)[1];
    }

    /**
     * Присваиваем глобальные переменные для Пользователя
     * @param $role
     */
    public function userSettings($role)
    {


        $_SESSION['User']['id'] = $role->id;
        $_SESSION['User']['login'] = $role->login;
        $_SESSION['User']['username'] = $role->username;
        $_SESSION['User']['role'] = $role->role;
        $_SESSION['User']['close'] = $role->close;
        $_SESSION['User']['count'] = $role->count;
        $_SESSION['User']['menu'] = $role->menu;
        $_SESSION['User']['settings_comment'] = $role->settings_comment;
        $_SESSION['User']['settings_phone'] = $role->settings_phone;
        $_SESSION['User']['settings_recal'] = $role->settings_recal;
        $_SESSION['User']['assist'] = $role->assist;
        $_SESSION['User']['visible'] = $role->visible;
        $_SESSION['User']['depart'] = $role->depart;
        $_SESSION['User']['settings_menu'] = $role->settings_menu;
        $_SESSION['User']['comment_list'] = $role->comment_list;
        $_SESSION['User']['settings_help'] = $role->settings_help;
        $_SESSION['User']['settings_url'] = $role->settings_url;
        $_SESSION['User']['settings_userticket'] = $role->settings_url;

        Yii::$app->user->login(Login::findOne(['id' => $role->id]), 0);
    }

    public function userSettingsUnset()
    {
        $_SESSION['User']['id'] = null;
        $_SESSION['User']['login'] = null;
        $_SESSION['User']['username'] = null;
        $_SESSION['User']['role'] = null;
        $_SESSION['User']['close'] = null;
        $_SESSION['User']['count'] = null;
        $_SESSION['User']['menu'] = null;
        $_SESSION['User']['settings_comment'] = null;
        $_SESSION['User']['settings_phone'] = null;
        $_SESSION['User']['settings_menu'] = null;
        $_SESSION['User']['settings_recal'] = null;
        $_SESSION['User']['assist'] = null;
        $_SESSION['User']['visible'] = null;
        $_SESSION['User']['depart'] = null;
        $_SESSION['User']['comment_list'] = null;
        $_SESSION['User']['settings_help'] = null;
        $_SESSION['User']['settings_url'] = null;
        $_SESSION['User']['settings_userticket'] = null;

    }

    /*
     * Проверка на наличие открытых заявок. Со статусом "В Работу"
     *
     * */
    public function validateLoginApp($id)
    {
        if ($id <> Yii::$app->user->id) {
            return App::find()
                ->andWhere(['id_user' => $id])
                ->andWhere(['status' => 1])
                ->andWhere(['type' => null])
                ->count();
        }
        return false;
    }

    public static function getList()
    {
        return ArrayHelper::map(self::getLoginList(), 'id', 'username');
    }


    /**
     * @return int|string current user ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string current user auth key
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        foreach (self::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }

        return null;
    }


    /**
     * @param $id_depart
     * @return array|\yii\db\ActiveRecord[]
     * выводим пользователей по секторам
     */
    public static function getUserByDepart($id_depart){
        return self::find()
            ->andFilterWhere(['depart' => $id_depart])
            ->andFilterWhere(['is', 'no_hd', new \yii\db\Expression('null')])
            ->andFilterWhere(['<>', 'visible',1])
            ->all();
    }


    /**
     * @param $id_depart
     * @return array
     * Выводим полную статистику по отделу
     */
    public static function getFullStatusByDepart($id_depart){

        $_users = self::getUserByDepart($id_depart);

        foreach ($_users as $item) {
            $usr = $item->username;
            $stat[$usr][History::STATUS_WORK] = Login::getAppCountByStatus($item->id, History::STATUS_WORK);
            $stat[$usr][History::STATUS_ASIDE] = Login::getAppCountByStatus($item->id, History::STATUS_ASIDE);
            $stat[$usr][History::STATUS_CONSIDERATION] = Login::getAppCountByStatus($item->id, History::STATUS_CONSIDERATION);
            $stat[$usr]['now'] = Login::getAppCountByStatus($item->id, History::STATUS_CONSIDERATION, 1);
            $stat[$usr]['absent'] = self::findOne($item->id)->absent;
            $stat[$usr][200] = $stat[$usr][History::STATUS_WORK] + $stat[$usr][History::STATUS_CONSIDERATION];

            $max[$usr] = $stat[$usr][200];
        }

        return ['stat' => $stat, 'max' => $max];
    }


    /**
     * @param $id_user
     * @param $status
     * @return int|string
     * Получаем количество заявок у пользователя по статусу
     */
    public static function getAppCountByStatus($id_user, $status, $date = null){

        $status = isset($date) ? [History::STATUS_ASIDE, History::STATUS_WORK, History::STATUS_CONSIDERATION, History::STATUS_CLOSE] : $status;
        $date_to = isset($date) ? date('Y-m-d 00:00:00') : date('Y-m-d 00:00:00', strtotime( '-30 day' )); // начальная дата
        $date_do = date('Y-m-d 23:59:59'); // конечная дата

        return App::find()
            ->where(['id_user' => $id_user])
            ->andFilterWhere(['>=', 'date_ct', MyDate::getTimestamp($date_to)])
            ->andFilterWhere(['<=', 'date_ct', MyDate::getTimestamp($date_do)])
            ->andFilterWhere(['in', 'status', $status])
            ->count();
    }


    /**
     * @param $id_depart
     * @return string
     * @throws \Exception
     *
     * $id_depart - АйДи сетокра
     *
     * Статистистика для диспечтера.
     * Статистика Активных заявок
     * Статистика Отстутсвующий инженеров
     */
    public static function getAppStatistics($id_depart){

        $_users = self::getUserByDepart($id_depart); //список сотрудников по сектороно

        $max = $stat = array();

        try {
            foreach ($_users as $item) {

                $leadsCount =  self::getAppStatisticsByUserMonth($item->id); // текущая сатистика за месяц

                $stat_today=  self::getAppStatisticsByUserToday($item->id); // статистика за сегодня

                $leadsCount = json_decode(json_encode($leadsCount));
                $stat_today = json_decode(json_encode($stat_today));

                //формируем нужный массив
                foreach ($leadsCount as $user){
                    $stat[$user->username][$user->status] = isset($user->count) ? $user->count : 0;
                    $stat[$user->username]['absent'] = $user->absent ? $user->absent : 0;
                    $stat[$user->username]['200'] = in_array($user->status, [12,1]) ? $stat[$user->username]['200'] + $user->count : $stat[$user->username]['200'];
                    $max[$user->username] = $stat[$user->username]['200'];

                }

                //формируем нужный массив
                foreach ($stat_today as $user){
                    $stat[$user->username]['now'] =  $user->count;
                }

            }

        } catch (\Exception $ex) {
            print_r($ex->getMessage());
        }

        return StatusTable::widget(['model' => $stat, 'max' => $max]);

//        return ['stat' => $stat, 'max' => $max];

    }

    /**
     * @param $id_user
     * @param $date
     * @return array
     * @throws \yii\db\Exception
     *
     * Запрос
     */
    public static function getAppStatisticsByUserMonth($id_user){
        $date_to = MyDate::getTimestamp(date('Y-m-d 00:00:00', strtotime( '-30 day' ))); // дата за месяц

        return  Yii::$app->db->createCommand("
                      SELECT app.id, status, id_user, login.username,  absent, COUNT(*) as count 
                      FROM app
                          LEFT JOIN login ON app.id_user = login.id
                      WHERE id_user = ".$id_user." 
                      AND date_ct >= ".$date_to."
                      GROUP BY status ")->queryAll();
    }

    public static function getAppStatisticsByUserToday($id_user){
        $date_today = MyDate::getTimestamp(date('Y-m-d 00:00:00', strtotime( 'now' ))); // начальная дата

        return  Yii::$app->db->createCommand("
                              SELECT login.username, COUNT(*) as count
                              FROM app
                                  LEFT JOIN login ON app.id_user = login.id
                              WHERE id_user = ".$id_user."
                              AND date_ct >= ".$date_today."
                              GROUP BY login.username ")->queryAll();

    }


}
