<?php

require_once 'module/autoload.php';

define('UID', 'ssp');
define('BASE_URL', '/' . UID . '/');

$config =
[
    'srv'  => '192.168.30.223',
    'user' => '046327307_tasker',
    'pass' => '01478569',
    'db'   => 'msfm_tasker',
];

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

$id_user = new \ssp\module\SessionVar(UID, 'id_user');
$name_user = new \ssp\module\SessionVar(UID, 'name_user');
$position_user = new \ssp\module\SessionVar(UID, 'position_user');

$db = new \ssp\models\Db($config);

if (!$id_user->getValue()) {
    $action = 'login';
}

$fullname = "controllers/" . $action . ".php";

if (file_exists($fullname)) {
    require_once $fullname;
} else {
    require_once 'views/404.html';
}
