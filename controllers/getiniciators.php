<?php

$user = new \ssp\models\User($db);

\ssp\module\Tools::send_json($user->getIniciators($id_user->getValue()));

