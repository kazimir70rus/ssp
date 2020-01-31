<?php

$user = new \ssp\models\User($db);

if (isset($_POST['submit'])) {
    
    $name = htmlspecialchars($_POST['task']);
    $executor = (int)$_POST['executor'];
    $task = new \ssp\models\Task($db);
    
    $result = $task->add($executor, $name, $id_user->getValue());

}

$list_users = $user->getList();

require_once 'views/add_task.php';
