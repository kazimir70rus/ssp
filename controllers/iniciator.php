<?php

$task = new \ssp\models\Task($db);
$list_tasks = $task->getListTip($id_user->getValue(), 3, 100);

$zagolovok = 'Инициатор';

require_once 'views/tasks.php';
