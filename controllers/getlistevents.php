<?php

$id_task = (int)$param[1];

// проверка если пользователь не имеет доступа к этой задаче, список файлов не выводить
if ((new \ssp\models\Task($db))->checkAccess($id_task, $id_user->getValue())) {

    \ssp\module\Tools::send_json((new \ssp\models\Event($db))->getHistoryActions($id_task));
} else {

    \ssp\module\Tools::send_json([]);
}

