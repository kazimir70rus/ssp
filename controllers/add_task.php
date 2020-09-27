<?php

$user = new \ssp\models\User($db);

if (isset($_POST['submit'])) {

    // todo
    // проверка входных данных

    $task_info = [];
    $task_info['name']       = htmlspecialchars($_POST['task']);
    $task_info['executor']   = (int)$_POST['executor'];
    $task_info['iniciator']  = (int)$_POST['iniciator'];
    $task_info['client']     = (int)$_POST['client'];
    $task_info['controller'] = (int)$_POST['controller'];
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

