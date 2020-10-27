<?php

$id_dok = (int)$param[1];

// удалим указанный документ
(new \ssp\models\Doks($db))->removeDok($id_dok, $id_user->getValue());

\ssp\module\Tools::send_json([]);

