<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <link href="<?=BASE_URL?>css/main.css" rel="stylesheet" type="text/css">

<style>
    table {
        width: 39%; /* Ширина таблицы */
    }
</style>    

</head>
<body>

<?php require_once 'logout.html'; ?>
<br>

<table>
    <tr>
        <td width="25%">Срок:</td>
        <td><?=$task_info['data_end']?></td>
    </tr>
    <tr>
        <td>Дата начало:</td>
        <td><?=$task_info['data_begin']?></td>
    </tr>
    <tr>
        <td>Задание:</td>
        <td><b><?=$task_info['task_name']?></b></td>
    </tr>
    <tr>
        <td>Испольнитель:</td>
        <td><?=$task_info['executor']?></td>
    </tr>
    <tr>
        <td>Инициатор:</td>
        <td><?=$task_info['iniciator']?></td>
    </tr>
    <tr>
        <td>Потребитель:</td>
        <td><?=$task_info['client']?></td>
    </tr>
    <tr>
        <td>Контролер:</td>
        <td><?=$task_info['controller']?></td>
    </tr>
    <tr>
        <td>Дата исполнения:</td>
        <td><?=$task_info['data_execut']?></td>
    </tr>
    <tr>
        <td>Дата подтверждения:</td>
        <td><?=$task_info['data_client']?></td>
    </tr>
    <tr>
        <td>Статус:</td>
        <td><?=$task_info['primet']?></td>
    </tr>
    <tr>
        <td>Состояние:</td>
        <td><?=$task_info['state']?></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td>
            <form>
                <select name="action" required>
                    <option value="">выберете действие</option>
                    <?php
                        foreach ($list_actions as $action) {
                            echo '<option value="' . $action['id_action'] . '">';
                            echo $action['name'];
                            echo '</option>';
                        }
                    ?>
                </select>
        </td>
        <td>
            <input type="submit" name="submit" value="Подтвердить">
            </form>
        </td>
    </tr>

</table>

<!--
<div>Срок: <?=$task_info['data_end']?></div>
<div>Дата начало: <?=$task_info['data_begin']?></div>
<div>Задание: <b><?=$task_info['task_name']?></b></div>
<div>Испольнитель: <?=$task_info['executor']?></div>
<div>Инициатор: <?=$task_info['iniciator']?></div>
<div>Потребитель: <?=$task_info['client']?></div>
<div>Контролер: <?=$task_info['controller']?></div>
<div>Дата исполнения: <?=$task_info['data_execut']?></div>
<div>Дата подтверждения: <?=$task_info['data_client']?></div>
<div>Статус: <?=$task_info['primet']?></div>
<div>Состояние: <?=$task_info['state']?></div>
<br>

<form>
    <select name="action" required>
        <option value="">выберете действие</option>
        <?php
            foreach ($list_actions as $action) {
                echo '<option value="' . $action['id_action'] . '">';
                echo $action['name'];
                echo '</option>';
            }
         ?>
    </select>

<input type="submit" name="submit" value="Подтвердить">    

</form>

-->

</body>

</html>