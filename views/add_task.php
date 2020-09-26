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

    <form enctype="multipart/form-data" action="" method="post" @submit="checkForm">

        <div style="display: flex;">

            <div style="margin-right: 5rem;">
                Задание:<br>
                <textarea name="task" required class="input" style="height: 21rem; box-sizing: border-box;"></textarea>
            </div>

            <div>
                Инициатор:<br>
                <select v-model="iniciator" name="iniciator" class="input input-text">
                    <option v-for="iniciator in iniciators" v-bind:value="iniciator.id_user">{{iniciator.name}}</option>
                </select>
                <br>
                
                Потребитель:<br>
                <select v-model="client" name="client" class="input input-text" required>
                    <option v-for="client in clients" v-bind:value="client.id_user">{{client.name}}</option>
                </select>
                <br>

                Исполнитель:<br>
                <select v-model="executor" name="executor" class="input input-text" required>
                    <option v-for="executor in executors" v-bind:value="executor.id_user">{{executor.name}}</option>
                </select>
                <br>

                Контролер:<br>
                <select v-model="controller" name="controller" class="input input-text" required>
                    <option v-for="controller in controllers" v-bind:value="controller.id_user">{{controller.name}}</option>
                </select>
                <br>

                Дата начало:<br>
                <input type="date" name="data_beg" value="<?=$cur_date->format('Y-m-d')?>" required class="input input_text">
                <br>

                Срок исполнения:<br>
                <input type="date" name="data_end" value="<?=$fin_date->format('Y-m-d')?>" required class="input input_text">
                <br>

                Штрафные баллы:<br>
                <input type="number" name="penalty" v-model="penalty" required class="input input_text">
                <br>

                Вид результата:<br>
                <input type="text" v-model="type_result" name="type_result" class="input input_text" autocomplete="off"><br>
                <select v-if="res_visible" v-model="name_result" size="5" class="input" style="height: 90px" v-on:click="hide()">
                    <option v-for="res in type_results" v-bind:value="res.name">{{res.name}}</option>
                </select>
                <br>

                Вид отчета:<br>
                <select v-model="id_report" name="id_report" class="input input-text" required>
                    <option v-for="rep in type_reports" v-bind:value="rep.id_report">{{rep.name}}</option>
                </select>
                <br>

                <div>
                    <input type="file" name="userfile[]" v-model="name1" class="input input_text">
                    <button type="button" v-on:click="clear1()" class="input input_button" style="width: 2rem; max-width: 2rem">X</button>
                </div>

                <div v-if="file2">
                    <input type="file" name="userfile[]" v-model="name2" class="input input_text">
                    <button type="button" v-on:click="clear2()" class="input input_button" style="width: 2rem; max-width: 2rem">X</button>
                </div>

                <div v-if="file3">
                    <input type="file" name="userfile[]" v-model="name3" class="input input_text">
                    <button type="button" v-on:click="clear3()" class="input input_button" style="width: 2rem; max-width: 2rem">X</button>
                </div>

                <div v-if="file4">
                    <input type="file" name="userfile[]" v-model="name4" class="input input_text">
                    <button type="button" v-on:click="clear4()" class="input input_button" style="width: 2rem; max-width: 2rem">X</button>
                </div>

                <input type="submit" name="submit" value="Добавить" class="input input_button">
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
        name1: '',
        name2: '',
        name3: '',
        name4: '',
        file2: false,
        file3: false,
        file4: false,
        iniciators: [],
        iniciator: '',
        executors: [],
        executor: '',
        controllers: [],
        controller: '',
        clients: [],
        client: '',
        type_results: [],
        name_result: '',
        type_result: '',
        res_visible: false,
        type_reports: [],
        id_report: '',
        penalty: 1,
    },
    watch: {
        type_result: function () {
            if ((this.type_result.length > 1) && (this.type_result != this.name_result)) {
                this.seek_type_result();
            } else {
                this.res_visible = false;
            }
        },
        iniciator: function () {
            this.getExecutors(this.iniciator);
            this.getControllers(this.iniciator);
        },
        name1: function () {
            if (this.name1 != '') {
                this.file2 = true;
            } else if (this.name2 == '') {
                this.file2 = false;
            }
        },
        name2: function () {
            if (this.name2 != '') {
                this.file3 = true;
            } else {
                if (this.name3 == '') {
                    this.file3 = false;
                }

                if (this.name1 == '') {
                    this.file2 = false;
                }
            }
        },
        name3: function () {
            if (this.name3 != '') {
                this.file4 = true;
            } else {
                if (this.name4 == '') {
                    this.file4 = false;
                }

                if (this.name2 == '') {
                    this.file3 = false;
                }
            }
        },
        name4: function () {
            if (this.name3 == '') {
                if (this.name3 == '') {
                    this.file4 = false;
                }
            }
        },
    },
    methods: {
        clear1: function () {
            this.name1 = '';
        },
        clear2: function () {
            this.name2 = '';
        },
        clear3: function () {
            this.name3 = '';
        },
        clear4: function () {
            this.name4 = '';
        },
        getIniciators: function () {
            this.$http.get(this.server + 'getiniciators').then(
                function (otvet) {
                    this.iniciators = otvet.data;
                    this.iniciator = this.iniciators[0].id_user;
                },
                function (err) {
                    console.log(err);
                }
            );
        },
        checkForm: function (e) {
            if (parseInt(this.penalty) > 0) {
                return true;
            }

            e.preventDefault();
        },
        getExecutors: function (iniciator) {
            this.$http.get(this.server + 'getexecutors/' + iniciator).then(
                function (otvet) {
                    this.executors = otvet.data;

                    // если исполнитель один его и выбираем в противном случае
                    // выводим предложение о выборе
                    if (this.executors.length > 1) {
                        this.executors.unshift({name: 'выберите исполнителя', id_user: ''})
                    }
                    this.executor = this.executors[0].id_user;

                    this.getClients(this.iniciator);
                },
                function (err) {
                    console.log(err);
                }
            );
        },
        getControllers: function (iniciator) {
            this.$http.get(this.server + 'getcontrollers/' + iniciator).then(
                function (otvet) {
                    this.controllers = otvet.data;

                    // если исполнитель один его и выбираем в противном случае
                    // выводим предложение о выборе
                    if (this.controllers.length > 1) {
                        this.controllers.unshift({name: 'выберите контроллера', id_user: ''})
                    }
                    this.controller = this.controllers[0].id_user;
                },
                function (err) {
                    console.log(err);
                }
            );
        },
        getIndex: function (value, massiv) {
            for (let i = 0; i < massiv.length; ++i) {
                if (massiv[i].id_user == value) {
                    return i;
                }
            }

            return '';
        },
        getClients: function (iniciator) {
            // потребители выбираются из числа подчиненных плюс инициатор
            this.$http.get(this.server + 'getexecutors/' + iniciator).then(
                function (otvet) {
                    this.clients = otvet.data;

                    // по умолчанию инициатор является потребителем
                    this.clients.unshift(this.iniciators[this.getIndex(this.iniciator, this.iniciators)]);
                    this.client = this.clients[0].id_user;
                },
                function (err) {
                    console.log(err);
                }
            );
        },
        getGuide: function () {
            this.$http.get(this.server + 'gettypereports/').then(
                function (otvet) {
                    this.type_reports = otvet.data;
                },
                function (err) {
                    console.log(err);
                }
            );
        },
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
    created: function () {
        this.getIniciators();
        this.getGuide();
    },
});

</script>
</body>
</html>
