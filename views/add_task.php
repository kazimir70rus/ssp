<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <link href="<?=BASE_URL?>css/main.css" rel="stylesheet" type="text/css">
</head>
<body>

<?php require_once 'logout.html'; ?>

    <form method="post">
        Задание:
        <input type="text" name="task" required>
        <br>
        Инициатор:
        <select name="iniciator" required>
            <option value="">выберете инициатора</option>
            <?php
                foreach ($list_users as $user) {

                    echo '<option value="' . $user['id_user'] . '">';
                    echo $user["name"];
                    echo '</option>';
                }
             ?>
        </select>
        <br>
        Потребитель:
        <select name="client" required>
            <option value="">выберете потребителя</option>
            <?php
                foreach ($list_users as $user) {

                    echo '<option value="' . $user['id_user'] . '">';
                    echo $user["name"];
                    echo '</option>';
                }
             ?>
        </select>
        <br>
        Исполнитель:
        <select name="executor" required>
            <option value="">выберете испольнителя</option>
            <?php
                foreach ($list_users as $user) {

                    echo '<option value="' . $user['id_user'] . '">';
                    echo $user["name"];
                    echo '</option>';
                }
             ?>
        </select>
        <br>
        Контролер:
        <select name="controller" required>
            <option value="">выберете контролера</option>
            <?php
                foreach ($list_users as $user) {

                    echo '<option value="' . $user['id_user'] . '">';
                    echo $user["name"];
                    echo '</option>';
                }
             ?>
        </select>
        <br>
        Дата начало:
        <input type="date" name="data_beg" value="<?=$cur_date->format('Y-m-d')?>" required>
        <br>
        Срок исполнения:
        <input type="date" name="data_end" value="<?=$fin_date->format('Y-m-d')?>" required>
        <br>
        <input type="submit" name="submit" value="Добавить">

    </form>

</body>
</html>
