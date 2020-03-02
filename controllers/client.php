<?php

$task = new \ssp\models\Task($db);
$list_tasks = $task->getListTip($id_user->getValue(), 'id_client', 100);

$zagolovok = 'Потребитель';

require_once 'views/tasks.php';
