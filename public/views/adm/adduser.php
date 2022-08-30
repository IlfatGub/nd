<?php
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use app\models\Ldap;
use kartik\widgets\TypeaheadBasic;



$domain = isset($_GET['d']) ? $_GET['d'] : Ldap::DOMAIN_ZSMIK;

$model = new Ldap(['scenario' => Ldap::SCENARIO_ADDUser]);
$model->_domain = $domain;
$model->connectToRead($domain);

$class = 'form-control form-control-sm';
$connect = $model->connectToRead($domain);
$password = date('dmY');
?>

<style>
    .control-label{
        font-size: 90%;
        color: #bd4147;
        background-color: #f8f9fa;
        border-radius: 0.25rem;
        margin-bottom: 0;
    }
</style>


<?php


//print_r($model->getAccessList());

//
//
//function build_tree($connect, $ou = '', $base_dn = 'OU=2 ZSMIK,DC=zsmik,DC=com')
//{
//    $specific_ou = $ou;
//    $specific_dn = $base_dn;
//    $filter = 'ou=*';
//    $justthese = array("ou");
//    $sr = ldap_list($connect, $specific_dn, $filter, $justthese);
//    $info = ldap_get_entries($connect, $sr);
//    $result = '';
//    for ($i=0; $i < $info["count"]; $i++)
//    {
//        $specific_dn = $info[$i]["dn"];
//        $name = $info[$i]["ou"][0];
//        $result .= '<ul class="ml-2">';
//        $result .= '<li><label><input type="radio" class="ldap-path" name="_path" value="'.$specific_dn.'" title="'.$name.'" /><span style="display: none">'.$specific_dn.'</span> '.$name.'</label>';
//        $result .= build_tree($connect, $specific_ou, $specific_dn);
//        $result .= '</li>';
//        $result .= '</ul>';
//    }
//    return $result;
//}
//
//$conn = ldap_connect('10.224.200.1')
//or die("Cant connect to LDAP Server");
//ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
//ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);
//ldap_bind($conn, 'tts1000', '321qwe');
//
//
//?>



<?php $form = ActiveForm::begin() ?>

<div class="row col-12 ml-1 mb-2">
    <?= Html::a('ЗСМиК', ['adduser', 'd' => $model::DOMAIN_ZSMIK], ['class' => 'btn btn-info ml-3 px-5']) ?>
    <?= Html::a('НХРС', ['adduser', 'd' => $model::DOMAIN_NHRS], ['class' => 'btn btn-warning ml-3 px-5']) ?>
    <?= Html::a('Консалт', ['adduser', 'd' => $model::DOMAIN_CONSALT], ['class' => 'btn btn-danger ml-3 px-5']) ?>
    <?= Html::a('СНХРС', ['adduser', 'd' => $model::DOMAIN_SNHRS], ['class' => 'btn btn-dark ml-3 px-5']) ?>
</div>


<div class="row col-12 ml-1 fs-10">
    <div class="row">
        <div class="col-4">1</div>
        <div class="col-8">2</div>
    </div>
    <div class="row">
        <div class="col-4">3</div>
        <div class="col-4">4</div>
        <div class="col-4">5</div>
    </div>
</div>


