<?php

$data = json_decode(file_get_contents('php://input'), true);

$dok = new \ssp\models\Doks($db);

// по id_dok узнаем id_task
$id_task = $dok->getIdTask((int)$data['id_dok']);

// узнаем роли пользователя в этой задаче 
$role = (new \ssp\models\Task($db))->getTip($id_task, $id_user->getValue());

// пользователь должен быть контроллером этой задачи
if (in_array(4, $role, true)) {
    // меняем статус
    $dok->changePrintStatus((int)$data['id_dok'], (int)$data['printed']);
}
