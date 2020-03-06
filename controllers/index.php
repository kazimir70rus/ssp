<?php

$task = new \ssp\models\Task($db);

$list_tasks_executor = $task->getListTip($id_user->getValue(), 1);
$list_tasks_iniciator = $task->getListTip($id_user->getValue(), 3);
$list_tasks_client = $task->getListTip($id_user->getValue(), 2);
$list_tasks_controller = $task->getListTip($id_user->getValue(), 4);

require_once 'views/index.php';
