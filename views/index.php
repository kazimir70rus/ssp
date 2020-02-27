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
    
    <div class="container">
        <div class="item" style="border: 1px solid red">
        12
        </div>
        <div class="item" style="border: 1px solid green">
        12
        </div>
    </div>

    <div class="container">
        <div class="item" style="border: 1px solid blue">
        12
        </div>
        <div class="item" style="border: 1px solid black">
        12
        </div>
    </div>
    
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
            <th>Дата исполнения</th>
            <th>Задание</th>
            <th>Результат</th>
        </tr>

        <?php
            $i = 0;
            foreach ($author_tasks as $one_task) {
                echo '<tr>';
                echo '<td>', $one_task['data_end'], '</td>';
                echo '<td>', $one_task['name'], '</td>';
                echo '<td></td>';
                echo '</tr>';
        }
        ?>
    </table>
 
</body>
</html>