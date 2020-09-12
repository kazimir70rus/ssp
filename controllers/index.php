<?php

$task = new \ssp\models\Task($db);

// формируем список задач для отображения на главном экране
//$list_tasks_executor = $task->getTasksForControl($id_user->getValue(), true);
//$list_tasks_for_control = $task->getTasksForControl($id_user->getValue());

require_once 'views/index.php';

