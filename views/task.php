<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <link href="<?=BASE_URL?>css/main.css" rel="stylesheet" type="text/css">

<style>
    table {
        width: 39%; /* Ширина таблицы */
    }
</style>    

</head>
<body>

<?php require_once 'logout.html'; ?>

<div id="app">
    <table>
        <tr>
            <td width="25%">Срок:</td>
            <td><?=$task_info['data_end']?></td>
        </tr>
        <tr>
            <td>Дата начало:</td>
            <td><?=$task_info['data_begin']?></td>
        </tr>
        <tr>
            <td>Задание:</td>
            <td><b><?=$task_info['task_name']?></b></td>
        </tr>
        <tr>
            <td>Испольнитель:</td>
            <td><?=$task_info['executor']?></td>
        </tr>
        <tr>
            <td>Инициатор:</td>
            <td><?=$task_info['iniciator']?></td>
        </tr>
        <tr>
            <td>Потребитель:</td>
            <td><?=$task_info['client']?></td>
        </tr>
        <tr>
            <td>Контролер:</td>
            <td><?=$task_info['controller']?></td>
        </tr>
        <tr>
            <td>Дата исполнения:</td>
            <td><?=$task_info['data_execut']?></td>
        </tr>
        <tr>
            <td>Дата подтверждения:</td>
            <td><?=$task_info['data_client']?></td>
        </tr>
        <tr>
            <td>Статус:</td>
            <td><?=$task_info['primet']?></td>
        </tr>
        <tr>
            <td>Состояние:</td>
            <td><?=$task_info['state']?></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>

    </table>

    <form method="post">
        <input type="hidden" name="id_task" value="<?=$id_task?>">
        <select v-model="id_action" name="id_action" required class="input input_text">
            <option value="">выберете действие</option>
            <?php
                foreach ($list_actions as $action) {
                    echo '<option value="' . $action['id_action'] . '">';
                    echo $action['name'];
                    echo '</option>';
                }
             ?>
        </select>
        <div v-if="show_dt">
            <input type="date" name="dt" class="input input_text">
        </div>
        <div>
            <textarea name="comment" class="input"></textarea><br>
            <input type="submit" name="submit" value="Подтвердить" class="input input_button">
        </div>
    </form>
</div>

<script src="<?=BASE_URL?>js/vue.min.js"></script>

<script>

var app = new Vue({
    el: '#app',
    data: {
        show_dt: false,
        id_action: '',
    },
    watch: {
        id_action: function () {
            // to-do:
            // как-то связать с полем need_dt таблицы actions
            if (parseInt(this.id_action) == 9) {
                this.show_dt = true;
            } else {
                this.show_dt = false;
            }
        },
    }
});

</script>

</body>
</html>
