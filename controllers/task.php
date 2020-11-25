<?php

$task = new \ssp\models\Task($db);

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
        $event['dt'] = \ssp\module\Datemod::dateNoWeekends($_POST['dt']);
    }

    if ($task->updateCondition($event) > 0) {

        // id_action = 39 создать подзадачу
        if ($event['id_action'] === 39) {
            header('Location: ' . BASE_URL . 'add_task/' . $event['id_task']);
            exit;
        }

        // id_action = 17 это редактирование
        if ($event['id_action'] === 17) {
            header('Location: ' . BASE_URL . 'edit_task/' . $event['id_task']);
            exit;
        }

        // после выполнения действия перенеправление на главную страницу
        header('Location: ' . BASE_URL);
        exit;
    }
}

if (isset($_POST['upload'])) {
    (new \ssp\models\Doks($db))->addDoks([(int)$_POST['id_task']], $id_user->getValue());
}

$id_task = (int)$param[1];

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

$type_periodic = [
    1 => 'разовая',
    2 => 'ежедневно',
    3 => 'еженедельно',
    4 => 'ежемесячно',
    7 => 'ежеквартально',
    5 => 'ежегодно',
    6 => 'через',
];

$day_of_week = [
    1 => 'по понедельникам',
    2 => 'по вторникам',
    3 => 'по средам',
    4 => 'по четвергам',
    5 => 'по пятницам',
];

switch ((int)$task_info['repetition']) {
    case 2:
        $describ = '(ежедневно)';
        break;
    case 3:
        $data_end = \DateTime::createFromFormat('d-m-Y', $task_info['data_end']);
        $describ = '(еженедельно ' . $day_of_week[(int)$data_end->format('N')] . ')';
        break;
    case 4:
        $data_end = \DateTime::createFromFormat('d-m-Y', $task_info['data_end']);
        $describ = '(ежемесячно ' . $data_end->format('d') . ' числа)';
        break;
    case 5:
        $data_end = \DateTime::createFromFormat('d-m-Y', $task_info['data_end']);
        $describ = '(ежегодно ' . $data_end->format('d-m') . ' числа)';
        break;
    case 6:
        $describ = '(через ' . $task_info['custom_interval'] . ' дн.)';
        break;
    case 7:
        $data_end = \DateTime::createFromFormat('d-m-Y', $task_info['data_end']);
        $describ = '(ежеквартально ' . $data_end->format('d') . ' числа)';
        break;
    default:
        $describ = '';
        break;
}

if (
    $task->checkTip($id_task, $id_user->getValue(), 4) &&
    ($task->getRepetition($id_task) > 1) &&
    ($task->getRemainPeriod($id_task) === 1)
   ) {
    $msg = 'Наступил последний период этой периодической задачи!<br>
            Для продления выберите действие "продлить задачу"';
} else {
    $msg = '';
}

require_once 'views/task.php';

