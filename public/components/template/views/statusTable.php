



<?php

    /**
     * $max - остортированный массив, количество заявок
     * $model - полная информация по заявка пользователя
     */

    arsort($max);
    $i=0;

    if (!function_exists('zero')) {
        function zero($item = null)
        {
            return isset($item) ? $item : 0;
        }
    }

?>



<div style="border: 1px dashed silver;   padding: 0 3px; max-width: 550px" class=" alert-primar1y  mb-1">

<table class="table table-sm table-border fs-8 mt-1 mb-1">

    <tr class="alert-primary">
        <td>ФИО</td>
        <td>На рассм.</td>
        <td>В работе</td>
        <td>В ожид.</td>
        <td>Сегодня</td>
        <td>Всего</td>
    </tr>
    <?php foreach($max as $username => $item): ?>
        <?php $i++; ?>
        <?php $color = $i < 3 ? 'alert-warning hd-color-red' : ''; ?>
        <?php $color = isset($model[$username]['absent']) ? 'alert-danger hd-color-red' : $color; ?>
        <?php $icon = isset($model[$username]['absent']) ? '<span class="fa fa-close mr-2"></span>' : ''; ?>

        <tr class="<?=$color?>">
            <td><?= $icon.$username ?></td>
            <td><?= isset($model[$username][12]) ? $model[$username][12] : 0 ?></td>
            <td><?= isset($model[$username][1]) ? $model[$username][1] : 0 ?></td>
            <td><?= isset($model[$username][2]) ? $model[$username][2] : 0 ?></td>
            <td style="color: blue;"><?= isset($model[$username]["now"]) ? $model[$username]['now'] : 0 ?></td>
            <td><?= isset($model[$username][200]) ? $model[$username][200] : 0  ?></td>
        </tr>
    <?php endforeach; ?>
</table>
</div>