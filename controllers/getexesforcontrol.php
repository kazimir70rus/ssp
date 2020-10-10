<?php

$task = new \ssp\models\Task($db);

// получаем список исполнителей у задач контроллируемых пользователем 
\ssp\module\Tools::send_json($task->getExesForControl($id_user->getValue()));

