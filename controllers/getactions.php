<?php

$id_task = (int)$param[1];

$task = new \ssp\models\Task($db);

\ssp\module\Tools::send_json($task->getAction($id_task, $id_user->getValue()));

