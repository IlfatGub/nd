<?php
use app\modules\admin\models\App;
use yii\helpers\Html;
?>



<?php
$api = App::getTag($content);
if ($api) {
    if ($api->status === true) {
        echo "<div style='font-size: 8pt'>";
        foreach ($api->data as $item) {
            echo Html::a('<abbr title="Помочь?" style="color: #286090">'
                . strtolower($item->tag) .
                '</abbr>', 'http://sit.snhrs.ru/index.php/knowledge?tag=' . $item->tag);
            echo ' ';
        }
        echo "</div>";
    }
}
?>