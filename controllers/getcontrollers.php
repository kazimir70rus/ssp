<?php

// todo
// можно сделать проверку, инициатор либо сам пользователь или его руководитель

$user = new \ssp\models\User($db);

\ssp\module\Tools::send_json($user->getListControllers($id_iniciator));

