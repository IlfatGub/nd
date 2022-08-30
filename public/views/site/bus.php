<?php
/**
 * Created by PhpStorm.
 * User: 01gig
 * Date: 25.09.2019
 * Time: 13:46
 */

//echo "asdasd";

use phpnt\yandexMap\YandexMaps;
use yii\helpers\Html;
use yii\widgets\Pjax;

?>

<?php Pjax::begin()?>

<?php
$apiData = \app\models\Sitdesk::sendFrontMonitorBus();


$latitude = $apiData->lat;
$longitude = $apiData->lon;

?>


<style>
    .ymaps-2-1-74-copyright__layout{
        display: none;
    }

</style>



<?php
$zsmik = ['8:15', '9:00', '9:45', '10:30', '11:15', '13:15', '14:00', '14:45', '15:30', '16:15'];
$snhrs= ['8:40', '9:25', '10:10', '10:55', '11:40', '13:40', '14:25', '15:10', '15:55', '16:40'];

$zsmTime = null; $snTime = null;
$bodyBus = "<div class='text-center'> ЗСМиК / СНХРС ";
$bodyZsmik = "<div class='text-center'>";
$bodySnhrs = "<div class='text-center'>";
for ($i = 0; $i <= count($zsmik) - 1; $i++) {
    //Определяем время следующей остновки в ЗСМиК
    if (strtotime(date('H:i')) <= strtotime($zsmik[$i]) and !$zsmTime) {
        $zsmTime = $zsmik[$i]; $zsmCol = 'bg-warning';
    }else{
        $zsmCol = null;
    };

    //Определяем время следующей остновки в СНХРС
    if (strtotime(date('H:i')) <= strtotime($snhrs[$i]) and !isset($snTime)) {
        $snTime = $snhrs[$i];$snCol = 'bg-warning';
    }else{
        $snCol = null;
    };

    //Определяем промежуток времени для ЗСМиК -> СНХРС
    if (strtotime(date('h:i')) >= strtotime($zsmik[$i]) and strtotime(date('h:i')) <= strtotime($snhrs[$i])) {
        $color = 'bg-warning';
    } else {
        $color = null;
    };

    $bodyBus .=   "<br><span class='$zsmCol'> $zsmik[$i] </span> / <span class='$snCol'> $snhrs[$i] </span> ";
    $bodyZsmik .= "<div class='$zsmCol'> $zsmik[$i] </div>";
    $bodySnhrs .= "<div class='$snCol'> $snhrs[$i] </div>";
}
$bodyBus .= "</div>";
$bodyZsmik .= "</div>";
$bodySnhrs .= "</div>";

if ($latitude == 0 or $longitude == 0){
    $latCenter = 53.384586;
    $lonCenter = 55.894473;
}else{
    $latCenter = $latitude;
    $lonCenter = $longitude;
    $items[] =
        [
            'latitude' =>  $latitude,
            'longitude' => $longitude,
            'options' => [
                [
                    'hintContent' => 'ГАЗель Р373А',
                    'balloonContentHeader' => 'ГАЗель Р373А',
                    'balloonContentBody' => $bodyBus,
                    'iconCaption' =>  'Маршрут ЗСМиК - СНХРС'
                ],
                [
                    'preset' => 'islands#icon',
                    'iconColor' => '#FE3333',
                ]
            ],
        ];
}


$items[] =
    [
        'latitude' =>  53.384586,
        'longitude' => 55.894473,
        'options' => [
            [
                'balloonContentHeader' => 'Остановка ЗСМиК',
                'iconCaption' => isset($zsmTime) ? 'ЗСМиК / '.$zsmTime : 'ЗСМиК',
                'balloonContentBody' => $bodyZsmik,

            ],
            [
                'preset' => 'islands#circleIcon',
                'iconColor' => '#19aa8d',
            ]
        ],
    ];
$items[] =
    [
        'latitude' =>  53.382696,
        'longitude' => 55.904848,
        'options' => [
            [
                'balloonContentHeader' => 'Нуриманова 3',
                'iconCaption' => 'Остановка Нуриманова 3',

            ],
            [
                'preset' => 'islands#circleIcon',
                'iconColor' => '#19aa8d'
            ]
        ],
    ];

$items[] =
    [
        'latitude' =>  53.398922,
        'longitude' => 55.906777,
        'options' => [
            [
                'balloonContentHeader' => 'Остановка СНХРС',
                'iconCaption' => isset($snTime) ? 'СНХРС / '.$snTime : 'СНХРС',
                'balloonContentBody' => $bodySnhrs,

            ],
            [
                'preset' => 'islands#blueAutoIcon',
                'iconColor' => '#19aa8d'
            ]
        ],
    ];
?>


<div class="col-12" style="height: 100% !important;">
    <div class="row container-fluid m-0">
        <div class="col-lg-10  col-sm-10 col-8 btn btn-sm btn-secondary my-2">
            Маршрутка ЗСМиК-СНХРС
        </div>
        <div class="col-lg-2 col-sm-2 col-4 ">
            <?= Html::a("<i class=\"fas fa-sync\"></i>", ['site/bus'], ['class' => 'col-12 btn btn-sm btn-info my-2', 'title' => 'Обноувить']) ?>
        </div>
    </div>

    <?php
        // вывод карты
        echo YandexMaps::widget([
            'myPlacemarks' => $items,
            'mapOptions' => [
                'center' => [$latCenter, $lonCenter],                                             // центр карты
                'zoom' => 17,                                                                   // показывать в масштабе
//                'type' => 'yandex#satellite',
                'controls' => [],                                           // использовать эл. управления
                'control' => [
                    'zoomControl' => [                                                          // расположение кнопок управлением масштабом
                        'top' => 75,
                        'left' => 15
                    ],
                ],
            ],
            'disableScroll' => false,                                                            // отключить скролл колесиком мыши (по умолчанию true)
            'windowHeight'  => '500px',
        ]);
    ?>
</div>

<?php Pjax::end()?>
