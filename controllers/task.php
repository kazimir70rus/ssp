<?php

$task = new \ssp\models\Task($db);

$id_task = (int)$param[1];

if (isset($_POST['submit'])) {

    // проверка имеет ли отношение пользователь к этой задаче
    if (!$task->checkAccess((int)$_POST['id_task'], $id_user->getValue())) {
        // доступа нет
        header('Location: ' . BASE_URL);
        exit;
    }

    $event = [
        'id_task'   => (int)$_POST['id_task'],
        'id_action' => (int)$_POST['id_action'],
        'comment'   => htmlspecialchars($_POST['comment']),
        'id_user'   => $id_user->getValue(),
        'penalty'   => (int)($_POST['penalty'] ?? 0),
    ];

    if (\DateTime::createFromFormat('Y-m-d', $_POST['dt'] ?? '')) {
        $event['dt'] = $_POST['dt'];
    }

    if ($task->updateCondition($event) > 0) {

        // id_action = 17 это редактирование
        if ($event['id_action'] == 17) {
            header('Location: ' . BASE_URL . 'edit_task/' . $event['id_task']);
            exit;
        }

        // если потребитель и инициатор одно лицо, и потребитель подтверждает выполнение
        // и сразу генерируем подтверждении инициатором
        if (
            ((int)$event['id_action'] == 13) &&
            ($task->executorIsClient($event['id_task'], $event['id_user']))
        ) {
            $event['id_action'] = 19;
            $task->updateCondition($event);
        }

        // после выполнения действия перенеправление на главную страницу
        header('Location: ' . BASE_URL);
        exit;
    }
}


if (isset($_POST['upload'])) {
    // обработка загрузки файла
    $uploads = new \ssp\models\Doks($db);

    // todo
    // сделать константу
    $uploaddir = 'attachdoks/' . $id_task;

    if (!file_exists($uploaddir)) {
        mkdir($uploaddir);
    }

    foreach ($_FILES['userfile']['error'] as $key => $error) {
        if ($error == UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['userfile']['tmp_name'][$key];
            // basename() может спасти от атак на файловую систему;
            // может понадобиться дополнительная проверка/очистка имени файла
            $name = basename($_FILES['userfile']['name'][$key]);
            if (move_uploaded_file($tmp_name, "${uploaddir}/${name}")) {
                $uploads->addDok($id_task, $id_user->getValue(), $name);
            }
        }
    }

    // todo
    // для предотвращения сообщения при обновлении страницы, можно сделать перенаправление
}

// проверка имеет ли отношение пользователь к этой задаче
if (!$task->checkAccess($id_task, $id_user->getValue())) {
    // доступа нет
    header('Location: ' . BASE_URL);
    exit;
}

// если для данной задачи пользователь является исполнителем и состояние задачи новая,
// то меняем статус задачи на выполняется
$event = [
    'id_task'   => $id_task,
    'id_action' => 15,
    'comment'   => 'принял к выполнению',
    'id_user'   => $id_user->getValue(),
];
$task->updateCondition($event);

// запрашиваем детальную информацию о задаче
$task_info = $task->getInfo($id_task);

// выбор возможных действий зависит от текущего состояния задачи и роли пользователя в ней
$list_actions = $task->getAction($id_task, $id_user->getValue());

// вывод истории действий которые проводились над этой задачей
$history_actions = $task->getHistoryActions($id_task);

require_once 'views/task.php';

