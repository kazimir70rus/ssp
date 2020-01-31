<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <link href="<?=BASE_URL?>css/main.css" rel="stylesheet" type="text/css">

    <!--    убрал, так как все это от лукавого
<style>
    table {
        width: 100%; /* Ширина таблицы */
    }
    th {
        background: maroon; /* Цвет фона ячеек */
        color: white; /* Цвет текста */
    }
    
    </style>    
 --> 
</head>
<body>

<?php require_once 'logout.html'; ?>
    <br>

    <a href="<?=BASE_URL?>add_task">Создать задачу</a>
    
    <br>
    <p><b>Список задач к исполнению:</b></p>
   <?php
        foreach ($list_tasks as $one_task) {
            echo '<div>';
            echo $one_task['name'];
            echo '</div>';
        }
     ?>    
    <br>    
    <p><b>Список задач на контроле:</b></p>

    <table border="0">
        <tr>
            <th>Сделать</th>
            <th>Исполнитель</th>
            <th>Дата исполнения</th>
        </tr>

        <?php
            foreach ($author_tasks as $one_task) {
                echo '<tr>';
                echo '<td>', $one_task['name'], '</td>';
                echo '<td>', $one_task['fio_executor'], '</td>';
                echo '<td>', $one_task['data_end'], '</td>';
                echo '</tr>';
        }
        ?>
    </table>
 
</body>
</html>