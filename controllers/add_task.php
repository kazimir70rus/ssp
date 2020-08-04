<?php

$user = new \ssp\models\User($db);

if (isset($_POST['submit'])) {

    $name = htmlspecialchars($_POST['task']);
    $id_users = [];
    $id_users['executor'] = (int)$_POST['executor'];
    $id_users['iniciator'] = (int)$_POST['iniciator'];
    $id_users['client'] = (int)$_POST['client'];
    $id_users['controller'] = (int)$_POST['controller'];    
    $task = new \ssp\models\Task($db);
    $data_beg = $_POST['data_beg'];
    $data_end = $_POST['data_end'];

        if(!empty($name) and !empty($data_beg) and !empty($data_end)) {
            $result = $task->add($id_users, $name, $id_user->getValue(), $data_beg, $data_end);
            header('Location: ' . BASE_URL);
            exit();
        } else {
            echo '<p style="color: red"> нет информации для сохранения </p>';
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

