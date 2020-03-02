<?php

$task = new \ssp\models\Task($db);
$list_tasks = $task->getListTip($id_user->getValue(), 'id_iniciator', 100);

$zagolovok = 'Инициатор';

require_once 'views/tasks.php';
