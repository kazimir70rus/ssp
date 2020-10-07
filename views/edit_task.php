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
    <form method="post">
        <input type="hidden" value="<?=$id_task?>" name="id_task">
        <div style="display: flex;">

            <div style="margin-right: 5rem;">
                Задание:<br>
                <textarea name="task" required class="input" style="height: 21rem; box-sizing: border-box;"><?=$task_info['name']?></textarea>
            </div>

            <div style="margin-right: 5rem;">
                Потребитель:<br>
                <select name="id_client" required class="input input_text">
                    <option value="">выберете потребителя</option>
                    <?php
                        foreach ($list_users as $user) {
                            $selected = $user['id_user'] == $task_info['id_client'] ? ' selected ' : ''; ?>
                            <option value="<?=$user['id_user']?>" <?=$selected?>><?=$user["name"]?></option>
                    <?php
                        }
                     ?>
                </select>
                <br>
                Исполнитель:<br>
                <select name="id_executor" required class="input input_text">
                    <option value="">выберете исполнителя</option>
                    <?php
                        foreach ($list_users as $user) {
                            $selected = $user['id_user'] == $task_info['id_executor'] ? ' selected ' : ''; ?>
                            <option value="<?=$user['id_user']?>" <?=$selected?>><?=$user["name"]?></option>
                    <?php
                        }
                     ?>
                </select>
                <br>
                Контролер:<br>
                <select name="id_controller" required class="input input_text">
                    <option value="">выберете контролера</option>
                    <?php
                        foreach ($list_controllers as $user) {
                            $selected = $user['id_user'] == $task_info['id_controller'] ? ' selected ' : ''; ?>
                            <option value="<?=$user['id_user']?>" <?=$selected?>><?=$user["name"]?></option>
                    <?php
                        }
                     ?>
                </select>
                <br>
                Вид результата:<br>
                <input type="text" v-model="type_result" name="type_result" class="input input_text" autocomplete="off"><br>
                <select v-if="res_visible" v-model="name_result" size="5" class="input" style="height: 90px" v-on:click="hide()">
                    <option v-for="res in type_results" v-bind:value="res.name">{{res.name}}</option>
                </select>
                Вид отчета:<br>
                <select name="id_report" required class="input input_text">
                    <option value="">выберете вид отчета</option>
                    <?php
                        foreach ($list_report as $type_report) {
                            $selected = $type_report['id_report'] == $task_info['id_report'] ? ' selected ' : ''; ?>
                            <option value="<?=$type_report['id_report']?>" <?=$selected?>><?=$type_report["name"]?></option>
                    <?php
                        }
                     ?>
                </select>
                <br>
                Штрафные баллы:<br>
                <input type="number" name="penalty" value="<?=$task_info['penalty']?>" required class="input input_text">
                <br>
                <input type="submit" name="save" value="Сохранить" class="input input_button">
                <br>
                <a href="<?=BASE_URL?>"><button type="button" class="input input_button">Отменить</button></a>
            </div>

            <div style="flex-grow: 1">
                <div v-if="!enable_ends">
                    Дата начала:<br>
                    <input type="date" name="data_beg" value="<?=$task_info['data_begin']?>" required class="input input_text">
                </div>
                Срок исполнения:<br>
                <input type="date" name="data_end" value="<?=$task_info['data_end']?>" required class="input input_text">
                <br>
                периодичность:<br>
                <input type="radio" v-model="repetition" name="repetition" value="1"> разовая<br>
                <input type="radio" v-model="repetition" name="repetition" value="2"> ежедневно<br>
                <input type="radio" v-model="repetition" name="repetition" value="3"> еженeдельно<br>
                <input type="radio" v-model="repetition" name="repetition" value="4"> ежемесячно<br>
                <input type="radio" v-model="repetition" name="repetition" value="7"> ежеквартально<br>
                <input type="radio" v-model="repetition" name="repetition" value="5"> ежегодно<br>
                <input type="radio" v-model="repetition" name="repetition" value="6"> через 
                <input type="number" name="period" class="input input_text" style="width: 5rem;"> дней
                <div v-if="enable_ends">
                    повторять до:<br>
                    <input type="date" name="date_to" value="<?=$task_info['date_to']?>" class="input input_text">
                </div>
            </div>
        </div>
    </form>
</div>

<script src="<?=BASE_URL?>js/vue.min.js"></script>
<script src="<?=BASE_URL?>js/vue-resource.min.js"></script>

<script>

var app = new Vue({
    el: '#app',
    data: {
        server: '<?=BASE_URL?>',
        type_results: [],
        name_result: '',
        type_result: '<?=$type_result?>',
        res_visible: false,
        enable_ends: false,
        repetition: 1, 
    },
    watch: {
        repetition: function () {
            this.enable_ends = this.repetition == '1' ? false : true;
        },
        type_result: function () {
            if ((this.type_result.length > 1) && (this.type_result != this.name_result)) {
                this.seek_type_result();
            } else {
                this.res_visible = false;
            }
        },
    },
    methods: {
        seek_type_result: function () {
            this.$http.get(this.server + 'gettyperesults/' + this.type_result).then(
                function (otvet) {
                    this.type_results = otvet.data;

                    if (this.type_results.length > 0) {
                        this.res_visible = true;
                    } else {
                        this.res_visible = false;
                    }
                },
                function (err) {
                    console.log(err);
                }
            );
        },
        hide: function() {
            this.res_visible = false;
            this.type_result = this.name_result;
        },
    },
    created: function (){
        this.repetition = <?=$task_info['repetition']?>;
},
});

</script>
</body>
</html>
