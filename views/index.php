<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <link href="<?=BASE_URL?>css/main.css" rel="stylesheet" type="text/css">

<!--    убрал, так как все это от лукавого... Саша прости.... вернул )  -->
<style>
    table {
        width: 100%; /* Ширина таблицы */
    }
    th {
        background: green; /* Цвет фона ячеек */
        color: white; /* Цвет текста */
    }

    .container {
      display: flex;
    }
    .item {
        width: 49%;
        padding: 1rem;
    }

    @media screen and (max-width: 500px) {
        .container {
            display: flex;
            flex-wrap: wrap;
        }
        .item {
            width: 99%;
        }
    }    
    </style>    
<!-- --> 

</head>
<body>

<?php require_once 'logout.html';

function task_out($tasks)
{
    $i = 0;
    foreach ($tasks as $task) {
        echo '<div><a href="' . BASE_URL . 'task/' . $task['id_task'] . '">';
        echo $task['data_end'];
        echo $task['name'];
        echo '</a></div>';
        echo $task['stat'];
    }
}

 ?>
    
    <br>

    <a href="<?=BASE_URL?>add_task"><b>Создать новую задачу</b></a>

    <br>    
    <br>    
    
    <div class="container">
        <div class="item" style="border: 1px solid red">
            <div><b>Список задач (<a href="<?=BASE_URL?>executor/<?=$id_user->getValue()?>">Исполнитель</a>):</b></div>
            <br>
            <?php
                task_out($list_tasks_executor);
            ?>    
        </div>
        <div class="item" style="border: 1px solid green">
            <div><b>Список задач (<a href="<?=BASE_URL?>iniciator/<?=$id_user->getValue()?>">Инициатор</a>):</b></div>
            <br>
            <?php
                task_out($list_tasks_iniciator);
            ?>    
        </div>
    </div>

    <div class="container">
        <div class="item" style="border: 1px solid blue">
            <div><b>Список задач (<a href="<?=BASE_URL?>client/<?=$id_user->getValue()?>">Потребитель</a>):</b></div>
            <br>
            <?php
                task_out($list_tasks_client);
            ?>    
        </div>
        <div class="item" style="border: 1px solid black">
            <div><b>Список задач (<a href="<?=BASE_URL?>controller/<?=$id_user->getValue()?>">Котролер</a>):</b></div>
            <br>            
            <?php
                task_out($list_tasks_controller);
            ?>    
        </div>
    </div>
     
</body>
</html>