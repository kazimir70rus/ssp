<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <link href="<?=BASE_URL?>css/main.css" rel="stylesheet" type="text/css">
</head>
<body>

<?php require_once 'logout.html'; ?>
    <br>

    <a href="<?=BASE_URL?>add_task">Создать задачу</a>
    
    <br>
    <p>Список задач к исполнению:</p>
   <?php
        foreach ($list_tasks as $one_task) {
            echo '<div>';
            echo $one_task['name'];
            echo '</div>';
        }
     ?>    
    <br>
    <p>Список задач на контроле:</p>
    <?php
        foreach ($author_tasks as $one_task) {
            echo '<div>';
            echo $one_task['name'];
            echo '</div>';
        }
    ?>
     
</body>
</html>
