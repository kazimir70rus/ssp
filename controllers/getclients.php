<?php

$user = new \ssp\models\User($db);

\ssp\module\Tools::send_json($user->getClients());

