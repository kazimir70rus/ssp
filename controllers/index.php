<?php

$task = new \ssp\models\Task($db);


$task->checkExpired($id_user->getValue());

$list_tasks_executor = $task->getTasksForControl($id_user->getValue(), true);
$list_tasks_for_control = $task->getTasksForControl($id_user->getValue());

require_once 'views/index.php';

