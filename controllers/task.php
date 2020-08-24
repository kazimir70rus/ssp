<?php

$task = new \ssp\models\Task($db);

if (isset($_POST['submit'])) {
    $event = [
        'id_task'   => (int)$_POST['id_task'],
        'id_action' => (int)$_POST['id_action'],
        'comment'   => htmlspecialchars($_POST['comment']),
        'id_user'   => $id_user->getValue(),
    ];

    // to-do:
    // дата нужна не всегда, но если нужна должна быть корректной
    // запрашиваем список действия для которых важна дата
    $events = new \ssp\models\Event($db);

    if (count($events->checkActionNeedDate($event['id_action']))) {
        // дата нужна
        $dt = DateTime::createFromFormat('Y-m-d', $_POST['dt']);

        if ($dt) {
            $event['dt'] = $dt->format('Y-m-d');
            $result = $events->add($event);
        }
    } else {
        $result = $events->add($event);
    }

    if ($result > 0) {
        $task->updateCondition($event['id_task'], $event['id_action']);

        // todo
        // перенеправление на другую страницу
    }
}

$id_task = (int)$param[1];
$task_info = $task->getInfo($id_task);

// сделать:
// выбор возможных действий зависит от текущего состояния задачи и роли пользователя в ней
$list_actions = $task->getAction($id_task, $id_user->getValue());


require_once 'views/task.php';
