<?php

$id_task = (int)$param[1];
$task = new \ssp\models\Task($db);
$task_info = $task->getInfo($id_task);

require_once 'views/task.php';
