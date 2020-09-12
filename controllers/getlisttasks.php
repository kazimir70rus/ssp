<?php

$is_executor = isset($param[1]) ? (int)$param[1] : 0;
$seek_str = isset($param[2]) ? $param[2] : '';

$task = new \ssp\models\Task($db);

// формируем список задач для отображения на главном экране
\ssp\module\Tools::send_json($task->getTasksForControl($id_user->getValue(), $is_executor, $seek_str));

