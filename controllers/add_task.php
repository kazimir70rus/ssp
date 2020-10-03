<?php

function createPeriodicTasks($db, $repetition, $id_user)
{
    // формируем шаблон задачи
    $guide = new \ssp\models\Guide($db);
    $task = new \ssp\models\Task($db);

    $repetition = (int)$_POST['repetition'];

    $task_template = [
                        'author'     => $id_user,
                        'id_result'  => $guide->getIdTypeResult($_POST['type_result']),
                        'id_report'  => (int)$_POST['id_report'],
                        'name'       => htmlspecialchars($_POST['task']),
                        'penalty'    => (int)$_POST['penalty'],
                        'executor'   => (int)$_POST['executor'],
                        'iniciator'  => (int)$_POST['iniciator'],
                        'client'     => (int)$_POST['client'],
                        'controller' => (int)$_POST['controller'],
                        'date_from'  => $_POST['data_end'],
                        'date_to'    => $_POST['date_to'],
                        'repetition' => $repetition,
                    ];

    // записываем в таблицу периодических задач
    $id_periodic = $task->addPeriodic($task_template);

    // граница интервалов
    $dt_st = \DateTime::createFromFormat('Y-m-d', $_POST['data_end']);
    $dt_en = \DateTime::createFromFormat('Y-m-d', $_POST['date_to']);

    // формируем строку для интервала
    switch ($repetition) {
        case 2:
            $interval = 'P1D';
            break;
        case 3:
            $interval = 'P7D';
            break;
        case 4:
            $interval = 'P1M';
            break;
        case 5:
            $interval = 'P1Y';
            break;
        case 6:
            $days = isset($_POST['period']) ? (int)$_POST['period'] : 30;
            $interval = 'P' . $days . 'D';
            break;
    }

    $dt_curr = \DateTime::createFromFormat('Y-m-d', $dt_st->format('Y-m-d'));

    while ($dt_curr <= $dt_en) {

        // если задача ежедневная и выпадает на выходные, то ее не добавляем
        if (!(($repetition == 2) && (($dt_curr->format('N') == '6') || ($dt_curr->format('N') == '7')))) {
            $task_template['data_beg'] = \ssp\module\Datemod::dateNoWeekends($dt_curr->format('Y-m-d'));
            $task_template['data_end'] = \ssp\module\Datemod::dateNoWeekends($dt_curr->format('Y-m-d'));
            // добавляем задачу
            $id_task = $task->add($task_template, $id_periodic);
        }

        $dt_curr->add(new \DateInterval($interval));
    }

    header('Location: ' . BASE_URL);
    exit;
}


$user = new \ssp\models\User($db);

if (isset($_POST['submit'])) {

    // todo
    // проверка входных данных

    $repetition = (int)$_POST['repetition'];

    if ($repetition != 1) {
        createPeriodicTasks($db, $repetition, $id_user->getValue());
    }

    $task_info = [];
    $task_info['name']       = htmlspecialchars($_POST['task']);
    $task_info['executor']   = (int)$_POST['executor'];
    $task_info['iniciator']  = (int)$_POST['iniciator'];
    $task_info['client']     = (int)$_POST['client'];
    $task_info['controller'] = (int)$_POST['controller'];
    $task_info['data_beg']   = $_POST['data_beg'];
    $task_info['data_end']   = \ssp\module\Datemod::dateNoWeekends($_POST['data_end']);
    $task_info['penalty']    = (int)$_POST['penalty'];
    $task_info['author']     = $id_user->getValue();
    
    $guide = new \ssp\models\Guide($db);
    
    $task_info['id_result']  = $guide->getIdTypeResult($_POST['type_result']);
    $task_info['id_report']  = (int)$_POST['id_report'];

    $task = new \ssp\models\Task($db);

    $id_task = $task->add($task_info);

    if ($id_task > 0) {
        
        // обработка загрузки файла
        $uploads = new \ssp\models\Doks($db);

        if (count($_FILES['userfile']) > 0) {
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

        header('Location: ' . BASE_URL);
        exit;
    }
}

$cur_date = new DateTime();
$fin_date = new DateTime();
$fin_date = $fin_date->add(\DateInterval::createFromDateString('5 days'));

require_once 'views/add_task.php';