<div class="row col-12 ml-1 fs-10">

    <div class="col-4">
        <div style="border: 1px solid silver; border-radius: 3px" class="p-2 mb-2">
            <div class="row">
                <div class="col-6">
                    <?= $form->field($model, '_surname')->textInput(['class' => $class,  'placeholder' => 'Фамилия'])?>
                </div>
                <div class="col-6">
                    <?= $form->field($model, '_givenName')->textInput(['class' => $class, 'placeholder' => 'Имя Отчество']) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <?= $form->field($model, '_name')->textInput(['class' => $class, 'placeholder' => 'Полное имя']) ?>
                </div>
                <div class="col-12">
                    <?= $form->field($model, '_title')->textInput(['class' => $class, 'placeholder' => 'Должность']) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <?= $form->field($model, '_samaccountname')->textInput(['class' => $class, 'placeholder' => 'Имя пользователя']) ?>
                </div>
                <div class="col-6">
                    <?= $form->field($model, '_domain')->dropDownList([4 => '@a-consalt.ru',3 => '@zsmik.com',2 => '@nhrs.ru', 1 => '@nhrs.ru'], ['class' => $class, 'placeholder' => 'Подразделение']) ?>
                </div>
            </div>
        </div>
        <div>
            <div class="card  shadow-sm px-0">
                <h5 class="card-header"><code>ЗСМиК</code> <input id="search" class="float-right form-control-sm" size="20" /></h5>
                <ul class="treeCSS fs-16 p-2" id="search-items">
                    <?php echo $model->getContainerList($connect, $model->getDefaultDN($domain)); ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-8">
        <div>

            <div class="card shadow-sm px-0 mb-2">
                <h5 class="card-header p-1"><code>Active Directory</code></h5>
                <div  class="p-1 mb-2" id="ps-full">
                    New-ADUser
                    <br>-Name "<span class="ps-name"></span>"
                    <br>-GivenName "<span id="ps-givenname"></span>"
                    <br>-Surname "<span id="ps-surname"></span>"
                    <br>-Title "<span id="ps-title"></span>"
                    <br>-Description "<span id="ps-description"></span>"
                    <br>-SamAccountName "<span id="ps-samaccountname"></span>"
                    <br>-Path  "<span id="ps-path"></span>"
                    <br>-AccountPassword (ConvertTo-SecureString -AsPlainText "<?=$password?><span id="ps-password"></span>" -force) -Enabled $true
                </div>
            </div>

            <div style="border: 1px solid silver; border-radius: 4px" class="p-1 mb-2 display-none" id="ps-full-end"> </div>

            <div class="card shadow-sm px-0 mb-2">
                <h5 class="card-header p-1"><code>Exchange</code></h5>
                <div style="border: 1px solid silver; border-radius: 4px" class="p-1" id="ps-exchange"> Enable-Mailbox -Identity "<span class="ps-name"></span>" -Database "MDB-MBX1-1" </div>
            </div>

        </div>
        <div>
            <div style="border: 1px solid silver; border-radius: 4px"  id="ps-login" class="pb-2 display-none"> </div>
        </div>
    </div>
</div>

<?php ActiveForm::end() ?>

<?php
$js = <<<JS
$('input#ldap-_givenname, input#ldap-_surname, input#ldap-_title, input#ldap-_samaccountname').on('input', function(){
    setName();
    $('#ps-givenname').html(getGivenname());
    $('#ps-surname').html(getSurname());
    $('#ps-samaccountname').html(getSamaccountname());
    $('#ps-title').html(getTitle());
    $('#ps-description').html(getTitle());
    $('#ps-full-end').show().html(getPsFull());
    $('#ps-password').html(getSamaccountname().replace(/[^a-z]/g, ''))
});

var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;
    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};

$('.ldap-path').click(function(){
    $('#ps-path').html($(this).val());
    $('#ps-full-end').show().html(getPsFull());
    var key = getUrlParameter('d');
        $.ajax({
	      method: "GET", // метод HTTP, используемый для запроса
	      url: "/ldap/user", // строка, содержащая URL адрес, на который отправляется запрос
	      data: { // данные, которые будут отправлены на сервер
	        dn: $(this).val(),
	        domain: key,
	      },
	      success: function ( msg ) { // функции обратного вызова, которые вызываются если AJAX запрос выполнится успешно (если несколько функций, то необходимо помещать их в массив)
	        $('#ps-login').show().html(msg) 
	      },
	    })
});
        
function setName(){
    $('#ldap-_name').val(getSurname() + ' ' + getGivenname());
    $('.ps-name').html(getSurname() + ' ' + getGivenname());
}

function getSamaccountname(){
    return $('input#ldap-_samaccountname').val();
}
function getPsFull(){
    return $('#ps-full').text();
}
function getTitle(){
    return $('input#ldap-_title').val();
}
function getSurname(){
    return $('input#ldap-_surname').val();
}
function getName(){
    return $('input#ldap-_name').val();
}
function getGivenname(){
    return $('input#ldap-_givenname').val();
}
JS;


$this->registerJs( $js );
?>