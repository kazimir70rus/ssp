<?php

$task = new \ssp\models\Task($db);
$list_tasks = $task->getListTip($id_user->getValue(), 'id_executor', 100);

$zagolovok = 'Исполнитель';

require_once 'views/tasks.php';
