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
    <div><b><?=$zagolovok?></b></div>
    <br>
    <?php
        foreach ($list_tasks as $one_task) {
            echo '<div>';
            echo $one_task['data_end'];                    
            echo $one_task['name'];
            echo '</div>';
        }
            ?>    
     
</body>
</html>