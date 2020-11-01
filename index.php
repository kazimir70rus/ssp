<?php

require_once 'module/autoload.php';

require_once 'configuration/global.php';

date_default_timezone_set('Asia/Novosibirsk');

session_start();

if (isset($_GET['url'])) {
    $param = explode('/', $_GET['url']);
} else {
    $param = [];
}

if (isset($param[0])) {
    $action = $param[0];
} else {
    $action = 'index';
}

$id_user = new \ssp\module\SessionVar('id_user');
$name_user = new \ssp\module\SessionVar('name_user');
$position_user = new \ssp\module\SessionVar('position_user');

$db = new \ssp\module\Db(new \ssp\configuration\DB);

if (!$id_user->getValue()) {
    $action = 'login';
}

$fullname = "controllers/" . $action . ".php";

if (file_exists($fullname)) {
    require_once $fullname;
} else {
    require_once 'views/404.html';
}
