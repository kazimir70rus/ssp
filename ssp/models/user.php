<?php

namespace ssp\models;

Class User
{
    private $db;

    private $id_user;

    function __construct($db, $id_user = 0)
    {
        $this->db = $db;
        $this->id_user = $id_user;
    }

    function getInfo()
    {
        $query = 'select name from users where id_user = :id_user';

        return $this
                    ->db
                    ->getRow($query, ['id_user' => $this->id_user]);
    }

    function check($login, $pass)
    {
        $query ='select id_user from users where name = :login and pass = password(:pass)';

        return $this
                    ->db
                    ->getRow($query, ['login' => $login, 'pass' => $pass]);
    }
}
