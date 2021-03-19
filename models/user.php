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

    function check($id_organisation, $login, $pass)
    {
        $query ='select
                    id_user, positions.name as position 
                from
                    users join positions using (id_position) 
                where 
                    users.name = :login and pass = password(:pass) and id_organisation = :id_organisation';

        return $this
                    ->db
                    ->getRow($query, ['login' => $login, 'pass' => $pass, 'id_organisation' => $id_organisation]);
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
        $query ='select id_user, name from users where is_controller = 1 order by name';

        return $this
                    ->db
                    ->getList($query, ['id_parent' => $id_parent]);
    }


    // возвращает список организаций
    function getListOrganisations()
    {
        $query = 'select id_organisation, name from organisations order by name';

        return $this
                    ->db
                    ->getList($query);

    }


    // формирование списка возможных инициаторов, если пользователь контроллер, то возвращаем список всех возможных инициаторов
    // если не контроллер, то сам пользователь является инициатором
    function getIniciators($id_user)
    {
        if ($this->isController($id_user)) {
            $query = 'select id_user, name from users where id_user in (select distinct id_parent from users) order by name';

            return $this->db->getList($query);
        } else {
            $query = 'select id_user, name from users where id_user = :id_user';

            return $this->db->getList($query, ['id_user' => $id_user]);
        }
    }


    // возвращает 1 если пользователь является контроллером, и 0 если не является
    function isController($id_user)
    {
        $query = 'select is_controller from users where id_user = :id_user';

        return (int)$this->db->getValue($query, ['id_user' => $id_user]);
    }


    // возвращает id инициатора у заданной задачи
    function getIdIniciator($id_task)
    {
        $query = 'select id_user from task_users where id_tip = 3 and id_task = :id_task';

        return (int)$this->db->getValue($query, ['id_task' => $id_task]);
    }
}
