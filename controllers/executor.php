<?php

$task = new \ssp\models\Task($db);
$list_tasks = $task->getListTip($id_user->getValue(), 1, 100);

$zagolovok = 'Исполнитель';

require_once 'views/tasks.php';
