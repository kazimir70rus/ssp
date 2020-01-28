<?php

if (isset($_POST['submit'])) {
    $login = htmlspecialchars($_POST['login']);
    $pass = htmlspecialchars($_POST['pass']);

    $id_user->setValue(1);

    header('Location: ' . BASE_URL);
    exit();
}

require_once 'views/login.php';

