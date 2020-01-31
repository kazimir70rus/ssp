<?php

$user = new \ssp\models\User($db);

if (isset($_POST['submit'])) {

    $name = htmlspecialchars($_POST['task']);
    $executor = (int)$_POST['executor'];
    $task = new \ssp\models\Task($db);

        if(!empty($name)) {
            $result = $task->add($executor, $name, $id_user->getValue());
            
            header('Location: ' . BASE_URL);
        } else {
            echo 'нет информации для сохранения';
        }
}

$list_users = $user->getList();

require_once 'views/add_task.php';

