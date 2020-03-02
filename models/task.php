<?php

namespace ssp\models;

Class Task
{
    private $db;

    function __construct($db)
    {
        $this->db = $db;
    }

    function add($id_users, $task, $id_author, $data_beg, $data_end)
    {
        $query = 'insert into tasks
            (id_executor, id_iniciator, id_client, id_controller, name, id_author, data_begin, data_end)
            values(:id_executor, :id_iniciator, :id_client, :id_controller, :name, :author, :data_begin, :data_end)';

        return $this
                    ->db
                    ->insertData($query, [
                                            'id_executor'   => $id_users['executor'],
                                            'id_iniciator'  => $id_users['iniciator'],
                                            'id_client'     => $id_users['client'],
                                            'id_controller' => $id_users['controller'],
                                            'name'          => $task,
                                            'author'        => $id_author,
                                            'data_begin'    => $data_beg,
                                            'data_end'      => $data_end,
                                         ]);
    }

    function getListTip($id_user, $tip, $limit = 10)
    {
        $query = 'select id_task, name, data_end from tasks where ' . $tip . ' = :id_user order by data_end desc limit ' . $limit;

        return $this
                    ->db
                    ->getList($query, ['id_user' => $id_user]);
    }


    function getList($id_executor)
    {
        $query = 'select id_task, name, id_author from tasks where id_executor = :id_executor';

        return $this
                    ->db
                    ->getList($query, ['id_executor' => $id_executor]);
    }

    function getListAuthorTasks($id_author)
    {
        $query = 'select tasks.id_task, tasks.name, users.name as fio_executor, tasks.data_end from tasks, users
                    where tasks.id_executor=users.id_user and tasks.id_author = :id_author
                    order by tasks.data_end';
        
        return $this
                    ->db
                    ->getList($query, ['id_author' => $id_author]);
    }
}
