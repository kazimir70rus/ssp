<?php

$id_task = (int)$param[1];

$user = new \ssp\models\User($db);
$task = new \ssp\models\Task($db);
$guide = new \ssp\models\Guide($db);

if (isset($_POST['save'])) {

    $task_info = [
        'id_task'       => (int)$_POST['id_task'],
        'id_author'     => $id_user->getValue(),
        'id_result'     => $guide->getIdTypeResult($_POST['type_result']),
        'id_report'     => (int)$_POST['id_report'],
        'name'          => htmlspecialchars($_POST['task']),
        'penalty'       => (int)$_POST['penalty'],
        'id_executor'   => (int)$_POST['id_executor'],
        'id_client'     => (int)$_POST['id_client'],
        'id_controller' => (int)$_POST['id_controller'],
        'repetition'    => (int)$_POST['repetition'],
    ];

    // проверка на существование задачи с заданным id, и права доступа текущего пользователя на нее
    if (!$task->checkAccess($task_info['id_task'], $id_user->getValue())) {
 
        header('Location: ' . BASE_URL);
        exit;
    }

    // узнаем периодичность этой задачи до редактирования
    $prev_task_repetition = $task->getRepetition($task_info['id_task']);

    // узнаем историю, в зависимости от этого, возможно можно будет сделать вывод о 
    // блокировки смены периодичности
    if ($task->disableChangePeriod($task_info['id_task'])) {
        
        $task_info['repetition'] = $prev_task_repetition;
    }

    if (($prev_task_repetition == 1) && ($task_info['repetition'] == 1)) {

        // задача была разовая, такой осталась
        $task_info['data_begin'] = $_POST['data_beg'];
        $task_info['data_end']   = \ssp\module\Datemod::dateNoWeekends($_POST['data_end']);

        $task->saveAfterEdit($task_info);

        header('Location: ' . BASE_URL);
        exit;
    }

    // т.к. дальше будем создавать задачи нам необходимо узнать инициатора редактируемой задачи
    $task_info['id_iniciator'] = $user->getIdIniciator($task_info['id_task']);

    if ($prev_task_repetition != 1) {
        // раньше была периодической, теперь или разовая или периодическая

        // удаляем с не наступившими сроками 
        $task->delPeriodicTask($task->getIdPeriodic($task_info['id_task']));

        if ($task_info['repetition'] == 1) {
            // создаем разовую задачу
            $task_info['data_begin'] = $_POST['data_beg'];
            $task_info['data_end']   = \ssp\module\Datemod::dateNoWeekends($_POST['data_end']);

            $task->add($task_info);

            header('Location: ' . BASE_URL);
            exit;
        }
    } else {
        // задача была разовой, а стала периодической

        // если срок еще не подошел, то удаляем задачу
        $task->delOneTimeTask($task_info['id_task']);
    }

    // создаем периодическую
    $task_info['date_from']     = $_POST['data_end'];
    $task_info['date_to']       = $_POST['date_to'];
    $task_info['custom_period'] = ((int)$_POST['period'] < 1) ? 28 : (int)$_POST['period'];

    $task->createPeriodicTasks($task_info);

    header('Location: ' . BASE_URL);
    exit;
}

// формируем список потребителей, исполнителей и контроллеров исходя из инициатора задачи
$id_iniciator = $user->getIdIniciator($id_task);
$list_users = $user->getListSubordinate($id_iniciator);
$list_controllers = $user->getListControllers($id_iniciator);

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

