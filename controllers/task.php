<?php

$id_task = (int)$param[1];
$task = new \ssp\models\Task($db);
$task_info = $task->getInfo($id_task);

$list_actions = $task->getAction($id_task, $id_user->getValue());

require_once 'views/task.php';
