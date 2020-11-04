<?php

$id_task = (int)$param[1];

// проверка если пользователь не имеет доступа к этой задаче, список файлов не выводить
if ((new \ssp\models\Task($db))->checkAccess($id_task, $id_user->getValue())) {

    // запросим роли пользователя в этой задаче и далее
    // если пользователь является контроллером задачи, выводим статус печати
    $role = (new \ssp\models\Task($db))->getTip($id_task, $id_user->getValue());
    
    // получаем список файлов приклипленных к этой задаче
    $upload_doks =
        (new \ssp\models\Doks($db))->getList($id_task, $id_user->getValue(), in_array(4, $role, true));
} else {
    $uploads_doks = [];
}

\ssp\module\Tools::send_json($upload_doks);

