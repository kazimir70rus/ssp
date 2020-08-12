<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <link href="<?=BASE_URL?>css/main.css" rel="stylesheet" type="text/css">

</head>
<body>

<?php require_once 'logout.html'; ?>
<div id="app">
    <div>Срок: <?=$task_info['data_end']?></div>
    <div>Дата начало: <?=$task_info['data_begin']?></div>
    <div>Задание: <b><?=$task_info['task_name']?></b></div>
    <div>Исполнитель: <?=$task_info['executor']?></div>
    <div>Инициатор: <?=$task_info['iniciator']?></div>
    <div>Потребитель: <?=$task_info['client']?></div>
    <div>Контролер: <?=$task_info['controller']?></div>
    <div>Дата исполнения: <?=$task_info['data_execut']?></div>
    <div>Дата подтверждения: <?=$task_info['data_client']?></div>
    <div>Статус: <?=$task_info['primet']?></div>
    <div>Состояние: <?=$task_info['state']?></div>
    <br>
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

<?=$err?>

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
