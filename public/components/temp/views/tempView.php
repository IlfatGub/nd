<?php
    /**
     * @property int $type Тип записи
     *
     * $type = 1. Ответ от 1С.  Action site/domain-translation
     * $type = 2. Api uri, для 1С.  Action site/domain-translation
     */

    $i = 1;
    $columns = array();

    switch ($type) {
        case 2: $columns = ['t1', 'delete']; break;
        case 1: $columns = ['t1', 't2', 't3'];  break;
    }

    switch ($type) {
        case 2: $columns_name = ['Api_uri']; break;
        case 1: $columns_name = ['Старая уч. запись', 'Новая уч. запись', 'Ответ'];  break;
    }
    ?>


    <table class="table table-sm table-hover table-striped mt-2 fs-10" id="">
        <thead>
        <tr>
            <th>#</th>
            <?php foreach($columns_name as $name): ?>
                <th><?= $name ?></th>
            <?php endforeach; ?>
        </tr>
        </thead>
        <?php foreach($model as $item): ?>
            <tr id="temp-line-<?=  $item->id ?>">
                <td><?= $i++ ?></td>
                <?php foreach($columns as $column): ?>
                    <?php if($column != 'delete'): ?>
                        <td><?= $item->$column ?></td>
                    <?php else: ?>
                        <td> <i class="fa fa-times temp-del" id="<?= $item->id ?>" style="color: red; cursor: pointer"></i>  </td>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </table>



