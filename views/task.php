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
            <td>Исполнитель:</td>
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
            <td>Штрафные баллы:</td>
            <td><?=$task_info['penalty']?></td>
        </tr>
        <tr>
            <td>Статус:</td>
            <td><?=$task_info['primet']?></td>
        </tr>
        <tr>
            <td>Состояние:</td>
            <td><?=$task_info['state']?></td>
        </tr>
    </table>
    <br>
    <form method="post">
        <input type="hidden" name="id_task" value="<?=$id_task?>">
        <select v-model="id_action" name="id_action" required class="input input_text">
            <option value="">выберете действие</option>
            <option v-for="action in actions" v-bind:value="action.id_action">{{action.name}}</option>
        </select>
        
        <div v-if="show_dt">
            изменить дату переноса:<br>
            <input type="date" name="dt" v-model="required_date" class="input input_text">
        </div>

        <div v-if="show_penalty">
            увеличить штрафные баллы на:<br>
            <input type="number" name="penalty" value="0" class="input input_text">
        </div>

        <div>
            Примечание:<br>
            <textarea name="comment" class="input" style="height: 5rem; max-width: 40rem;"></textarea><br>
            <input type="submit" name="submit" value="Подтвердить" class="input input_button">
        </div>
    </form>
</div>
<br>
<div>
    <table>
    <caption>загруженные документы</caption>
    <?php foreach ($upload_doks as $dok) { ?>
        <tr>
            <td><a href="<?=BASE_URL?>attachdoks/<?=$id_task?>/<?=$dok['filename']?>"><?=$dok['filename']?></a></td>
        </tr>    
    <?php } ?>
    </table>
    <form enctype="multipart/form-data" action="" method="post">
        <input type="file" name="userfile[]" class="input input_text" required><br>
        <input type="submit" name="upload" value="Добавить файл" class="input input_button">
    </form>
</div>
<div>
    <table>
    <caption>история изменений</caption>
    <?php foreach ($history_actions as $event) { ?>
        <tr>
            <td><?=$event['dt_create']?></td>
            <td><?=$event['user']?></td>
            <td><?=$event['action']?></td>
            <td><?=$event['dt_wish']?></td>
            <td><?=$event['comment']?></td>
        </tr>    
    <?php } ?>
    </table>
</div>
<br>
<script src="<?=BASE_URL?>js/vue.min.js"></script>
<script src="<?=BASE_URL?>js/vue-resource.min.js"></script>

<script>

var app = new Vue({
    el: '#app',
    data: {
        server: '<?=BASE_URL?>',
        actions: [],
        id_task: <?=$id_task?>,
        show_dt: false,
        show_penalty: false,
        required_date: '',
        id_action: '',
    },
    watch: {
        id_action: function () {
            let need_dt = false;
            let change_penalty = false;
            
            // ищем выбранное действие в массиве, если нужно выводим поля для ввода
            for (let i = 0; i < this.actions.length; ++i) {

                if (parseInt(this.id_action) == this.actions[i].id_action) {

                    if (this.actions[i].need_dt == 1) {
                        need_dt = true;
                        // todo
                        // возможно имеет смысл вставить запрашиваемую дату переноса
                        this.getRequiredDate();
                    }

                    if (this.actions[i].change_penalty == 1) {
                        change_penalty = true;
                    }
                    // дальше искать не нужно
                    break;
                }
            }

            this.show_dt = need_dt;
            this.show_penalty = change_penalty;
        },
    },
    methods: {
        getActions: function() {
            this.$http.get(this.server + 'getactions/' + this.id_task).then(
                function (otvet) {
                    this.actions = otvet.data;
                },
                function (err) {
                    console.log(err);
                }
            );
        },
        getRequiredDate: function() {
            this.$http.get(this.server + 'getreqdt/' + this.id_task).then(
                function (otvet) {
                    this.required_date = otvet.data.dt_wish;
                },
                function (err) {
                    console.log(err);
                }
            );
        },
    },
    created: function() {
        this.getActions();
    }
});

</script>

</body>
</html>
