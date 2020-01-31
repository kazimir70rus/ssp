<?php

$task = new \ssp\models\Task($db);
$list_tasks = $task->getList($id_user->getValue());
$author_tasks = $task->getListAuthorTasks($id_user->getValue());

require_once 'views/index.php';
