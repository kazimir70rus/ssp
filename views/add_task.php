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
                Исполнитель:<br>
                <select name="executor" required class="input input_text">
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
                <input type="submit" name="submit" value="Добавить" class="input input_button">
            </div>

        </div>

    </form>

</body>
</html>
