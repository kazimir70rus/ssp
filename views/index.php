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

<?php require_once 'logout.html'; ?>
    <br>

    <a href="<?=BASE_URL?>add_task">Создать задачу</a>

    <br>    
    <br>    
    
    <div class="container">
        <div class="item" style="border: 1px solid red">
            <div><b>Список задач (Исполнитель):</b></div>
            <?php
                foreach ($list_tasks_executor as $one_task) {
                    echo '<div>';
                    echo $one_task['data_end'];                    
                    echo $one_task['name'];
                    echo '</div>';
                }
            ?>    
        </div>
        <div class="item" style="border: 1px solid green">
            <div><b>Список задач (Инициатор):</b></div>
            <?php
                foreach ($list_tasks_iniciator as $one_task) {
                    echo '<div>';
                    echo $one_task['data_end'];                    
                    echo $one_task['name'];
                    echo '</div>';
                }
            ?>    
        </div>
    </div>

    <div class="container">
        <div class="item" style="border: 1px solid blue">
            <div><b>Список задач (Потребитель):</b></div>
            <?php
                foreach ($list_tasks_client as $one_task) {
                    echo '<div>';
                    echo $one_task['data_end'];                    
                    echo $one_task['name'];
                    echo '</div>';
                }
            ?>    
        </div>
        <div class="item" style="border: 1px solid black">
            <div><b>Список задач (Котролер):</b></div>
            <?php
                foreach ($list_tasks_controller as $one_task) {
                    echo '<div>';
                    echo $one_task['data_end'];                    
                    echo $one_task['name'];
                    echo '</div>';
                }
            ?>    
        </div>
    </div>
     
</body>
</html>