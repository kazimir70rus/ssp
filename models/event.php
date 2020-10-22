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
        $query = '
            insert into events 
                (id_task, id_user, id_action, comment, dt_wish, dt_create) 
            values 
                (:id_task, :id_user, :id_action, :comment, :dt_wish, :dt_create)
        ';

        return $this->db->insertData($query, [
                                                'id_task'   => $event['id_task'],
                                                'id_user'   => $event['id_user'],
                                                'id_action' => $event['id_action'],
                                                'comment'   => $event['comment'],
                                                'dt_wish'   => $event['dt'] ?? NULL,
                                                'dt_create' => $event['dt_create'] ?? date('Y-m-d'),
                                             ]);
    }
}

