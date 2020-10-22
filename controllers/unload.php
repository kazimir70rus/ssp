<?php

$task = new \ssp\models\Task($db);

$data = $task->getReport($id_user->getValue());

$content = "Тип;Дата создания;Время создания;Срок;Задача;Инициатор;Потребитель;Исполнитель;Контроллер;Вид результата;Вид отчета;Вес;Состояние;Дата исполнения;Дата подтверждения;Штрафы;\n";

foreach ($data as $row) {
    $content .= $row['periodicity'] . ';';
    $content .= $row['data_create'] . ';';
    $content .= $row['time_create'] . ';';
    $content .= $row['data_end'] . ';';
    $content .= $row['name'] . ';';
    $content .= $row['name_iniciator'] . ';';
    $content .= $row['name_client'] . ';';
    $content .= $row['name_executor'] . ';';
    $content .= $row['name_controller'] . ';';
    $content .= $row['name_result'] . ';';
    $content .= $row['name_report'] . ';';
    $content .= $row['penalty'] . ';';
    $content .= $row['condition'] . ';';
    $content .= $row['data_execut'] . ';';
    $content .= $row['data_client'] . ';';
    $content .= $row['charges_penalty'] . ';';
    $content .= "\n";
}

\ssp\module\Tools::save_CSV($content, 'reestr.csv');

