<?php

$user = new \ssp\models\User($db);
$task = new \ssp\models\Task($db);

if (isset($_POST['cancel'])) {
    // поменять состояние задачи, только если она в состоянии edit и юзер инициатор или контроллер этой задачи
    $task->changeEditToNew((int)$_POST['id_task'], $id_user->getValue());

    header('Location: ' . BASE_URL);
    exit;
}

if (isset($_POST['save'])) {

    $task_info = [];
    $task_info['id_task']    = (int)$_POST['id_task'];
    $task_info['name']       = htmlspecialchars($_POST['task']);
    $task_info['id_executor']   = (int)$_POST['id_executor'];
    $task_info['id_client']     = (int)$_POST['id_client'];
    $task_info['id_controller'] = (int)$_POST['id_controller'];
    $task_info['data_beg']   = $_POST['data_beg'];
    $task_info['data_end']   = $_POST['data_end'];
    $task_info['penalty']    = (int)$_POST['penalty'];
    $task_info['id_user']    = $id_user->getValue();

    $task->saveAfterEdit($task_info);

    header('Location: ' . BASE_URL);
    exit;
}

$list_users = $user->getListSubordinate($id_user->getValue());
// добавим в этот массив текущего пользователя
array_unshift($list_users, ['id_user' => $id_user->getValue(), 'name' => $name_user->getValue()]);

$list_controllers = $user->getListControllers($id_user->getValue());

if (count($list_controllers) == 0) {
    $list_controllers = $list_users;
}

$id_task = (int)$param[1];

$task_info = $task->getShortDetail($id_task, $id_user->getValue());

if (!is_array($task_info)) {
    // если результат пустой, то переходим на главную
    header('Location: ' . BASE_URL);
    exit;
}

require_once 'views/edit_task.php';

