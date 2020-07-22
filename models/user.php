<?php

namespace ssp\models;

Class User
{
    private $db;

    private $id_user;

    public $name;

    function __construct($db, $id_user = 0, $position = '')
    {
        $this->db = $db;

        if ($id_user) {
            $this->id_user = $id_user;
            $result = $this->getInfo($id_user);
            $this->$name = $result['name'];
        }
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

    function getListSubordinate($id_parent)
    {
        $query ='select id_user, name from users where id_parent = :id_parent order by name';

        return $this
                    ->db
                    ->getList($query, ['id_parent' => $id_parent]);
    }

    function getListControllers($id_parent)
    {
        $query ='select id_user, name from users where id_parent = :id_parent and is_controller = 1 order by name';

        return $this
                    ->db
                    ->getList($query, ['id_parent' => $id_parent]);
    }
    

}
