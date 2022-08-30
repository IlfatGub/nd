<?php

namespace app\models;

use app\controllers\NameCaseLib\Library\NCLNameCaseRu;
use app\modules\admin\models\AppComment;
use app\modules\admin\models\AppContent;
use app\modules\admin\models\AppFiles;
use app\modules\admin\models\FioCase;
use app\modules\admin\models\Login;
use app\modules\admin\models\MyDate;
use app\modules\admin\models\Podr;
use Yii;
use yii\base\Model;
use app\modules\admin\models\App;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/**
 * LoginForm is the model behind the login form.
 * @property string $_name
 * @property string $_surname
 * @property string $_givName
 * @property string $_title
 * @property string $_samaccountname
 * @property string $_path
 */
class Ldap extends Model
{

    public $_name;
    public $_surname;
    public $_givenName;
    public $_title;
    public $_samaccountname;
    public $_path;
    public $_domain;

    const DOMAIN_SNHRS = 1;
    const DOMAIN_NHRS = 2;
    const DOMAIN_ZSMIK = 3;
    const DOMAIN_CONSALT = 4;

    public $domain_ip;
    public $domain_user;
    public $domain_psw;
    public $domain_id;
    public $domain_path;
    public $connect;


    const SCENARIO_ADDUser = 'adduser';

    public function rules()
    {
        return [
            [['_name', '_surname', '_givName', '_samaccountname', '_path'], 'required', 'on' => self::SCENARIO_ADDUser],
            [['_name',], 'string', 'max' => '27', 'message' => 'максимум 27 символа', 'on' => self::SCENARIO_ADDUser],
            [['_surname', '_title',], 'string', 'max' => '28', 'message' => 'максимум 28 символа', 'on' => self::SCENARIO_ADDUser],
            [['_givName',], 'string', 'max' => '55', 'on' => self::SCENARIO_ADDUser],
            [['_samaccountname',], 'string', 'max' => '10', 'on' => self::SCENARIO_ADDUser],
        ];
    }

