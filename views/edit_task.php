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
        <input type="hidden" value="<?=$id_task?>" name="id_task">
        <div style="display: flex;">

            <div style="margin-right: 5rem;">
                Задание:<br>
                <textarea name="task" required class="input" style="height: 21rem; box-sizing: border-box;"><?=$task_info['name']?></textarea>
            </div>

            <div>
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
                Срок исполнения:<br>
                <input type="date" name="data_end" value="<?=$task_info['data_end']?>" required class="input input_text">
                <br>
                Штрафные баллы:<br>
                <input type="number" name="penalty" value="<?=$task_info['penalty']?>" required class="input input_text">
                <br>
                <input type="submit" name="save" value="Сохранить" class="input input_button">
                <br>
                <input type="submit" name="cancel" value="Отменить" class="input input_button">
            </div>
        </div>
    </form>
</body>
</html>
