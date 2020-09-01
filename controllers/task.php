<?php

$task = new \ssp\models\Task($db);

if (isset($_POST['submit'])) {
    $event = [
        'id_task'   => (int)$_POST['id_task'],
        'id_action' => (int)$_POST['id_action'],
        'comment'   => htmlspecialchars($_POST['comment']),
        'id_user'   => $id_user->getValue(),
    ];

    $result = $task->updateCondition($event);

    if ($result > 0) {
        // изменение параметров задачи
        // если id_action = 5, то вставляем последнюю дату из истории
        if ($event['id_action'] == 5) {
            $task->changeDateEnd($event['id_task']);
        }

        if ($event['id_action'] == 12) {
            $task->changeDateExec($event['id_task']);
        }

        if ($event['id_action'] == 13) {
            $task->changeDateClient($event['id_task']);
        }

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

            // id_action = 17 это редактирование
            if ($event['id_action'] == 17) {
                header('Location: ' . BASE_URL . 'edit_task/' . $event['id_task']);
                exit;
            }

            // перенеправление на другую страницу
            header('Location: ' . BASE_URL);
            exit;
        }
    }
}

$id_task = (int)$param[1];
$task_info = $task->getInfo($id_task);

// выбор возможных действий зависит от текущего состояния задачи и роли пользователя в ней
$list_actions = $task->getAction($id_task, $id_user->getValue());

// вывод истории действий которые проводились над этой задачей
$history_actions = $task->getHistoryActions($id_task);

require_once 'views/task.php';
