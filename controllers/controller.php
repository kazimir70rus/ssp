<?php

$task = new \ssp\models\Task($db);
$list_tasks = $task->getListTip($id_user->getValue(), 'id_controller', 100);

$zagolovok = 'Контролер';

require_once 'views/tasks.php';
