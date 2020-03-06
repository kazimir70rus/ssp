<?php

$task = new \ssp\models\Task($db);
$list_tasks = $task->getListTip($id_user->getValue(), 4, 100);

$zagolovok = 'Контролер';

require_once 'views/tasks.php';
