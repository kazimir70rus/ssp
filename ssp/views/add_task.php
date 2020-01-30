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
        <input type="text" name="task">

        <select name="executor">
            <?php
                foreach ($list_users as $user) {

                    echo '<option value="' . $user['id_user'] . '">';
                    echo $user["name"];
                    echo '</option>';
                }

             ?>
        </select>
        <input type="submit" name="submit" value="Добавить">
    </form>

</body>
</html>
