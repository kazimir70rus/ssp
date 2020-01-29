<?php

if (isset($_POST['submit'])) {
    $login = htmlspecialchars($_POST['login']);
    $pass = htmlspecialchars($_POST['pass']);

	$db = new \ssp\models\Db($config);
	
	$user = new \ssp\models\User($db);

	$result = $user->check($login, $pass);

	if (is_array($result)) {

		$id_user = new \ssp\module\SessionVar(UID, 'id_user');
    	$id_user->setValue($result['id_user']);

    	$name_user->setValue($login);

	    header('Location: ' . BASE_URL);
    	exit();

	}
	

}

require_once 'views/login.php';

