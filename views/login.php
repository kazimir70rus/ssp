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
        <select v-model="p1">
            <option v-for="org in organisations" v-bind:value="org.id">
            {{org.name}}
            </option>
        </select>
        <input type="text" name="login">
        <input type="password" name="pass">
        <input type="submit" name="submit" value="Вход">
    </form>

</div>

<script src="/ssp/js/vue.min.js"></script>
<script src="/ssp/js/vue-resource.min.js"></script>

<script>

Vue.use( VueResource );
var app = new Vue({
    el: '#app',
    data: {
        organisations: [],
        p1: '',
    },
    methods: {
    },
    created: function() {
        this.organisations.push({id: 1, name: 'Холдинг'});
        this.organisations.push({id: 2, name: 'Ладья'});
        this.organisations.push({id: 3, name: 'Управдом'});        
        console.log(this.organisations[1].name);
    }
})
</script>


</body>
</html>
