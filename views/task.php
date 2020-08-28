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
            <option v-for="action in actions" v-bind:value="action.id_action">{{action.name}}</option>
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
        id_action: '',
    },
    watch: {
        id_action: function () {
            let found = false;
            
            for (let i = 0; i < this.actions.length; ++i) {
                if (parseInt(this.id_action) == this.actions[i].id_action) {
                    if (this.actions[i].need_dt == 1) {
                        found = true;
                        break;
                    }
                }
            }

            if (found) {
                this.show_dt = true;
            } else {
                this.show_dt = false;
            }
        },
    },
    methods: {
        getActions: function(id) {
            this.$http.get(this.server + 'getactions/' + this.id_task).then(
                function (otvet) {
                    this.actions = otvet.data;
                },
                function (err) {
                    console.log(err);
                }
            );
        },
    },
    created: function() {
        this.getActions(1);
    }
});

</script>

</body>
</html>
