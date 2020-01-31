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
    
    </style>    
<!-- --> 

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
            <th>№</th>
            <th>Дата исполнения</th>
            <th>Дата начала</th>
            <th>Задание</th>
            <th>Ответственный</th>
            <th>Вид результата</th>
            <th>Вид отчета</th>
            <th>Потребитель</th>
            <th>Контролер</th>
            <th>Результат</th>
        </tr>

        <?php
            $i = 0;
            foreach ($author_tasks as $one_task) {
                $i++;
                echo '<tr>';
                echo '<td>', $i, '</td>';
                echo '<td>', $one_task['data_end'], '</td>';
                echo '<td></td>';
                echo '<td>', $one_task['name'], '</td>';
                echo '<td>', $one_task['fio_executor'], '</td>';
                echo '<td></td>';
                echo '<td></td>';
                echo '<td></td>';
                echo '<td></td>';
                echo '<td></td>';
                echo '</tr>';
        }
        ?>
    </table>
 
</body>
</html>