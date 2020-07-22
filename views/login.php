<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <link href="<?=BASE_URL?>css/main.css" rel="stylesheet" type="text/css">
</head>
<body>

<div id="app">

<form method="post">

    <h3>Выберите ваше подразделение</h3><br>

    <dl>
        <dt><label for="organisation" class="fnt-mid">Подразделение:</label></dt>
        <dd>
            <select v-model="organisation" id="organisation" class="input input_text">
                <option value="" >Выберите подразделение</option>
                <option v-for="org in organisations" v-bind:value="org.id">
                {{org.name}}
                </option>
            </select>
        <dd>
        <dt><label for="login" class="fnt-mid">Ф.И.О. сотрудника:</label></dt>
        <dd>
            <select v-model="login" id="login" name="login" class="input input_text">
                <option value="" >Выберите ФИО</option>
                <option v-for="i_name in names" v-bind:value="i_name.name" class="fnt-mid">
                    {{i_name.name}}
                </option>
            </select>
        </dd>
        <dt><label for="pass" class="fnt-mid">Введите пароль:</label>
<!--        <dd>
            <input type="password" name="pass" v-model="pass" id="pass" class="inp inp_txt fnt-mid">
        </dd>   -->
        <dd>
        <input type="password" name="pass" class="input input_text"> 
        </dd>
 </dl>    
    <div class="wdth">
        <h3 class="msg">{{ message }}</h3>
    </div>
<!--    <input type="button" class="btn fnt-mid" v-on:click="enter" value="Вход"> -->
    
    <input type="submit" name="submit" value="Вход" class="input input_button">

</form>
<!--    
    <form method="post">
        <select v-model="p1">
            <option v-for="org in organisations" v-bind:value="org.id">
            {{org.name}}
            </option>
        </select>
        <input type="text" name="login">
        <input type="password" name="pass">
        <input type="submit" name="submit" value="Вход">
    </form>

-->

</div>

<script src="/ssp/js/vue.min.js"></script>
<script src="/ssp/js/vue-resource.min.js"></script>

<script>

Vue.use( VueResource );
var app = new Vue({
    el: '#app',
    data: {
        organisations: [],
        organisation: '',
        names: [],
        login: '',
        message: '',
    },
    watch: {
        organisation: function() {
            this.getNames();
        },
    },
    methods: {
        getOrganisations: function() {
            this.organisations.push({id: 1, name: 'Холдинг'});
            this.organisations.push({id: 2, name: 'Ладья'});
            this.organisations.push({id: 3, name: 'Управдом'});
            this.organisations.push({id: 4, name: 'Сорнет'});
        },

        getNames: function() {

            if (this.organisation == 1) {
                this.names = [];
                this.names.push({id: 1, name: 'Директор'});
                this.names.push({id: 10, name: 'Контролер1'});
            }

            if (this.organisation == 2) {
                this.names = [];
                this.names.push({id: 2, name: 'Гл.бухгалтер'});
                this.names.push({id: 7, name: 'Бухгалтер1'});
                this.names.push({id: 8, name: 'Бухгалтер2'});
            }

            if (this.organisation == 3) {
                this.names = [];
                this.names.push({id: 3, name: 'Экономист'});
                this.names.push({id: 9, name: 'Аналитик1'});
            }

            if (this.organisation == 4) {
                this.names = [];
                this.names.push({id: 5, name: 'Слесарь'});
                this.names.push({id: 6, name: 'Электрик'});
            }

            console.log(this.organisation);
        },
    },
    created: function() {
        this.getOrganisations();
    }
})
</script>


</body>
</html>
