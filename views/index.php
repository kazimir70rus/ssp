<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <link href="<?=BASE_URL?>css/main.css" rel="stylesheet" type="text/css">

    <style>
        table {
            width: 100%; /* Ширина таблицы */
        }
        th {
            background: green; /* Цвет фона ячеек */
            color: white; /* Цвет текста */
        }
        td {
            padding: 0.3rem;
        }
        caption {
            text-align: left;
            font-size: 1.5rem;
        }
        .container {
          display: flex;
        }
        .item {
            width: 49%;
            padding: 1rem;
        }

        @media screen and (max-width: 500px) {
            .container {
                display: flex;
                flex-wrap: wrap;
            }
            .item {
                width: 99%;
            }
        }

        .container {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .container div {
            padding: 0 0.5rem;
        }    
    </style>    
</head>
<body>

<?php require_once 'logout.html';?>

<div id="app">

    <div class="container">
        <div><a href="<?=BASE_URL?>add_task"><b>Создать новую задачу</b></a></div>
        <div><a href="#" v-on:click="reset_filter"><b>Сброс фильтра</b></a></div>
    </div>

    <br>

    <div class="container">
        <div style="min-width: 26rem;flex-grow: 1;">
            наименование:
            <input type="text" v-model="seek_str" class="input input_text">
            <a href="#" v-on:click="clearSeek">X</a>
        </div>
        <div>
            задачи:
            <select v-model="common_filter" class="input input_text">
                <option value="1"> новые</option>
                <option value="2"> просроченные</option>
                <option value="3"> ожидают согласования</option>
                <option value="4"> завершенные и отмененные</option>
                <option value="5"> все, кроме завершенных и отмененных</option>
            </select>
        </div>
        <div>
            исполнитель:
            <select v-model="id_executor" class="input input_text small">
                <option value="">Все</option>
                <option v-for="executor in executors" v-bind:value="executor.id_user">{{executor.name}}</option>
            </select>
        </div>
        <div>
            с 
            <input type="date" v-model="date_from" class="input input_text medium">
            по
            <input type="date" v-model="date_to" class="input input_text medium">
        </div>
    </div>
    <table v-if="tasks_for_exe.length">
        <caption>задачи к выполнению</caption>
        <tr>
            <th style="width: 1.5rem"></th>
            <th style="width: 6rem">срок</th>
            <th>задача</th>
            <th style="width: 3rem">пот.</th>
            <th style="width: 3rem">вес</th>
            <th style="width: 25rem">состояние</th>
            <th style="width: 2rem">Ш</th>
        </tr>
        <template v-for="task in tasks_for_exe">
            <tr>
                <td>{{task.periodicity}}</td>
                <td>{{task.data_end}}</td>
                <td>
                    <a v-bind:href="'<?=BASE_URL?>task/' + task.id_task">
                        {{task.name}}
                    </a>
                </td>
                <td>{{task.name_client}}</td>
                <td>{{task.penalty}}</td>
                <td>{{task.condition}}</td>
                <td>{{task.charges_penalty}}</td>
            </tr>
        </template>
    </table>

    <table v-if="tasks_for_ctr.length">
        <caption>задачи на контролe</caption>
        <tr>
            <th style="width: 1.5rem"></th>
            <th style="width: 6rem">срок</th>
            <th>задача</th>
            <th style="width: 3rem">исп.</th>
            <th style="width: 3rem">пот.</th>
            <th style="width: 3rem">вес</th>
            <th style="width: 25rem">состояние</th>
            <th style="width: 2rem">Ш</th>
        </tr>
        <template v-for="task in tasks_for_ctr">
            <tr>
                <td>{{task.periodicity}}</td>
                <td>{{task.data_end}}</td>
                <td>
                    <a v-bind:href="'<?=BASE_URL?>task/' + task.id_task">
                        {{task.name}}
                    </a>
                </td>
                <td>{{task.name_executor}}</td>
                <td>{{task.name_client}}</td>
                <td>{{task.penalty}}</td>
                <td>{{task.condition}}</td>
                <td>{{task.charges_penalty}}</td>
            </tr>
        </template>
    </table>
</div>

<script src="<?=BASE_URL?>js/vue.min.js"></script>
<script src="<?=BASE_URL?>js/vue-resource.min.js"></script>

<script>

var app = new Vue({
    el: '#app',
    data: {
        server: '<?=BASE_URL?>',
        tasks_for_exe: [],
        tasks_for_ctr: [],
        seek_str: '',
        common_filter: 5,
        cookies: [],
        executors: [],
        id_executor: '',
        date_from: '',
        date_to: '',
    },
    watch: {
        seek_str: function () {
            if (this.seek_str.length > 2) {
                document.cookie = "seek_str=" + this.seek_str + "; SameSite=Strict";
                this.updateListTasks();
            }
        },
        common_filter: function () {
            document.cookie = "common_filter=" + this.common_filter + "; SameSite=Strict";
            this.updateListTasks();
        },
        id_executor: function () {
            document.cookie = "id_executor=" + this.id_executor + "; SameSite=Strict";
            this.updateListTasks();
        },
        date_from: function () {
            document.cookie = "date_from=" + this.date_from + "; SameSite=Strict";
            this.updateListTasks();
        },
        date_to: function () {
            document.cookie = "datei_to=" + this.datei_to + "; SameSite=Strict";
            this.updateListTasks();
        },
    },
    methods: {
        clearSeek: function () {
            this.seek_str = ''; 
            document.cookie = "seek_str=" + this.seek_str + "; SameSite=Strict";
            this.updateListTasks();
        },
        reset_filter: function () {
            this.common_filter = 5;
            this.seek_str = '';
            this.id_executor = '';
            this.date_from = '';
            this.date_to = '';
        },
        getListTasksExe: function () {
            param = {
                'is_executor': 1,
                'filter'     : this.common_filter,
                'seek_str'   : this.seek_str,
                'id_executor': this.id_executor,
                'date_from'  : this.date_from,
                'date_to'    : this.date_to,
            };
            this.$http.post(this.server + 'getlisttasks/', param).then(
                function (otvet) {
                    this.tasks_for_exe = otvet.data;
                },
                function (err) {
                    console.log(err);
                }
            );
        },
        getListTasksCtr: function () {
            param = {
                'is_executor': 0, 
                'filter'     : this.common_filter, 
                'seek_str'   : this.seek_str,
                'id_executor': this.id_executor,
                'date_from'  : this.date_from,
                'date_to'    : this.date_to,
            };
            this.$http.post(this.server + 'getlisttasks/', param).then(
                function (otvet) {
                    this.tasks_for_ctr = otvet.data;
                },
                function (err) {
                    console.log(err);
                }
            );
        },
        getExesForControl: function () {
            this.$http.get(this.server + 'getexesforcontrol/').then(
                function (otvet) {
                    this.executors = otvet.data;
                },
                function (err) {
                    console.log(err);
                }
            );
        },
        readCookies: function () {
            const raw_str = document.cookie;

            const raw_arr = raw_str.split('; ');

            this.cookies = [];

            for (let i = 0; i < raw_arr.length; ++i) {
                el = raw_arr[i].split('=');
                this.cookies.push({name: el[0], value: el[1]});
            }
        },
        getCookie: function (name, value) {
            for (let i = 0; i < this.cookies.length; ++i) {
                if (this.cookies[i].name == name) {
                    return this.cookies[i].value;
                }
            }

            return value;
        },
        updateListTasks: function () {
            // список задач к выполнению
            this.getListTasksExe();

            // список задач для контроля
            this.getListTasksCtr();
        },
    },
    created: function() {

        this.readCookies();

        this.common_filter = this.getCookie('common_filter', 5);
        this.seek_str = this.getCookie('seek_str', '');
        this.id_executor = this.getCookie('id_executor', '');
        this.date_from = this.getCookie('date_from', '');
        this.date_to = this.getCookie('date_to', '');

        // список исполнитлей
        this.getExesForControl();

        this.updateListTasks();
    }
});

</script>
</body>
</html>
