<?php

$task = new \ssp\models\Task($db);

$list_tasks_executor = $task->getListTip($id_user->getValue(), 1);

// todo
// попробуем найти просточенные

$list_tasks_for_control = $task->getTaskForControl($id_user->getValue());

require_once 'views/index.php';
