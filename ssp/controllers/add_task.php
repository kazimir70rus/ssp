<?php

$user = new \ssp\models\User($db);

if (isset($_POST['submit'])) {

    $name = htmlspecialchars($_POST['task']);
    $executor = (int)$_POST['executor'];
    $task = new \ssp\models\Task($db);
    $data_beg = $_POST['data_beg'];
    $data_end = $_POST['data_end'];
    
        if(!empty($name) and !empty($data_beg) and !empty($data_end)) {
            $result = $task->add($executor, $name, $id_user->getValue(), $data_beg, $data_end);
            header('Location: ' . BASE_URL);
        } else {
            echo '<p style="color: red"> нет информации для сохранения </p>';
        }
}

$list_users = $user->getList();

require_once 'views/add_task.php';

