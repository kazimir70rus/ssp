<?php

$msg = '';

$user = new \ssp\models\User($db);

if (isset($_POST['submit'])) {
    $login = mb_strtoupper(htmlspecialchars($_POST['login']));
    $pass = htmlspecialchars($_POST['pass']);
    $id_organisation = (int)$_POST['id_organisation'];

    $result = $user->check($id_organisation, $login, $pass);

    if (is_array($result)) {

        $id_user->setValue($result['id_user']);
        $name_user->setValue($login);
        $position_user->setValue($result['position']);

        header('Location: ' . BASE_URL);
        exit();
    }
    $msg = 'пользователь не найден';
}

$organisations = $user->getListOrganisations();

require_once 'views/login.php';
