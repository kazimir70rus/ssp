<?php



$user = new \ssp\models\User($db);
$guide = new \ssp\models\Guide($db);
$task = new \ssp\models\Task($db);

if (isset($_POST['submit'])) {

    // todo
    // проверка входных данных

    $task_info = [
        'id_author'     => $id_user->getValue(),
        'id_result'     => $guide->getIdTypeResult($_POST['type_result']),
        'id_report'     => (int)$_POST['id_report'],
        'name'          => htmlspecialchars($_POST['task']),
        'penalty'       => (int)$_POST['penalty'],
        'id_executor'   => (int)$_POST['executor'],
        'id_iniciator'  => (int)$_POST['iniciator'],
        'id_client'     => (int)$_POST['client'],
        'id_controller' => (int)$_POST['controller'],
        'repetition'    => (int)$_POST['repetition'],
    ];

    $executors_for_task = $_POST['executors_for_task'] ?? [];

    if ($task_info['repetition'] != 1) {
        $task_info['date_from'] = $_POST['data_end'];
        $task_info['date_to']   = $_POST['date_to'];
        $task_info['period']    = $_POST['period'] ?? 30;

        $task->createPeriodicTasks($task_info);

        header('Location: ' . BASE_URL);
        exit;
    }

    $task_info['data_begin'] = $_POST['data_beg'];
    $task_info['data_end']   = \ssp\module\Datemod::dateNoWeekends($_POST['data_end']);

    if (count($executors_for_task) > 0) {
        foreach ($executors_for_task as $id_executor) {
            $task_info['id_executor'] = $id_executor;
            $task->add($task_info);
        }

        header('Location: ' . BASE_URL);
        exit;
    } else {
        $id_task = $task->add($task_info);
    }

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

