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
        <input type="text" name="task">
        <br>
        Ответственный:
        <select name="executor">
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
        <input type="date" name="data_beg">
        <br>
        Срок исполнения:
        <input type="date" name="data_end">
        <br>
        <input type="submit" name="submit" value="Добавить">

    </form>

</body>
</html>
