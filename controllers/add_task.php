<?php

$user = new \ssp\models\User($db);
$guide = new \ssp\models\Guide($db);
$task = new \ssp\models\Task($db);

if (isset($_POST['submit'])) {

    // todo
    // проверка входных данных

    $task_info = [
        'id_author'      => $id_user->getValue(),
        'id_result'      => $guide->getIdTypeResult($_POST['type_result']),
        'id_report'      => (int)$_POST['id_report'],
        'name'           => htmlspecialchars($_POST['task']),
        'penalty'        => (int)$_POST['penalty'],
        'id_executor'    => (int)$_POST['executor'],
        'id_iniciator'   => (int)$_POST['iniciator'],
        'id_client'      => (int)$_POST['client'],
        'id_controller'  => (int)$_POST['controller'],
        'repetition'     => (int)$_POST['repetition'],
        'id_master_task' => (int)$_POST['id_master_task'],
    ];

    // проверка, если это подзадача, то она не может быть периодической,
    // если иначе, отправляем на главную. возможно и нескольких исполнителей нужно будет отсечь
    if ($task_info['id_master_task'] && $task_info['repetition'] > 1) {

        header('Location: ' . BASE_URL);
        exit;
    }

    // необходимо проверить имеет ли отношение текущий пользователь к основной задаче
    if ($task_info['id_master_task']) {
        if (!$task->checkAccess($task_info['id_master_task'], $id_user->getValue())) {

            header('Location: ' . BASE_URL);
            exit;
        }
    }

    // исполнители для задачи
    $executors_for_task = $_POST['executors_for_task'] ?? [(int)$_POST['executor']];

    // добавление парметров в зависимости от типа задачи
    if ($task_info['repetition'] != 1) {
        $task_info['date_from']     = $_POST['data_end'];
        $task_info['date_to']       = $_POST['date_to'];
        $task_info['custom_period'] = ((int)$_POST['period'] < 1) ? 28 : (int)$_POST['period'];
    } else {
        $task_info['data_begin'] = $_POST['data_beg'];
        $task_info['data_end']   = \ssp\module\Datemod::dateNoWeekends($_POST['data_end']);
    }

    $generated_tasks = [];
    foreach ($executors_for_task as $id_executor) {
        $task_info['id_executor'] = $id_executor;

        if ($task_info['repetition'] != 1) {
            $new_generated_tasks = $task->createPeriodicTasks($task_info);
            $generated_tasks = array_merge($generated_tasks, $new_generated_tasks);
        } else {
            $new_task = $task->add($task_info);

            if ($new_task) {
                // ели задача создана добавим ее id в массив
                $generated_tasks[] = $new_task;
            }
        }
    }

    if (count($generated_tasks) > 0) {
        $uploads = new \ssp\models\Doks($db);
        $uploads->addDoks($generated_tasks, $id_user->getValue());

        header('Location: ' . BASE_URL);
        exit;
    }
}

$id_master_task = (int)($param[1] ?? 0);

$cur_date = new DateTime();
$fin_date = new DateTime();
$fin_date = $fin_date->add(\DateInterval::createFromDateString('5 days'));

require_once 'views/add_task.php';

