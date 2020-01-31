<?php

namespace ssp\models;

Class User
{
    private $db;

    private $id_user;

    function __construct($db, $id_user = 0, $position = '')
    {
        $this->db = $db;
        $this->id_user = $id_user;
        $this->position = $position;
    }

    function getInfo($id_user)
    {
        $query = 'select name from users where id_user = :id_user';

        return $this
                    ->db
                    ->getRow($query, ['id_user' => $id_user]);
    }

    function check($login, $pass)
    {
        $query ='select id_user, position from users where name = :login and pass = password(:pass)';

        return $this
                    ->db
                    ->getRow($query, ['login' => $login, 'pass' => $pass]);
    }

    function getList()
    {
        $query ='select id_user, name from users order by name';

        return $this
                    ->db
                    ->getList($query);
    }

}
