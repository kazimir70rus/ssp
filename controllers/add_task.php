<?php

$user = new \ssp\models\User($db);

if (isset($_POST['submit'])) {

    $task_info = [];
    $task_info['name']       = htmlspecialchars($_POST['task']);
    $task_info['executor']   = (int)$_POST['executor'];
    $task_info['iniciator']  = (int)$_POST['iniciator'];
    $task_info['client']     = (int)$_POST['client'];
    $task_info['controller'] = (int)$_POST['controller'];
    $task_info['data_beg']   = $_POST['data_beg'];
    $task_info['data_end']   = $_POST['data_end'];
    $task_info['penalty']    = (int)$_POST['penalty'];
    $task_info['author']     = $id_user->getValue();

    $task = new \ssp\models\Task($db);

    if ($task->add($task_info) > 0) {
        header('Location: ' . BASE_URL);
        exit;
    }
}

$list_users = $user->getListSubordinate($id_user->getValue());
$list_controllers = $user->getListControllers($id_user->getValue());

if (count($list_controllers) == 0) {
    $list_controllers = $list_users;
}

$cur_date = new DateTime();
$fin_date = new DateTime();
$fin_date = $fin_date->add(\DateInterval::createFromDateString('5 days'));

require_once 'views/add_task.php';

