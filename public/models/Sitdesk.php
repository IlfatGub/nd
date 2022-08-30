<?php

    namespace app\models;

    use app\components\template\AppInfoTemplate;
    use app\controllers\NameCaseLib\Library\NCLNameCaseRu;
    use app\modules\admin\models\AppAnalog;
    use app\modules\admin\models\AppComment;
    use app\modules\admin\models\AppContent;
    use app\modules\admin\models\AppFiles;
    use app\modules\admin\models\FioCase;
    use app\modules\admin\models\Login;
    use app\modules\admin\models\MyDate;
    use app\modules\admin\models\Podr;
    use DateTime;
    use Yii;
    use yii\base\Model;
    use app\modules\admin\models\App;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\helpers\ArrayHelper;

    /**
     * LoginForm is the model behind the login form.
     */
    class Sitdesk extends Model
    {

        public $ldap;
        public $server;
        public $type;

        const SITDESK_GET_PROBLEM_PARENT = "http://newdesk.zsmik.com/api/get-problem-parent";
        const SUPPORT_GET_MAIL = "http://support.zsmik.com/api/get-user-mail";

        public function rules()
        {
            return [
                [['ldap'], 'string', 'min' => 2],
            ];
        }

        /*
         *  Отправка на почту.
         *
         */
        public static function appMail($id, $user, $type = null)
        {

            $model = App::find()
                ->where(['app.id' => $id])
                ->joinWith(['problem', 'priority'])
                ->joinWith(['user' => function ($q) {
                    $q->select(['id', 'login']);
                }])
                ->joinWith(['priority', 'podr'])
                ->joinWith(['appContent' => function ($q) {
                    $q->joinwith(['fio']);
                }])
                ->one();

            $login = Login::findOne($user);
            $mailTo = $login->login . "@" . Login::getDomainSettings($login->domain)[1];

            $danger = $model->id_priority == 3 ? 'bg-danger' : '';

            $message = "";

            $message .= AppInfoTemplate::widget(['id' => $id, 'type' => $type]);


            $document = AppFiles::find()->where(['id_app' => $id])->all();
            if (count($document) > 0) {
                $i = 1;
                $message .= "<br><br>";
                $message .= "Прикрепленные документы: <br>";
                foreach ($document as $item) {
                    $r = (explode('/', $item->path));
                    $s = (explode('_', array_pop($r)));
                    $message .= $i . '. ' . Html::a(array_shift($s), ['webtest.snhrs.ru/' . AppFiles::DEFAULT_PATH . '/' . $item->path], ['data-pjax' => 0]);
                    $message .= "<br>";
                    $i++;
                }
            }

            Yii::$app->mailer->compose()
                ->setFrom('ticket@nhrs.ru')
                ->setCc(["ticket@nhrs.ru"])
                ->setTo([str_replace(" ", "", $mailTo)])
                ->setSubject('New ticket ' . $id)
                ->setHtmlBody($message)
                ->send();

        }


        public static function getMailFromSupport($api_login){
            $model = new Sitdesk();
            return $model->curl($model::SUPPORT_GET_MAIL . '/?id_user=' . $api_login);
        }

        public function getReportForDay($date_to, $date_do){

            $date_to = strtotime($date_to);
            $date_do = strtotime($date_do);
            $dif = ($date_to - $date_do)/60/60/24;

            $status = [];
            $users = [];
            $login = [];

            for ($i = 0; $i <= abs($dif); $i++) {
                $day = $i * 86400;
                $date_do =  $date_to + $day;

                $app = App::find()->select(['COUNT(*) AS cnt', 'status'])
                    ->andFilterWhere(['>=', 'date_ct', $date_do])
                    ->andFilterWhere(['<=', 'date_ct', $date_do + 86399])
                    ->andFilterWhere(['<>', 'id_user', 50])
                    ->asArray()
                    ->groupBy(['status'])
                    ->all();

                $app2 = App::find()->select(['COUNT(*) AS cnt', 'id_user'])
                    ->andFilterWhere(['>=', 'date_ct', $date_do])
                    ->andFilterWhere(['<=', 'date_ct', $date_do + 86399])
                    ->andFilterWhere(['<>', 'id_user', 50])
                    ->asArray()
                    ->groupBy(['id_user' ])
                    ->all();

                $status[date('Y-m-d', $date_do)] = ArrayHelper::map($app, 'status', 'cnt');
                $users[date('Y-m-d', $date_do)] = ArrayHelper::map($app2, 'id_user', 'cnt');
                $login = array_unique(array_merge($login, ArrayHelper::map($app2, 'id_user', 'id_user')));


//                echo "<pre>"; print_r($app2 ); echo "</pre>";

            }

            return ['status' => $status, 'user' => $users, 'login' =>$login ];
        }

        /**
         * @param $id_app
         * отправка письма пользователю
         * при закрытии заявкаи
         * при отправке коментария
         */
        public static function sendUserMail($id_app, $no_exec = null, $comment = null)
        {
            $model = new Sitdesk();
            $message = '';

            $app = App::findOne($id_app);
            $appContent = AppContent::findOne(['id_app' => $id_app]);

            $url = $model::SUPPORT_GET_MAIL . '/?id_user=' . $app->api_login;

            //получаем почту пользоваетля
            $user_mail = $model->curl($url);

            $data = json_decode($user_mail);

            $date = date("Y-m-d", $app->date_ct);

            $setSubject = 'Helpdesk. Обращение №' . $id_app;

            if ($comment){
                //Текст сообщения
                $message .= "К заявке по вашему обращению №$id_app от $date оставлен комментарий  <br><br>";
                $message .= "<div><strong>Текст заявки №$id_app</strong> <br>$appContent->content </div>";
                $message .= "<br>";
                $message .= "<div><strong>Коментарий</strong> <br> $comment </div>";
                $message .= "<br><br><br><br>";
                $message .= "<a href='http://support.zsmik.com/helpdesk/index'>ссылка на систему Helpdesk</a>";
                $message .= "<br><br>Это письмо было сформировано автоматически, отвечать на него не нужно.";

                $setSubject = 'Helpdesk. Оставлен комментарий. Обращение №' . $id_app;

            }elseif (!$no_exec){
                //Текст сообщения
                $message .= "Заявки по вашему обращению №$id_app от $date выполнены  <br><br>";
                $message .= "<a href='http://support.zsmik.com/helpdesk/index'>ссылка на систему Helpdesk</a>";
                $message .= "<br><br>В случае не выполнения заявки в полном объеме, верните заявку в работу через личный кабинет системы Helpdesk с обязательнеым указанием причины возврата.";
                $message .= "<br><br><br><br>Это письмо было сформировано автоматически, отвечать на него не нужно.";
            }else{
                //Текст сообщения
                $message .= "Заявки по вашему обращению №$id_app от $date <span style='color:red'>не выполнены</span>  <br><br>";
                $message .= "<a href='http://support.zsmik.com/helpdesk/index'>ссылка на систему Helpdesk</a>";
                $message .= "<br><br><br><br>Это письмо было сформировано автоматически, отвечать на него не нужно.";
            }


            if ($data->status) {
                Yii::$app->mailer->compose()
                    ->setFrom('ticket@nhrs.ru')
                    ->setTo($data->data)
                    ->setCc(["ticket@nhrs.ru"])
                    ->setSubject($setSubject)
                    ->setHtmlBody($message)
                    ->send();
            }else{
                $file_headers = @get_headers($url);
                $message = 'id_app: '. $id_app."<br><br>";
                $message .= 'sit_user: '. Yii::$app->user->identity->username."<br><br>";
                $message .= 'url: '.$url."<br><br>";
                $message .= 'url_hader: <br>'.implode('<br>',  $file_headers);
                $setSubject = 'Helpdesk. Ошибка';

                Yii::$app->mailer->compose()
                    ->setFrom('ticket@nhrs.ru')
                    ->setTo(["ticket@nhrs.ru"])
                    ->setSubject($setSubject)
                    ->setHtmlBody($message)
                    ->send();
            }

        }


        public static function mailRemindComment($id, $user, $type = null, $comment = null)
        {

            $message = '';

            $model = App::find()
                ->where(['app.id' => $id])
                ->joinWith(['problem', 'priority'])
                ->joinWith(['user' => function ($q) {
                    $q->select(['id', 'login']);
                }])
                ->joinWith(['priority', 'podr'])
                ->joinWith(['appContent' => function ($q) {
                    $q->joinwith(['fio']);
                }])
                ->one();

            $login = Login::findOne($user);
            $mailTo = $login->login . "@" . Login::getDomainSettings($login->domain)[1];

            $message .= 'Пользователь оставил комменатрий на заявку ' . $id . '<br><br><br>';
            $message .= $comment;

            Yii::$app->mailer->compose()
                ->setFrom('ticket00@snhrs.ru')
                ->setTo([str_replace(" ", "", $mailTo)])
                ->setSubject('Ticket ' . $id)
                ->setHtmlBody($message)
                ->send();

        }


        public static function mailRemind($id, $type = 1)
        {
            $message = '';

            $model = App::findOne($id);

            $login = Login::findOne($model->id_user);
            $mailTo = $login->login . "@" . Login::getDomainSettings($login->domain)[1];

            $message .= AppInfoTemplate::widget(['id' => $id, 'type' => $type]);

            Yii::$app->mailer->compose()
                ->setFrom('ticket@nhrs.ru')
                ->setTo([str_replace(" ", "", $mailTo)])
                ->setCc(["ticket@nhrs.ru"])
                ->setSubject('Ticket ' . $id)
                ->setHtmlBody($message)
                ->send();
        }


        /**
         * Информация по заявке в виде таблицы
         */
        public function getAppInfoTable($id)
        {
            $model = App::find()
                ->where(['app.id' => $id])
                ->joinWith(['problem', 'priority'])
                ->joinWith(['user' => function ($q) {
                    $q->select(['id', 'login']);
                }])
                ->joinWith(['priority', 'podr'])
                ->joinWith(['appContent' => function ($q) {
                    $q->joinwith(['fio']);
                }])
                ->one();

            $message = '';

        }

        /*
         * ФИО
         * type = 1     -       выводи только Имени
         */
        public static function fio($fio, $type = null)
        {
            if (isset($fio)) {
                $var = explode(" ", trim($fio));
                if ($type == 1) {

                    $name = array_key_exists(1, $var) ? $var[1] : '';
                    $lastname = array_key_exists(0, $var) ? $var[0] : '';

                    $text = $name . ' ' . mb_substr($lastname, 0, 1, 'UTF-8') . '.';
                } else {
                    $text = $fio;
                }
            } else {
                $text = $fio;
            }
            return $text;
        }

        /*
         * Преобразуем в массив
         */
        public function CommList($comment)
        {
            return explode(',', $comment);
        }

        /*
         * Проверка на наличие заявок у коллег
         */
        public function countAssist()
        {
            return App::find()
                ->andWhere(['type' => null])
                ->andWhere(['status' => 1])
                ->andWhere(['id_user' => ArrayHelper::map(Login::find()->where(['depart' => Yii::$app->user->identity->depart])->all(), 'id', 'id')])
                ->andWhere('id_user != :id_user', ['id_user' => Yii::$app->user->id])
                ->orderBy(['id_user' => SORT_DESC])
                ->count();
        }

        /**
         * Получаем пордяковый номер домена по названию
         * @param $name
         * @return bool|int
         */
        public static function getDomainIdByEng($name)
        {
            switch ($name) {
                case 'SNHRS'://СНХРС
                    return 1;
                    break;
                case 'NHRS'://НХРС
                    return 2;
                    break;
                case 'ZSMIK'://ЗСМиК
                    return 3;
                    break;
                case 'A-CONSALT'://ЗСМиК
                    return 4;
                    break;
            }
            return false;
        }

        /**
         * Получаем конфигуррацю сервера по Названию домена
         * 1 - СНХРС, 2 - ЗСМиК....
         * @param $srv
         */
        public static function domainConfig($srv)
        {
            $srvConfig = '';
            $srv_password = "321qweR";
            $snhrs = ["10.224.177.30", "www_ldap@snhrs.ru", $srv_password, "DC=snhrs,DC=ru"];
            $nhrs = ["10.224.100.1", "www_ldap@nhrs.ru", $srv_password, "DC=nhrs,DC=ru"];
            $zsmik = ["10.224.200.1", "www_ldap@zsmik.com", $srv_password, "DC=zsmik,DC=com"];
            $consalt = ["10.224.90.1", "www_ldap@a-consalt.ru", $srv_password, "DC=a-consalt,DC=ru"];

            switch ($srv) {
                case '1'://СНХРС
                    $srvConfig = $snhrs;
                    break;
                case '2'://НХРС
                    $srvConfig = $nhrs;
                    break;
                case '3'://ЗСМиК
                    $srvConfig = $zsmik;
                    break;
                case '4'://Аудит-Консалт
                    $srvConfig = $consalt;
                    break;
            }

            return $srvConfig;
        }

        /*
         * Поиск пользователей и групп. Вывод соответствующи параметров
         *
         * @param string $srv
         * @param array $search
         * @param string $srv_login
         * @param string $srv_password
         * @param string $dn
         * @param integer $type  Тип запроса. 1 = Поиск по пользователям. 2 = Поиск по группам
                 *
                * @return array
         */
        public static function ldap($srv, $search, $srv_login, $srv_password, $dn, $type = 1)
        {
            $a = array();
            $dc = ldap_connect($srv, 389); //Открываем сессию


            foreach ($search as $item) :
                if ($type == 1) {
                    $filter = "(&(objectCategory=user)(|(cn=" . trim($item) . "*)(sAMAccountName=" . trim($item) . ")))";   //тип запроса, поиск по пользоватлям
                    $attr = array("cn", "mail", "title", "department", "description", "sAMAccountName", "ou", "memberof"); // поля которые будут возвращены. Пустой массив, выведет все поля
                } else {

                    $filter = "(&(objectCategory=group)(cn=" . trim(preg_replace('/\s/', '', $item)) . "*)(!(userAccountControl:1.2.840.113556.1.4.803:=2)))"; //тип запроса, поиск по группам
                    $attr = array("cn", "description", "member"); // поля которые будут возвращены. Пустой массив, выведет все поля
                }
                ldap_set_option($dc, LDAP_OPT_PROTOCOL_VERSION, 3);
                ldap_set_option($dc, LDAP_OPT_REFERRALS, 0);

                ldap_bind($dc, $srv_login, $srv_password);
                $result = ldap_search($dc, $dn, $filter, $attr);
                $result_entries = ldap_get_entries($dc, $result);
                $a = array_merge($a, $result_entries);
                if ($type <> 1)
                    break;
            endforeach;
            ldap_unbind($dc); //Закрываем сесиию
            return $a;
        }

        /*
         * Проверка на наличие сессии у пользователя
         */
        public function validateUserConnect()
        {
//        if(!isset($_SESSION['User']['id'])) { return $this->redirect(['/site/login']); }
        }


        /*
         * Вывод ФИО найденных в описании
         */
        public static function getFioCase($content)
        {
            $fioCase = array();
            $pos = false;
            $content = preg_replace('/\s+/', ' ', $content);
            foreach (ArrayHelper::map(FioCase::find()->all(), 'id', 'name') as $item) {
                if ($item) {
                    $exp = explode(' ', $item);
                    $F = array_shift($exp);
                    $N = mb_substr(array_shift($exp), 0, 1);
                    if (strlen($F) > 1) {
                        $pos = strpos($content, $F . ' ' . $N);
                    }
                    if ($pos !== false) {
                        $q = FioCase::find()->filterWhere(['Like', 'name', $F . ' ' . $N])->one();
                        $fioCase[] = $q->parent_id ? $q->parent_id : $q->id;
                    }
                }
            }

            return array_unique($fioCase);
        }

        public function userDepartArray()
        {
            if (Yii::$app->user->identity) {
                ;
                $userDepart = Login::find()->where(['depart' => Yii::$app->user->identity->depart])->all();
                return array_column($userDepart, 'id');
            }
            return false;
        }

        public static function getDomain($text)
        {
            $r = array();
            $q = explode('.', $text);
            $r = explode('@', array_shift($q));
            $text = array_pop($r);
            return $text;
        }

        /**
         * Получаем Организацю(СНХРС, ЗСМиК) по известному нам ip адресу(10.224.1.0, 10.224.205.0)
         * @param string $ip
         */
        public static function ipMaskAddress($ip)
        {
            $subnet = explode('.', $ip)[2]; //Получаем подсеть

            //список подсетей
            $zsmik = ['5', '51', '205', '251', '210', '204', '202', '206', '2', '10'];
            $consalt = [];
            $snhrs = ['1', '12', '30'];
            $itc = ['7', '207'];
            $rmz = ['21'];
            $nhrs = ['100', '91', '90'];

            //условия поиска организаций
            if (in_array($subnet, $zsmik)) {
                $org = 'ЗСМиК';
            } elseif (in_array($subnet, $consalt)) {
                $org = 'Консалт-Аудит';
            } elseif (in_array($subnet, $snhrs)) {
                $org = '01 СНХРС';
            } elseif (in_array($subnet, $itc)) {
                $org = 'ИТЦ';
            } elseif (in_array($subnet, $nhrs)) {
                $org = 'НХРС';
            } elseif (in_array($subnet, $rmz)) {
                $org = '02 РМЗ';
            } else {
                return false;
            }

            //Получаем АйДи организации
            if (Podr::find()->where(['like', 'name', $org, false])->exists()) {
                $i = Podr::find()->where(['like', 'name', $org, false])->one();
                return $i->id;
            } else {
                return false;
            }

        }

        /**
         * Получаем номер телефон по ФИО.
         * @param string $fio , Кондратьев Сергей Николаевич
         * @return string data, 33-33
         * $fullfio = поиск по полному ФИО
         */
        public static function getPhone($fio, $fullfio = null)
        {
            if ($fullfio == 1) {
                $fio_full = str_replace(' ', '_', $fio);
                $apiData = json_decode(file_get_contents("http://tel.snhrs.ru/index.php/api/index?search=" . $fio_full . "&fullfio=" . $fullfio));
            } else {
                $apiData = json_decode(file_get_contents("http://tel.snhrs.ru/index.php/api/index?search=" . $fio));
            }

            if (!$apiData->status) {
//            echo "http://phone.a-consalt.ru/index.php/api/index?search=".$fio;
//            echo "<pre>"; print_r($apiData ); die();

                $apiData = json_decode(file_get_contents("http://phone.a-consalt.ru/index.php/api/index?search=" . $fio));
            }
//        $apiData = json_decode(file_get_contents("http://phone.a-consalt.ru/index.php/api/index?search=".$search));

            return $apiData->status == 1 ? $apiData->data : null;
        }

        /**
         * Получаем ФИО по номеру телефона.
         * @param string $phone , 33-33
         * @return string data, Кондратьев Сергей Николаевич
         */
        public static function getFullname($phone = null)
        {
            $apiData = json_decode(file_get_contents("http://tel.snhrs.ru/index.php/api/index?search=" . $phone));
            if (!$apiData->status) {
                $apiData = json_decode(file_get_contents("http://phone.a-consalt.ru/index.php/api/fullname?phone=" . $phone));
            }
            return $apiData->status == 1 ? $apiData->data : null;
        }


        /**
         * Получаем читабельный вид отдела
         * @param $depart
         */
        public static function getDepartName($depart)
        {
            if ($depart) {
                $po = array("Завод строительных материалов и конструкций,", "Салаватнефтехимремстрой,");
                $replace = array(",", "=", "/", "\\", ".", "_");
                $str = explode('OU', $depart);
                $s1 = str_replace($replace, "", preg_replace("/[a-zA-Z]/i", "", $str[1]));
                $str = strlen($s1) > 0 ? $s1 . ', ' : '';
                return str_replace($po, "", substr($str, 0, -2));
            }
        }

        /*
         * Проверка на наличие аналогичный заявки
         */
        public static function chekIdByDv($dv, $id)
        {
            if (AppContent::find()->where(['AND', ['dv' => $dv], ['<>', 'id_app', $id]])->count() > 0) {
                return true;
            } else {
                return false;
            }
        }

        /*
         * Проверка на наличие аналогичный заявки
         */
        public static function getIdByDv($dv, $id)
        {
            if ($dv) {
                if (self::chekIdByDv($dv, $id)) {
                    return AppContent::find()->select(['id_app'])->where(['AND', ['dv' => $dv], ['<>', 'id_app', $id]])->all();
                }
            }
            return false;
        }


        /**
         * @param $number
         * @return string
         * генериреум пароль
         */
        public static function generate_password($number)
        {
            $arr = array('a', 'b', 'c', 'd', 'e', 'f',
                'g', 'h', 'i', 'j', 'k', 'l',
                'm', 'n', 'o', 'p', 'r', 's',
                't', 'u', 'v', 'x', 'y', 'z',
                'A', 'B', 'C', 'D', 'E', 'F',
                'G', 'H', 'I', 'J', 'K', 'L',
                'M', 'N', 'O', 'P', 'R', 'S',
                'T', 'U', 'V', 'X', 'Y', 'Z',
                '1', '2', '3', '4', '5', '6',
                '7', '8', '9', '0', '[', ']', '!', '?',
                '&', '%', '@', '{', '}');
            // Генерируем пароль
            $pass = "";
            for ($i = 0; $i < $number; $i++) {
                // Вычисляем случайный индекс массива
                $index = rand(0, count($arr) - 1);
                $pass .= $arr[$index];
            }
            return $pass;
        }




        public function setCalendar()
        {
            $message = "BEGIN:VCALENDAR
VERSION:2.0
CALSCALE:GREGORIAN
METHOD:REQUEST
BEGIN:VEVENT
DTSTART:20190918T121000Z
DTEND:20191001T131000Z
DTSTAMP:20110525T075116Z
ORGANIZER;CN=From Name:mailto:from email id
UID:12345678
ATTENDEE;PARTSTAT=NEEDS-ACTION;RSVP= TRUE;CN=Sample:mailto:sample@test.com
DESCRIPTION:This is a test of iCalendar event invitation.
LOCATION: Kochi
SEQUENCE:0
STATUS:CONFIRMED
SUMMARY:Test iCalendar
TRANSP:OPAQUE
END:VEVENT
END:VCALENDAR";

            /*Setting the header part, this is important */
            $headers = "From: From Name support@mail.ru \n";
            $headers .= "MIME-Version: 1.0\n";
            $headers .= "Content-Type: text/calendar; method=REQUEST;\n";
            $headers .= '        charset="UTF-8"';
            $headers .= "\n";
            $headers .= "Content-Transfer-Encoding: 7bit";

            /*mail content , attaching the ics detail in the mail as content*/
            $subject = "Meeting Subject";
            $subject = html_entity_decode($subject, ENT_QUOTES, 'UTF-8');

            /*mail send*/
            if (mail('support@mail.ru', $subject, $message, $headers)) {

                echo "sent";
            } else {
                echo "error";
            }
        }


        public function getDayRus($day_str){
            $days = array(
                'Вс', 'Пн', 'Вт', 'Ср',
                'Чт', 'Пт', 'Сб'
            );
            return '('.$days[(date('w', $day_str))].')';
        }



        public function curl($url)
        {

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            return curl_exec($ch);
        }

        public static function curl_buh($url)
        {
            $username = "sitdesk";
            $password = getenv("smtp_pas")";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            return curl_exec($ch);
        }


    }
