<?php

$data = json_decode(file_get_contents('php://input'), true);

$task = new \ssp\models\Task($db);

// формируем список задач для отображения на главном экране
\ssp\module\Tools::send_json($task->getTasksForControl($id_user->getValue(), $data));

