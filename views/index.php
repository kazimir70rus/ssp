<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <link href="<?=BASE_URL?>css/main.css" rel="stylesheet" type="text/css">

    <style>
        table {
            width: 100%; /* Ширина таблицы */
        }
        th {
            background: green; /* Цвет фона ячеек */
            color: white; /* Цвет текста */
        }
        caption {
            text-align: left;
            font-size: 1.5rem;
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

</head>
<body>

<?php require_once 'logout.html';

function task_out($tasks, $header)
{
    if (count($tasks)) {
        echo '<table>';
        echo "<caption>{$header}</caption>";
        echo '<tr>';
        echo '<th>срок</th>';
        echo '<th>задача</th>';
        echo '<th>статус</th>';
        echo '</tr>';
        foreach ($tasks as $task) {
            echo '<tr>';
            echo "<td>{$task['data_end']}</td>";                    
            echo '<td><a href="' . BASE_URL . 'task/' . $task['id_task'] . '">';
            echo $task['name'];
            echo '</a></td>';
            echo "<td>{$task['condition']}</td>";
            echo '</tr>';
        }
        echo '</table>';
    }
}

?>
    <br>

    <a href="<?=BASE_URL?>add_task"><b>Создать новую задачу</b></a>

    <br>    
    <br>    
    <div>
        <?php
            $header = 'задачи к выполнению';
            task_out($list_tasks_executor, $header);
        ?>    
    </div>

    <div>
        <?php
            $header = 'задачи на контролe';
            task_out($list_tasks_for_control, $header);
        ?>    
    </div>
     
</body>
</html>