    public function attributeLabels()
    {
        return [
            '_name' => 'Имя Отчество',
            '_surname' => 'Фамилия',
            '_givenName' => 'Полное имя',
            '_samaccountname' => 'Имя пользователя',
            '_domain' => 'Домен',
            '_path' => 'Подразделение',
            '_title' => 'Должность',
        ];
    }



    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_ADDUser] = ['_name', '_surname', '_givName', '_samaccountname', '_path'];

        return $scenarios;
    }

    /**
     * Создаем соединение
     * @return false|resource
     */
    public function connect(){
        $conn = ldap_connect($this->domain_ip)  or die("Cant connect to LDAP Server");
        ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);
        ldap_bind($conn, $this->domain_user, $this->domain_psw);
        $this->connect = $conn;
        return $conn;
    }

    /**
     * Получаем конфигуррацю сервера по Названию домена
     * @param $srv
     */
    public static function domainConfig($srv){
        $srvConfig = '';
        $srv_password = getenv('ldap_password');
        $snhrs = [getenv('ldap_snhrs'), "www_ldap@snhrs.ru", $srv_password, "DC=snhrs,DC=ru"];
        $nhrs = [getenv('ldap_nhrs'), "www_ldap@nhrs.ru", $srv_password, "DC=nhrs,DC=ru"];
        $zsmik = [getenv('ldap_zsmik'), "www_ldap@zsmik.com", $srv_password, "DC=zsmik,DC=com"];
        $consalt = [getenv('ldap_consalt'), "www_ldap@a-consalt.ru", $srv_password, "DC=a-consalt,DC=ru"];

        switch ($srv){
            case self::DOMAIN_SNHRS://СНХРС
                $srvConfig = $snhrs;
                break;
            case self::DOMAIN_NHRS://НХРС
                $srvConfig = $nhrs;
                break;
            case self::DOMAIN_ZSMIK://ЗСМиК
                $srvConfig = $zsmik;
                break;
            case self::DOMAIN_CONSALT://Аудит-Консалт
                $srvConfig = $consalt;
                break;
        }

        return $srvConfig;
    }

    public function getDefaultDN($srv){
        switch ($srv){
            case self::DOMAIN_SNHRS://СНХРС
                return "OU=JSC_SNHRS,DC=snhrs,DC=ru";
                break;
            case self::DOMAIN_NHRS://НХРС
                return "OU=NHRS,DC=nhrs,DC=ru";
                break;
            case self::DOMAIN_ZSMIK://ЗСМиК
                return "OU=2 ZSMIK,DC=zsmik,DC=com";
                break;
            case self::DOMAIN_CONSALT://Аудит-Консалт
                return "OU=Салават,OU=1_КОНСАЛ-АУДИТ,DC=a-consalt,DC=ru";
                break;
        }
    }

    /**
     * @param $domainName - название домена
     * @return false|resource
     */
    public function connectToRead($domainName){
        $conf = self::domainConfig($domainName);
        $this->domain_ip = $conf[0];
        $this->domain_user = $conf[1];
        $this->domain_psw = $conf[2];
        return self::connect();
    }

    /**
     * Получавем пользвоателей по контейнеру
     * @param $dn
     * @param $domain
     * @return array
     */
    public function getUserForContainer($dn, $domain){
        $filter = "(&(objectCategory=user)(objectClass=user)(!(userAccountControl:1.2.840.113556.1.4.803:=2))(mail=*))";   //тип запроса, поиск по пользоватлям
        $attr = array("cn", "mail", "title"); // поля которые будут возвращены. Пустой массив, выведет все поля
        $result = ldap_search($this->connect, $dn, $filter, $attr);
        $result_entries = ldap_get_entries($this->connect, $result);
        return $result_entries;
    }

    /**
     * @param $connect
     * @param $base_dn
     * @param string $ou
     * @return string
     * Получаем список контейнеров
     */
    public function getContainerList($connect, $base_dn, $ou = ''){
        $specific_ou = $ou;
        $specific_dn = $base_dn;
        $filter = 'ou=*';
        $justthese = array("ou");
        $sr = ldap_list($connect, $specific_dn, $filter, $justthese);
        $info = ldap_get_entries($connect, $sr);
        $result = '';
        for ($i=0; $i < $info["count"]; $i++)
        {
            $specific_dn = $info[$i]["dn"];
            $name = $info[$i]["ou"][0];
            $result .= '<ul class="ml-2">';
            $result .= '<li><label><input type="radio" class="ldap-path" name="_path" value="'.$specific_dn.'" title="'.$name.'" /><span style="display: none">'.$specific_dn.'</span> '.$name.'</label>';
            $result .= self::getContainerList($connect, $specific_dn, $specific_ou);
            $result .= '</li>';
            $result .= '</ul>';
        }
        return $result;
    }

    public function getAccessList($base_dn="OU=Access,OU=1 WORK,DC=zsmik,DC=com"){
        $filter = "(&(objectClass=group))";   //тип запроса, поиск по пользоватлям
        $attr = array("name"); // поля которые будут возвращены. Пустой массив, выведет все поля
        $result = ldap_search($this->connect, $base_dn, $filter, $attr);
        $result_entries = ldap_get_entries($this->connect, $result);
        return $result_entries;
    }


    public static function addContact($text, $model){
        // Атрибут для АД, контейнер где храняться контакты ЗСМиК для соответствующий доменов
        $ou_zsmik['company'] = 'zsmik';
        $ou_zsmik['snhrs'] = 'OU=ZSMIK,OU=Contacts,DC=snhrs,DC=ru';
        $ou_zsmik['consalt'] = 'OU=ZSMIK,OU=MailContact,OU=0_WORK,DC=a-consalt,DC=ru';
        $ou_zsmik['nhrs'] = 'OU=ZSMIK,OU=MailContact,OU=1WORK,DC=nhrs,DC=ru';

        $ou_snhrs['company'] = 'snhrs';
        $ou_snhrs['zsmik'] = 'OU=SNHRS,OU=MailContact,OU=1 WORK,DC=zsmik,DC=com';
        $ou_snhrs['consalt'] = 'OU=SNHRS,OU=MailContact,OU=0_WORK,DC=a-consalt,DC=ru';
        $ou_snhrs['nhrs'] = 'OU=SNHRS,OU=MailContact,OU=1WORK,DC=nhrs,DC=ru';

        $ou_consalt['company'] = 'consalt';
        $ou_consalt['snhrs'] = 'OU=KA,OU=Contacts,DC=snhrs,DC=ru';
        $ou_consalt['zsmik'] = 'OU=KA,OU=MailContact,OU=1 WORK,DC=zsmik,DC=com';
        $ou_consalt['nhrs'] = 'OU=KA,OU=MailContact,OU=1WORK,DC=nhrs,DC=ru';

        $ou_nhrs['company'] = 'nhrs';
        $ou_nhrs['snhrs'] = 'OU=NHRS,OU=Contacts,DC=snhrs,DC=ru';
        $ou_nhrs['zsmik'] = 'OU=NHRS,OU=MailContact,OU=1 WORK,DC=zsmik,DC=com';
        $ou_nhrs['consalt'] = 'OU=NHRS,OU=MailContact,OU=0_WORK,DC=a-consalt,DC=ru';


        $contactKA = "# Контакт для CONSALT.</br><div style='display: inline-block; font-size: 8pt;' id='foo'>";
        $contactSN = "# Контакт для SNHRS.</br><div style='display: inline-block; font-size: 8pt;' id='foo'>";
        $contactZS = "# Контакт для ZSMiK.</br><div style='display: inline-block; font-size: 8pt;' id='foo'>";
        $contactNH = "# Контакт для NHRS.</br><div style='display: inline-block; font-size: 8pt;' id='foo'>";


        $textContactKA = "";
        $textContactSN = "";
        $textContactZS = "";
        $textContactNH = "";

        foreach ($text as $item) {

            $login = $item['samaccountname'][0];

            if (isset($item['mail'][0]) and $login){

                $fioContact = explode(' ', $item['cn'][0]);

                $first_text = "New-MailContact -Name \"".$item['cn'][0]."\" -ExternalEmailAddress \"SMTP:".$item['mail'][0]."\"";
                $bottom_text = "-FirstName \"".$fioContact[1].' '.$fioContact[2]."\"  -Initials \"\" -LastName \"".$fioContact[0]."\" -PrimarySmtpAddress \"".$item['mail'][0]."\"";
                if(isset($model->server)){
                    if ($model->server == 1){ // СНХРС
                        $textContactZS .=  $first_text." -Alias \"".$ou_snhrs['company']."_".$login."\" ".$bottom_text." -OrganizationalUnit \"".$ou_snhrs['zsmik']."\" |  set-contact -company \"".strtoupper($ou_snhrs['company'])."\" -title \"".$item['title'][0]."\"<br>";
                        $textContactKA .=  $first_text." -Alias \"".$ou_snhrs['company']."_".$login."\" ".$bottom_text." -OrganizationalUnit \"".$ou_snhrs['consalt']."\" |  set-contact -company \"".strtoupper($ou_snhrs['company'])."\" -title \"".$item['title'][0]."\"<br>";
                        $textContactNH .=  $first_text." -Alias \"".$ou_snhrs['company']."_".$login."\" ".$bottom_text." -OrganizationalUnit \"".$ou_snhrs['nhrs']."\" |  set-contact -company \"".strtoupper($ou_snhrs['company'])."\" -title \"".$item['title'][0]."\"<br>";
                    }
                    if ($model->server == 2){ // НХРС
                        $textContactSN .=  $first_text." -Alias \"".$ou_nhrs['company']."_".$login."\" ".$bottom_text." -OrganizationalUnit \"".$ou_nhrs['snhrs']."\" |  set-contact -company \"".strtoupper($ou_nhrs['company'])."\" -title \"".$item['title'][0]."\"<br>";
                        $textContactKA .=  $first_text." -Alias \"".$ou_nhrs['company']."_".$login."\" ".$bottom_text." -OrganizationalUnit \"".$ou_nhrs['consalt']."\" |  set-contact -company \"".strtoupper($ou_nhrs['company'])."\" -title \"".$item['title'][0]."\"<br>";
                        $textContactZS .=  $first_text." -Alias \"".$ou_nhrs['company']."_".$login."\" ".$bottom_text." -OrganizationalUnit \"".$ou_nhrs['zsmik']."\" |  set-contact -company \"".strtoupper($ou_nhrs['company'])."\" -title \"".$item['title'][0]."\"<br>";
                    }
                    if ($model->server == 3){ // ЗСмик
                        $textContactSN .=  $first_text." -Alias \"".$ou_zsmik['company']."_".$login."\" ".$bottom_text." -OrganizationalUnit \"".$ou_zsmik['snhrs']."\" |  set-contact -company \"".strtoupper($ou_zsmik['company'])."\" -title \"".$item['title'][0]."\"<br>";
                        $textContactKA .=  $first_text." -Alias \"".$ou_zsmik['company']."_".$login."\" ".$bottom_text." -OrganizationalUnit \"".$ou_zsmik['consalt']."\" |  set-contact -company \"".strtoupper($ou_zsmik['company'])."\" -title \"".$item['title'][0]."\"<br>";
                        $textContactNH .=  $first_text." -Alias \"".$ou_zsmik['company']."_".$login."\" ".$bottom_text." -OrganizationalUnit \"".$ou_zsmik['nhrs']."\" |  set-contact -company \"".strtoupper($ou_zsmik['company'])."\" -title \"".$item['title'][0]."\"<br>";

                    }
                    if ($model->server == 4){ //Консалт
                        $textContactSN .=  $first_text." -Alias \"".$ou_consalt['company']."_".$login."\" ".$bottom_text." -OrganizationalUnit \"".$ou_consalt['snhrs']."\" |  set-contact -company \"".strtoupper($ou_consalt['company'])."\" -title \"".$item['title'][0]."\"<br>";
                        $textContactZS .=  $first_text." -Alias \"".$ou_consalt['company']."_".$login."\" ".$bottom_text." -OrganizationalUnit \"".$ou_consalt['zsmik']."\" |  set-contact -company \"".strtoupper($ou_consalt['company'])."\" -title \"".$item['title'][0]."\"<br>";
                        $textContactNH .=  $first_text." -Alias \"".$ou_consalt['company']."_".$login."\" ".$bottom_text." -OrganizationalUnit \"".$ou_consalt['nhrs']."\" |  set-contact -company \"".strtoupper($ou_consalt['company'])."\" -title \"".$item['title'][0]."\"<br>";

                    }
                }
            }
        }

        $copyKA = "<span class=\"btn btn-sm sitdesk-btncopy fas fa-copy myCopyf p-0 m-0\" title=\"Копировать\" data-clipboard-text='".str_replace('<br>', '',$textContactKA)."'></span>";
        $copySN = "<span class=\"btn btn-sm sitdesk-btncopy fas fa-copy myCopyf p-0 m-0\" title=\"Копировать\" data-clipboard-text='".str_replace('<br>', '',$textContactSN)."'></span>";
        $copyZS = "<span class=\"btn btn-sm sitdesk-btncopy fas fa-copy myCopyf p-0 m-0\" title=\"Копировать\" data-clipboard-text='".str_replace('<br>', '',$textContactZS)."'></span>";
        $copyNH = "<span class=\"btn btn-sm sitdesk-btncopy fas fa-copy myCopyf p-0 m-0\" title=\"Копировать\" data-clipboard-text='".str_replace('<br>', '',$textContactNH)."'></span>";

        $contactKA .= $copyKA.$textContactKA."</div><br><br>";
        $contactSN .= $copySN.$textContactSN."</div><br><br>";
        $contactZS .= $copyZS.$textContactZS."</div><br><br>";
        $contactNH .= $copyNH.$textContactNH."</div><br><br>";

        $contact = '';

        if (strlen($textContactSN) > 200)
            $contact .= $contactSN;
        if (strlen($textContactZS) > 200)
            $contact .= $contactZS;
        if (strlen($textContactKA) > 200)
            $contact .= $contactKA;
        if (strlen($textContactNH) > 200)
            $contact .= $contactNH;

        return $contact;

    }


    /**
     * @param $text
     * @return array
     * Вывод
     */
    public static function accessListAll($model){
        if($model){
            $read = '';
            $write = '';
            $other  = '';
            $description = $access = '';
            foreach($model as $item){
                $text = $item['cn'][0];
                if (strpos($text, '_read') !== false) {
                    $read .= $text."<br>";
                }elseif (strpos($text, '_write') !== false){
                    $write .= $text."<br>";
                }else{
                    $other .= $text."<br>";
                }
                $access .= $item['cn'][0]."<br>";
                $description .= $item['description'][0]."<br>";
            }

            return [$read,$write,$other,$description,$access];
        }
    }


}




