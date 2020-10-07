<?php

$id_task = (int)$param[1];

$user = new \ssp\models\User($db);
$task = new \ssp\models\Task($db);
$guide = new \ssp\models\Guide($db);

if (isset($_POST['save'])) {

    // необходимо проверить на изменение следующих параметров: тип задачи, периодичность у периодическиoх

    // узнать тип и периодичность текущей задачи

    $task_info = [
        'id_task'       => (int)$_POST['id_task'],
        'name'          => htmlspecialchars($_POST['task']),
        'id_executor'   => (int)$_POST['id_executor'],
        'id_client'     => (int)$_POST['id_client'],
        'id_controller' => (int)$_POST['id_controller'],
        'data_begin'    => $_POST['data_beg'],
        'data_end'      => \ssp\module\Datemod::dateNoWeekends($_POST['data_end']),
        'penalty'       => (int)$_POST['penalty'],
        'id_user'       => $id_user->getValue(),
        'id_result'     => $guide->getIdTypeResult($_POST['type_result']),
        'id_report'     => (int)$_POST['id_report'],
    ];

    $task->saveAfterEdit($task_info);

    header('Location: ' . BASE_URL);
    exit;
}

// формируем список потребителей, исполнителей и контроллеров исходя из инициатора задачи
$id_iniciator = $user->getIdIniciator($id_task);
$list_users = $user->getListSubordinate($id_iniciator);
$list_controllers = $user->getListControllers($id_iniciator);

// добавим в этот массив текущего пользователя
array_unshift($list_users, ['id_user' => $id_user->getValue(), 'name' => $name_user->getValue()]);


if (count($list_controllers) == 0) {
    $list_controllers = $list_users;
}

$task_info = $task->getShortDetail($id_task, $id_user->getValue());

if (!is_array($task_info)) {
    // если результат пустой, то переходим на главную
    header('Location: ' . BASE_URL);
    exit;
}

$type_result = $guide->getNameTypeResult($task_info['id_result']);
$list_report = $guide->getTypeReports();

require_once 'views/edit_task.php';

