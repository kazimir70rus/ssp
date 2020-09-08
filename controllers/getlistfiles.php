<?php

$id_task = (int)$param[1];

// обработка загрузки файла
$uploads = new \ssp\models\Doks($db);

// получаем список файлов приклипленных к этой задаче
\ssp\module\Tools::send_json($uploads->getList($id_task));

