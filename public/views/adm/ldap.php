<?php

use app\models\Sitdesk;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use kartik\widgets\DatePicker;
use yii\widgets\Pjax;
use yii\helpers\Url;


$model->ldap = isset($search) ? $search : '';
$model->server = isset($server) ? $server : '';
$type = isset($type) ? $type : '';
$mailDisable = '';
$mail = '';
$t = 0;
$samaccountname = '';


    function generate_password($number)
    {
        $arr = array(
            '1','2','3','4','5','6',
            '7','8','9');

        $arr = array('a','b','c','d','e','f',
            'g','h','i','j','k',
            'm','n','p','r','s',
            't','u','v','x','y','z',
            '2','3','4','5','6',
            '7','8','9');


        // Генерируем пароль
        $pass = "";
        for($i = 0; $i < $number; $i++)
        {
            // Вычисляем случайный индекс массива
            $index = rand(0, count($arr) - 1);
            $pass .= $arr[$index];
        }
        return $pass;
    }

?>


<div class="org-form">
    <?php Pjax::begin([
        'id' => 'login',
        'enablePushState' => false
    ]); ?>

    <div class="row col-12">
        <div class="col-12">
            <div class="col-12">
                <div class="bs-callout bs-callout-info">
                    <h4>Работа с ActiveDirectory</h4>
                    <!--                    <p id="ldapText">-->
                    <!--                        Поиск по ФИО и логину. Фио и логин разделять <code>';'</code>. Вводить ФИО полностью, ишет полное совпадение<br>Пример:-->
                    <!--                        <br>-->
                    <!--                        - <code>Иванов Иван Иванович</code> или <code>Иванов Иван Иванович; Петров Петр Петрович</code><br>-->
                    <!--                        - <code>01iii</code> или <code>01iii; 01nnn</code><br>-->
                    <!--                    </p>-->
                </div>
            </div>
        </div>


        <div class="col-12">
            <?php $form = ActiveForm::begin(['action' => ['/adm/ldap']]); ?>
            <div class="row col-12 ">
                <div class="col-6">
                    <?= $form->field($model, 'server')->dropDownList(['3' => 'ЗСМиК', '1' => 'СНХРС', '2' => 'НХРС', '4' => 'Аудит Консалт', '5' => 'All'], ['value' => isset($server) ? $server : ''])->label(false) ?>
                </div>
                <div class="col-6">
                    <?= $form->field($model, 'type')->dropDownList(['1' => 'Пользователи', '2' => 'Группы'], ['id' => 'LdapDropdownlist', 'value' => isset($type) ? $type : '', 'onChange' => 'ChangeText()'])->label(false) ?>
                </div>
            </div>
            <div class="row col-12">
                <div class="col-11">
                    <?= $form->field($model, 'ldap')->textInput()->label(false) ?>
                </div>
                <div class="col-1">
                    <?= Html::submitButton('Ок', ['class' => 'btn btn-success col-sm-12']) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>

        <?php if ($text) { ?>
            <div class="col-12">
                <div class="checkbox col-12">
                    <label><input type='checkbox' id='cb1'/>Показать информацию</label>
                    <label><input type='checkbox' id='cb2'/>Показать Отдел/Должнолсть</label>
                </div>
            </div>
        <?php } ?>


        <div class="col-12 mx-3">

            <?php if ($type == 1){
            $limit = 0;
            $i = 1;
            $mailHidden = null;
                $mailContactHidden = null;
            $loginList = null;
            $loginList2 = null;
            $forwardAddress = null;
            $cachClear = null;
            $mailReaply = null;
            $disableAccount = null;
            $contactDisable = null;
            $mailResetPass = null;
            $mailResetPass2 = null;
            $mailEnable = null;
            $moveADUser = null;
            $samaccountname = 'samaccountname<br>';
            $contactSN = '';
            $contactKA = '';
            $loginListHistory = "<table class='search-history-table-ldap'>"
            ?>


            <?php if ($text) {

                foreach ($text as $item) {
                    $login = isset($item['samaccountname'][0]) ? $item['samaccountname'][0] : null; //логин пользователя

                    $mail_login = isset($item['mail'][0]) ? $item['mail'][0] : $item['samaccountname'][0]; ?>
                    <?php if (isset($login)) {
                        if ($login) { ?>
                            <?php
                            $fullFio = explode(" ", trim($item['cn'][0])); // полное ФИО
                            $fio = $fullFio[0] . mb_substr($fullFio[1], 0, 1, 'UTF-8') . mb_substr($fullFio[2], 0, 1, 'UTF-8'); //Сокращенное ФИО. ИваноИИ

                            $mailBegin = "<div style='display: inline-block' id='foo" . $login . "'>New-MailBoxExportRequest -MailBox " . $login; // Начало скрипта
                            $mailEnd = date('Ymd') . "_" . $fio . "_" . $login . ".pst </div><br>"; // Конец скрипта
                            $passwords = date('dmY') . preg_replace('/[0-9]+/', '', $login);
//                                $passwords = Sitdesk::generate_password(8);
                            $samaccountname .= $login . "<br>"; //Список Логинов

                            $mailResetPass .= "<div style='display: inline-block' id='foo'>Set-ADAccountPassword " . $login . " -Reset -NewPassword (ConvertTo-SecureString -AsPlainText \"" . $passwords . "\" -Force -Verbose) –PassThru</div> <br>"; //Скрыпты для Отключения почты
                            $mailResetPass2 .= "<div style='display: inline-block' id='foo'>Set-ADAccountPassword " . $login . " -Reset -NewPassword (ConvertTo-SecureString -AsPlainText \"" . generate_password(10) . "\" -Force -Verbose) –PassThru</div> <br>"; //Скрыпты для Отключения почты
//                                $mailHidden .= "<div style='display: inline-block' id='foo'>Set-Mailbox -Identity " . $login . " -HiddenFromAddressListsEnabled \$true </div> <br>"; //Скрыпты для Отключения почты
                            $loginListHistory .= "<tr><td>" . $item['cn'][0] . "</td><td>" . $item['title'][0] . "</td><td>" . $mail_login . "</td></tr>"; // спиок ФИО.Логин.Должность

                            $loginList .= "<tr><td>" . $i++ . "</td><td>" . $item['cn'][0] . "</td><td>" . $item['title'][0] . "</td><td>" . $mail_login . "</td><td>" . $passwords . "</td></tr>"; // спиок ФИО.Логин.Должность
                            $loginList2 .= "<div>ФИО: " . $item['cn'][0] . "<div></div>Логин: " . $mail_login . "<div></div>Пароль: " . $passwords . "</div>"; // спиок ФИО.Логин.Должность

                            $disableAccount .= "Disable-ADAccount -Identity $login"; // спиок ФИО.Логин.Должность


                            //Автоответ на почту
                            $replayText = "Добрый день!
                                Почта " . $item['mail'][0] . " заблокирована. Прошу Вас отправлять письма на электронный адрес " . $item['samaccountname'][0] . "@nhrs.ru.
                                С уважением " . $item['cn'][0];


//                                $fioContact = explode(' ', $item['cn'][0]);
//                                if(isset($model->server)){
//                                    if ($model->server == 3){
//                                        $contactSN .=  "<div style='display: inline-block; font-size: 8pt;' id='foo'>
//                                        New-MailContact -Name \"".$item['cn'][0]."\" -ExternalEmailAddress \"SMTP:".$item['mail'][0]."\" -Alias 'zsmik_".$login."' -FirstName \"".$fioContact[1].' '.$fioContact[2]."\"  -Initials '' -LastName \"".$fioContact[0]."\" -PrimarySmtpAddress \"".$item['mail'][0]."\" -OrganizationalUnit \"OU=ZSMIK,OU=Contacts,DC=snhrs,DC=ru\" |  set-contact -company \"ZSMIK\" -title \"".$item['title'][0]."\"
//                                    </div> <br><br>";
//                                        $contactKA .=  "<div style='display: inline-block; font-size: 8pt;' id='foo'>
//                                        New-MailContact -Name \"".$item['cn'][0]."\" -ExternalEmailAddress \"SMTP:".$item['mail'][0]."\" -Alias 'zsmik_".$login."' -FirstName \"".$fioContact[1].' '.$fioContact[2]."\"  -Initials '' -LastName \"".$fioContact[0]."\" -PrimarySmtpAddress \"".$item['mail'][0]."\" -OrganizationalUnit \"OU=ZSMIK,OU=MailContact,OU=0_WORK,DC=a-consalt,DC=ru\" |  set-contact -company \"ZSMIK\" -title \"".$item['title'][0]."\"
//                                    </div> <br><br>";
//                                    }
//                                }


                            if (!isset($item['mail'][0])) {
                                // Создаем почту для созданной учетки
//                                $mailEnable .= "<div style='display: inline-block' id='foo'>Enable-Mailbox -Identity \"" . $item['cn'][0] . "\" -Database \"MDB-MBX1-1\"</div> <br>";
                                //Архив почты
                                switch ($server) {
                                    case '2': //NHRS
                                        $mailEnable .= "<div style='display: inline-block' id='foo'>Enable-Mailbox -Identity \"".$item['cn'][0]."\" -Database \"MailDB2\"</div> <br>";
                                        break;
                                    default: //All
                                        $mailEnable .= "<div style='display: inline-block' id='foo'>Enable-Mailbox -Identity \"" . $item['cn'][0] . "\" -Database \"MDB-MBX1-1\"</div> <br>";
                                        break;
                                }
                            } else {

                                $cachClear .= "<div style='display: inline-block' id='foo'>Remove-Item -Path '\\\\10.224.181.5\c$\Users\\" . $login . "\Appdata\Local\\1C\\1cv8\*' -Recurse</div> <br>";
                                $cachClear .= "<div style='display: inline-block' id='foo'>Remove-Item -Path '\\\\10.224.181.5\c$\Users\\" . $login . "\Appdata\Roaming\\1C\\1cv8\*' -Recurse</div> <br>";
                                $cachClear .= "<div style='display: inline-block' id='foo'>Remove-Item -Path '\\\\10.224.181.6\c$\Users\\" . $login . "\Appdata\Local\\1C\\1cv8\*' -Recurse</div> <br>";
                                $cachClear .= "<div style='display: inline-block' id='foo'>Remove-Item -Path '\\\\10.224.181.6\c$\Users\\" . $login . "\Appdata\Roaming\\1C\\1cv8\*' -Recurse</div> <br> <br>";
                                $cachClear .= "<div style='display: inline-block' id='foo'>Remove-Item -Path '\\\\10.224.181.5\c$\Users\\" . $login . ".ZSMIK\Appdata\Local\\1C\\1cv8\*' -Recurse</div> <br>";
                                $cachClear .= "<div style='display: inline-block' id='foo'>Remove-Item -Path '\\\\10.224.181.5\c$\Users\\" . $login . ".ZSMIK\Appdata\Roaming\\1C\\1cv8\*' -Recurse</div> <br>";
                                $cachClear .= "<div style='display: inline-block' id='foo'>Remove-Item -Path '\\\\10.224.181.6\c$\Users\\" . $login . ".ZSMIK\Appdata\Local\\1C\\1cv8\*' -Recurse</div> <br>";
                                $cachClear .= "<div style='display: inline-block' id='foo'>Remove-Item -Path '\\\\10.224.181.6\c$\Users\\" . $login . ".ZSMIK\Appdata\Roaming\\1C\\1cv8\*' -Recurse</div> <br><br>";

                                $cachClear .= "<div style='display: inline-block' id='foo'>Remove-Item -Path '\\\\10.224.100.41\c$\Users\\" . $login . "\\Appdata\\Local\\1C\\1cv8\\*'-Recurse</div> <br>";
                                $cachClear .= "<div style='display: inline-block' id='foo'>Remove-Item -Path '\\\\10.224.100.41\c$\Users\\" . $login . "\\Appdata\\Roaming\\1C\\1cv8\\*' -Recurse</div> <br>";
                                $cachClear .= "<div style='display: inline-block' id='foo'>Remove-Item -Path '\\\\10.224.100.42\c$\Users\\" . $login . "\\Appdata\\Local\\1C\\1cv8\\*'-Recurse</div> <br>";
                                $cachClear .= "<div style='display: inline-block' id='foo'>Remove-Item -Path '\\\\10.224.100.42\c$\Users\\" . $login . "\\Appdata\\Roaming\\1C\\1cv8\\*' -Recurse</div> <br>";
                                $cachClear .= "<div style='display: inline-block' id='foo'>Remove-Item -Path '\\\\10.224.100.43\c$\Users\\" . $login . "\\Appdata\\Local\\1C\\1cv8\\*'-Recurse</div> <br>";
                                $cachClear .= "<div style='display: inline-block' id='foo'>Remove-Item -Path '\\\\10.224.100.43\c$\Users\\" . $login . "\\Appdata\\Roaming\\1C\\1cv8\\*' -Recurse</div> <br>";

                                //Удаляем контакт
                                $contactDisable .= "<div style='display: inline-block' id='foo'>Set-MailContact -Identity " . $login . "_snhrs -HiddenFromAddressListsEnabled \$true -Force -confirm:\$false</div> <br>";

                                //Скрыпты для Отключения почты
                                $mailHidden .= "<div style='display: inline-block' id='foo'>Set-Mailbox -Identity " . $login . " -HiddenFromAddressListsEnabled \$true </div> <br>";
                                $mailContactHidden .= "<div style='display: inline-block' id='foo'>Set-MailContact -Identity consalt_" . $login . " -HiddenFromAddressListsEnabled \$true </div> <br>";

                                //Скрыпты для Отключения почты
                                $mailDisable .= "<div style='display: inline-block' id='foo'>Disable-Mailbox -Identity " . $login . "  -confirm:\$false</div> <br>";

                                //Переадресация почты
                                $forwardAddress .= "Set-Mailbox " . $login . "@snhrs.ru -ForwardingAddress " . $item['mail'][0] . "<br>";

                                //Автооответ
                                $mailReaply .= "Set-MailboxAutoReplyConfiguration " . $item['mail'][0] . " –AutoReplyState Scheduled –StartTime “" . date('m/d/Y') . "” –EndTime “" . date('m/d/Y', strtotime('+1 year')) . "”  –ExternalMessage “" . $replayText . "” –InternalMessage “" . $replayText . "”" . "<br><br>";

                                //Архив почты
                                switch ($server) {
                                    case '1': //SNHRS
                                        $mail .= $mailBegin . " -FilePath \\\\mbx1\\l$\\DeleteMailBox\\" . $mailEnd;
                                        $moveADUser = "Move-ADObject -Identity (Get-ADUser -Identity \$_.samaccountname).distinguishedName -TargetPath 'OU=Удаленные из почты,OU=Уволенные сотрудники,OU=JSC_SNHRS_WORKS,DC=snhrs,DC=ru'";
                                        break;
                                    case '2': //NHRS
                                        $mail .= $mailBegin . " -FilePath \\\\srv-mbx1\\e$\\delete_mailbox\\" . $mailEnd;
                                        $moveADUser = "Move-ADObject -Identity (Get-ADUser -Identity \$_.samaccountname).distinguishedName -TargetPath 'OU=Уволенные_сотрудники,OU=1WORK,DC=nhrs,DC=ru' ";
                                        break;
                                    case '3': //ZSMiK
                                        $mail .= $mailBegin . " -FilePath \\\\zsm-mbx1\\e$\\delete_mailbox\\" . $mailEnd;
                                        $moveADUser = "Move-ADObject -Identity (Get-ADUser -Identity \$_.samaccountname).distinguishedName -TargetPath 'OU=Уволенные_сотрудники,OU=1 WORK,DC=zsmik,DC=com' ";
                                        break;
                                    case '4': // Consalt
                                        $mail .= $mailBegin . " -FilePath \\\\ka-mbx1\\e$\\delete_mailbox\\" . $mailEnd;
                                        $moveADUser = "Move-ADObject -Identity (Get-ADUser -Identity \$_.samaccountname).distinguishedName -TargetPath 'OU=Уволенные_сотрудники,OU=0_WORK,DC=a-consalt,DC=ru' ";
                                        break;
                                    default: //All
                                        $mail .= '';
                                        $samaccountname = '';
                                        $mailDisable = '';
                                        break;
                                }
                            }


                            ?>


                        <?php };
                    } ?>
                    <?php if ($limit >= 200) break;
                    $limit++;
                } ?>
            <?php } ?>

            <!--  Блок с Пользователем. ФИО/Отдел/Должность/Список доступов-->
            <?php echo \app\components\ldapUserInfo::widget(['model' => $text]) ?>
            <!--  Блок с Пользователем. ФИО/Отдел/Должность/Список доступов-->


            <?php
            $loginListHistory .= "</table>";
            \app\modules\admin\models\AppSearchHistory::record($search, $loginListHistory, 'ldap');
            ?>


        </div>
    <?php if (Yii::$app->user->can('SuperAdmin')) { ?>

        <!--   Вывод скрипта для архивирования почты и отдельный скрипт для отключения-->
        <?php if ($type == 1 and $server != 5 and isset($text)) : ?>
            <div class="col-12 mx-3 my-3">
                <div class="card">
                    <div class="card-header">
                        <h3>Команды для PowerShell(Exhcange)</h3>
                    </div>
                    <div class="card-block">
                        <blockquote class="card-blockquote py-3 px-3">

                            <strong># Архив почты </strong><br>
                            <?= $mail ?>
                            <br>

                            <strong># Отключаем почту</strong> <br>
                            <?= $mailDisable ?>
                            <br>

                            <strong># Отключаем Учетку</strong> <br>
                            <?= $disableAccount ?>
                            <br><br>

                            <strong># Отключаем Контакт</strong> <br>
                            <?= $contactDisable ?>
                            <br>

                            <strong># Скрываем Почту из списка адресов</strong> <br>
                            <?= $mailHidden ?>
                            <br>

                            <strong># Скрываем Контакт из списка адресов</strong> <br>
                            <?= $mailContactHidden ?>
                            <br>

                            <strong># Сброс пароля</strong> <br>
                            <?= $mailResetPass ?> <br>
                            <br>

                            <strong># Сброс пароля</strong> <br>
                            <?= $mailResetPass2 ?> <br>
                            <br>

                            <strong># Создать почту для созданной учетки</strong> <br>
                            <?= $mailEnable ?>
                            <br>

                            # Переадресация <code>ТОЛЬКО ДЛЯ СНХРС -> ЗСМиК</code> <br>
                            <?= $forwardAddress ?>
                            <br>

                            # Для Чистки <code>Кэша в 1С. Только для 1с ЗСМиК </code><br>
                            <?= (string)$cachClear ?>
                            <br>

                            # Автоответ<br>
                            <div class="fs-10">
                                <?= $mailReaply ?>
                                <br>
                                <br>
                                #Отчклюаем автоответ
                                Set-MailboxAutoReplyConfiguration tts100@zsmik.com –AutoReplyState
                            </div>
                            <br>
                            <br>

                            <?= \app\models\Ldap::addContact($text, $model) ?>

                        </blockquote>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!--    Вывод скрипта для архивирования почты и отдельный скрипт для отключения-->


        <!--Вывод скрипты для дулаения групп с учетки, перемещения в "уволенные", блокировка-->
        <?php if (isset($text) and $type == 1) { ?>

            <?php


            $removeGroup = "Import-Csv C:\\samaccountname.csv | foreach  { Remove-ADPrincipalGroupMembership -Identity \$_.samaccountname -MemberOf (Get-ADPrincipalGroupMembership -Identity \$_.samaccountname) }";
            $disableADAccount = "Import-Csv C:\\samaccountname.csv | foreach {Disable-ADAccount -i \$_.samaccountname}";
            ?>

            <div class="col-12 mx-3  my-3">
                <div class="card">
                    <div class="card-header">
                        <h3>Команды для PowerShell(ActiveDirectory)</h3>
                    </div>
                    <div class="card-block">
                        <blockquote class="card-blockquote py-3 px-3">
                            <?php echo
                            "Import-Csv C:\\PS_UserDisable\\samaccountname.csv | ForEach-Object {  <br>

                                Disable-ADAccount -i \$_.samaccountname<br>
                                
                                \$login =\$_.samaccountname<br>
                                \$dt=Get-Date -Format \"dd-MM-yyyy\"<br>
                                (Get-ADPrincipalGroupMembership -Identity \$login | Where {\$_.Name -ne 'Пользователи домена'}).name | Select-Object | Out-File   \"c:\PS_UserDisable\\\$login(\$dt).txt\"<br>
                    
                                Remove-ADPrincipalGroupMembership -Identity \$login -MemberOf (Get-ADPrincipalGroupMembership -Identity \$login | Where {\$_.Name -ne 'Пользователи домена'})<br>
                                
                                $moveADUser <br>
                                }
                                <br>
                                'samaccountname' > C:\PS_UserDisable\samaccountname.csv
                    
                                " ?>
                            <br><br>
                            <?= $samaccountname ?>
                        </blockquote>
                    </div>
                </div>
            </div>
        <?php } ?>
        <!--Вывод скрипты для дулаения групп с учетки, перемещения в "уволенные", блокировка-->
    <?php } ?>

    <?php } else { ?>
        <?php if (Yii::$app->user->can('SuperAdmin')) { ?>
            <div class="card-columns mb-3">
                <!--Список пользователй в группе-->
                <?php
                $limit = 0;
                $replace = array("\\\\srv-fs3", "\\\\srv-fs5", "\\\\snhrs.ru", "\\share", "\\\\srv-fs");
                $replace2 = array("CN=", "CN");
                $access = \app\models\Ldap::accessListAll($text);
                $accessCreate = '' ?>
                <?php if ($text): ?>
                    <?php foreach ($text as $item) : ?>
                        <?php if (isset($item['cn'][0])) : ?>
                            <?php
                            $desp = isset($item['description'][0]) ? $item['description'][0] : '';
                            $accessCreate .= "New-ADGroup \"" . $item['cn'][0] . "\" -path 'OU=KA,OU=Access,OU=1 WORK,DC=zsmik,DC=com' -Description '" . $desp . "' -GroupScope Global -PassThru –Verbose <br>" ?>
                            <!--                        $accessCreate .= "New-ADGroup \"".$item['cn'][0]."\" -path 'OU=KA,OU=Access,OU=1 WORK,DC=zsmik,DC=com' -Description '".$desp."' -GroupScope Global -PassThru –Verbose <br>" ?>-->
                            <div class="card">
                                <div class="card-block">
                                    <table class="table table-condensed table-hover table-sm">
                                        <tr class="alert-primary">
                                            <td><?= $item['cn'][0] ?></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <?php $read = strpos($item['cn'][0], 'read') ?>
                                                <?php $write = strpos($item['cn'][0], 'write') ?>
                                                <?= str_replace($replace, "", isset($item['description'][0]) ? $item['description'][0] : '') ?><?php if ($read !== false) {
                                                    echo "(Чтение)";
                                                } elseif ($write !== false) {
                                                    echo "(Редактирование)";
                                                } ?>
                                            </td>
                                        </tr>
                                        <?php sort($item['member'], SORT_STRING); ?>
                                        <?php if (isset($item['member'])) { ?>

                                            <tr>
                                                <td colspan="2">

                                                    <?php for ($i = 1; $i <= count($item['member']) - 1; $i++) { ?>
                                                        <?php $explod = explode(',', $item['member'][$i]) ?>
                                                        <br>
                                                        <div class='cat1'
                                                             style='display: inline-block;'><?= ($i) . "." ?></div><?= str_replace($replace2, "", $explod[0]) ?>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>
        <?php } ?>
        <!--Список пользователй в группе-->
        <div class="col-lg-12 fs-10">
            <?= $access[0] ?> <br>
            <?= $access[1] ?> <br>
            <?= $access[2] ?> <br>
            <?= $access[3] ?> <br>
            <?= $access[4] ?> <br>
            <?php //       echo $accessCreate; ?>
        </div>
    <?php } ?>
        <?php Pjax::end(); ?>
    </div>

</div>






