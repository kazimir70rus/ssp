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
    </style>    
</head>
<body>

<?php require_once 'logout.html';
//    с <input type="date" class="input input_text medium"> по <input type="date" class="input input_text medium"><br>
?>

<div id="app">
    <br><a href="<?=BASE_URL?>add_task"><b>Создать новую задачу</b></a><br><br>    

    поиск по наименованию: <input type="text" v-model="seek_str" class="input input_text">
    <input type="radio" v-model="common_filter" value="1"> новые
    <input type="radio" v-model="common_filter" value="2"> просроченные
    <input type="radio" v-model="common_filter" value="3"> ожидают согласования
    <input type="radio" v-model="common_filter" value="4"> завершенные и отмененные
    <input type="radio" v-model="common_filter" value="5"> все, кроме завершенных и отмененных

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
    },
    watch: {
        seek_str: function () {
            if (this.seek_str.length > 3) {
                this.getListTasksExe(this.seek_str);
                this.getListTasksCtr(this.seek_str);
            }
        },
        common_filter: function () {

            document.cookie = "common_filter=" + this.common_filter + "; SameSite=Strict";

            // список задач к выполнению
            this.getListTasksExe();

            // список задач для контроля
            this.getListTasksCtr();
        },
    },
    methods: {
        getListTasksExe: function () {
            param = {'is_executor': 1, 'filter': this.common_filter, 'seek_str': this.seek_str};
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
            param = {'is_executor': 0, 'filter': this.common_filter, 'seek_str': this.seek_str};
            this.$http.post(this.server + 'getlisttasks/', param).then(
                function (otvet) {
                    this.tasks_for_ctr = otvet.data;
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
    },
    created: function() {

        this.readCookies();

        this.common_filter = this.getCookie('common_filter');

        // список задач к выполнению
        this.getListTasksExe();

        // список задач для контроля
        this.getListTasksCtr();
    }
});

</script>
</body>
</html>
