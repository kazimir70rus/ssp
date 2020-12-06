<?php

$data = json_decode(file_get_contents('php://input'), true);

$task = new \ssp\models\Task($db);

// формируем список задач для отображения на главном экране
$result = $task->getTasksForControl($id_user->getValue(), $data);

// сохоаним список id задач для последующего формирования реестра задач
$id_tasks = [];

foreach ($result as $row) {
    $id_tasks[] = $row['id_task'];
}

$list_id_tasks = new \ssp\module\SessionVar('list_tasks');
$list_id_tasks->setValue($id_tasks);

\ssp\module\Tools::send_json($result);

