<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <link href="<?=BASE_URL?>css/main.css" rel="stylesheet" type="text/css">

</head>
<body>

<?php require_once 'logout.html'; ?>

<div>Срок: <?=$task_info['data_end']?></div>
<div>Дата начало: <?=$task_info['data_begin']?></div>
<div>Задание: <?=$task_info['task_name']?></div>
<div>Испольнитель: <?=$task_info['executor']?></div>
<div>Инициатор: <?=$task_info['iniciator']?></div>
<div>Потребитель: <?=$task_info['client']?></div>
<div>Контролер: <?=$task_info['controller']?></div>
<div>Дата исполнения: <?=$task_info['data_execut']?></div>
<div>Дата подтверждения: <?=$task_info['data_client']?></div>
<div>Статус: <?=$task_info['primet']?></div>     

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
</form>

</body>
</html>