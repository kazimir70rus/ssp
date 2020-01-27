<?php

require_once 'module/autoload.php';

define('UUID', 'ssp');
define('BASE_URL', '/' . UUID . '/');

$config =
[
    'srv'  => 'localhost',
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

$fullname = "controllers/" . $action . ".php";

if (file_exists($fullname)) {
    require_once $fullname;
} else {
    require_once 'views/404.html';
}

