<?php

namespace ssp\models;

Class User
{
    private $db;

    private $id_user;

    function __construct($db, $id_user)
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
}
