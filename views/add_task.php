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

    <form enctype="multipart/form-data" action="" method="post">

        <div style="display: flex;">

            <div style="margin-right: 5rem;">
                Задание:<br>
                <textarea name="task" required class="input" style="height: 21rem; box-sizing: border-box;"></textarea>
            </div>

            <div>
                Инициатор: <?=$name_user->getValue()?>
                <input type="hidden" value="<?=$id_user->getValue()?>" name="iniciator">
                <br>
                Потребитель:<br>

                <select name="client" required class="input input_text">
                    <option selected value="<?=$id_user->getValue()?>"><?=$name_user->getValue()?></option>
                    <?php
                        foreach ($list_users as $user) {
                            // по умолчанию инициатор является потребителем
                            echo '<option value="' . $user['id_user'] . '">';
                            echo $user["name"];
                            echo '</option>';
                        }
                     ?>
                </select>
                <br>
                Исполнитель:<br>
                <select name="executor" required class="input input_text">
                    <option value="">выберете исполнителя</option>
                    <?php
                        foreach ($list_users as $user) {
                            echo '<option value="' . $user['id_user'] . '">';
                            echo $user["name"];
                            echo '</option>';
                        }
                     ?>
                </select>
                <br>
                Контролер:<br>
                <select name="controller" required class="input input_text">
                    <option value="">выберете контролера</option>
                    <?php
                        foreach ($list_controllers as $user) {
                            echo '<option value="' . $user['id_user'] . '">';
                            echo $user["name"];
                            echo '</option>';
                        }
                     ?>
                </select>
                <br>
                Дата начало:<br>
                <input type="date" name="data_beg" value="<?=$cur_date->format('Y-m-d')?>" required class="input input_text">
                <br>
                Срок исполнения:<br>
                <input type="date" name="data_end" value="<?=$fin_date->format('Y-m-d')?>" required class="input input_text">
                <br>
                Штрафные баллы:<br>
                <input type="number" name="penalty" value="0" required class="input input_text">
                <div>
                    <input type="file" name="userfile[]" v-model="name1" class="input input_text">
                </div>
                <div v-if="file2">
                    <input type="file" name="userfile[]" v-model="name2" class="input input_text">
                </div>
                <div v-if="file3">
                    <input type="file" name="userfile[]" v-model="name3" class="input input_text">
                </div>
                <div v-if="file4">
                    <input type="file" name="userfile[]" v-model="name4" class="input input_text">
                </div>
                <div v-if="file5">
                    <input type="file" name="userfile[]" class="input input_text">
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
        file2: false,
        file3: false,
        file4: false,
        file5: false,
        name1: '',
        name2: '',
        name3: '',
        name4: '',
    },
    watch: {
        name1: function () {
            if (this.name1 != '') {
                this.file2 = true;
            }
        },
        name2: function () {
            if (this.name2 != '') {
                this.file3 = true;
            }
        },
        name3: function () {
            if (this.name3 != '') {
                this.file4 = true;
            }
        },
        name4: function () {
            if (this.name4 != '') {
                this.file5 = true;
            }
        },
    },
});

</script>
</body>
</html>
