<?php
    /**
     * Created by PhpStorm.
     * User: 01gig
     * Date: 09.09.2021
     * Time: 9:50
     */



//    echo "<pre>"; print_r($model ); die();
    $i= 1;

    ?>





<div class="col-12 mx-2">
    <div class="col-12">
        <input id="search-users"  name="search-users" type="search" class="form-control form-control-sm mr-2 float-right w-300" placeholder="Поиск" aria-label="Поиск">

    </div>

    <table class="table table-sm fs-10 table_sort">
        <thead>
        <tr>
            <th>#</th>
            <th>ФИО</th>
            <th>Должность</th>
            <th>Вн.номер</th>
            <th>Внеш.номер</th>
            <th>Отдел</th>
        </tr>
        </thead>
        <?php foreach($model->Result as $item): ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= $item->User ?></td>
                <td><?= $item->Position ?></td>
                <td><?= $item->Work_Phone ?></td>
                <td><?= $item->External_Phone ?></td>
                <td><?= $item->subdivision ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

</div>


<?php
    $script = <<< JS

$("#search-users").keyup(function () {
    console.log('1');
    _this = this;
    $.each($("#hd-project tbody tr, #hw-users tbody tr"), function () {
        if ($(this).text().toLowerCase().indexOf($(_this).val().toLowerCase()) === -1) {
            $(this).hide();
        } else {
            $(this).addClass('pt-bg-light-yellow');
            $(this).show();
        }
    });
});


document.addEventListener('DOMContentLoaded', () => {
    console.log('2');

    const getSort = ({target}) => {
        const order = (target.dataset.order = -(target.dataset.order || -1));
        const index = [...target.parentNode.cells].indexOf(target);
        const collator = new Intl.Collator(['en', 'ru'], {numeric: true});
        const comparator = (index, order) => (a, b) => order * collator.compare(
            a.children[index].innerHTML,
            b.children[index].innerHTML
        );

        for (const tBody of target.closest('table').tBodies)
            tBody.append(...[...tBody.rows].sort(comparator(index, order)));

        for (const cell of target.parentNode.cells)
            cell.classList.toggle('sorted', cell === target);
    };

    document.querySelectorAll('.table_sort thead').forEach(tableTH => tableTH.addEventListener('click', () => getSort(event)));

});



JS;
    $this->registerJs($script);
?>