<?php

$task = new \ssp\models\Task($db);

$list_tasks_executor = $task->getListTip($id_user->getValue(), 'id_executor');
$list_tasks_iniciator = $task->getListTip($id_user->getValue(), 'id_iniciator');
$list_tasks_client = $task->getListTip($id_user->getValue(), 'id_client');
$list_tasks_controller = $task->getListTip($id_user->getValue(), 'id_controller');

require_once 'views/index.php';
