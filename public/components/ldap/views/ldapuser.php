<?php
/**
 * Created by PhpStorm.
 * User: 01gig
 * Date: 12.09.2018
 * Time: 15:21
 */




?>



<table class="table table-sm">
    <tbody>
        <tr>
            <th>ФИО</th>
            <th>Должность</th>
            <th>Почта</th>
        </tr>
    </tbody>
    <?php  for ($i = 0; $i <= $model['count']-1; $i++) { ?>
        <tr>
            <td><?= $model[$i]['cn'][0] ?></td>
            <td><?= isset($model[$i]['title'][0])? $model[$i]['title'][0] : ''?></td>
            <td><?= $model[$i]['mail'][0] ?></td>
        </tr>
    <?php  }   ?>
</table>
