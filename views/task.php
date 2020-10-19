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
            <td>Дата начала:</td>
            <td><?=$task_info['data_begin']?></td>
        </tr>
        <tr>
            <td>Дата создания:</td>
            <td><?=$task_info['data_create']?></td>
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
            <td>Вид результата:</td>
            <td><?=$task_info['result_name']?></td>
        </tr>
        <tr>
            <td>Вид отчета:</td>
            <td><?=$task_info['report_name']?></td>
        </tr>
        <tr>
            <td>Начисленные баллы:</td>
            <td><?=$task_info['charges_penalty']?></td>
        </tr>
        <tr>
            <td>Состояние:</td>
            <td><?=$task_info['state']?></td>
        </tr>
        <tr>
            <td>Тип задачи:</td>
            <td><?=$task_info['periodicity'] . ' ' . $describ?></td>
        </tr>
    </table>
    <br>
    <form method="post" @submit="checkForm">
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
            <textarea name="comment" class="input" v-model="comment" style="height: 5rem; max-width: 40rem;"></textarea><br>
            <input type="submit" name="submit" value="Подтвердить" class="input input_button">
        </div>
    </form>
    <br>
    <div>
        <table>
        <caption>загруженные документы</caption>
            <template v-for="dok in upload_files">
                <tr>
                    <td><a v-bind:href="'<?=BASE_URL?>attachdoks/<?=$id_task?>/' + dok.filename">{{dok.filename}}</a></td>
                </tr>
            </template>
        </table>
        <form enctype="multipart/form-data" action="" method="post">
            <input type="hidden" name="id_task" value="<?=$id_task?>">
            <input type="file" name="userfile[]" class="input input_text" v-model="name" required>
            <button type="button" v-on:click="clear()" class="input input_button" style="width: 2rem; max-width: 2rem">X</button><br>
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
        name: '',
        upload_files: [],
        comment: '',
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
        checkForm: function (e) {
            if (
                (this.id_action != 7) &&
                (this.id_action != 6) &&
                (this.id_action != 1)
               ) {
                return true;
            }

            if (this.comment.length > 10) {
                return true;
            }

            e.preventDefault();
        },
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
        clear: function () {
            this.name = '';
        },
        getListFiles: function () {
            this.$http.get(this.server + 'getlistfiles/' + this.id_task).then(
                function (otvet) {
                    this.upload_files = otvet.data;
                },
                function (err) {
                    console.log(err);
                }
            );
        },
    },
    created: function() {
        this.getActions();
        this.getListFiles();
    }
});

</script>

</body>
</html>
