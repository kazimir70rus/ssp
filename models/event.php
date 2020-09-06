<?php

namespace ssp\models;

Class Event
{
    private $db;

    function __construct($db)
    {
        $this->db = $db;
    }


    function listActionsWDate()
    {
        $query = 'select id_action from actions where need_dt = 1 order by id_action';

        return $this->db->getList($query);
    }


    function checkActionNeedDate($id_action)
    {
        $query = 'select id_action from actions where need_dt = 1 and id_action = :id_action';

        return $this->db->getList($query, ['id_action' => $id_action]);
    }


    function add($event)
    {
        // возможно в массиве лишня переменная, которая выдаст ошибку при вставке
        if (isset($event['penalty'])) {
            unset($event['penalty']);
        }

        if (isset($event['dt'])) {
            $query = 'insert into events (id_task, id_user, id_action, comment, dt_wish) values (:id_task, :id_user, :id_action, :comment, :dt)';
        } else {
            $query = 'insert into events (id_task, id_user, id_action, comment) values (:id_task, :id_user, :id_action, :comment)';
        }

        return $this->db->insertData($query, $event);
    }
}

