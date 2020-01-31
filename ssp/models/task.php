<?php

namespace ssp\models;

Class Task
{
    private $db;

    function __construct($db)
    {
        $this->db = $db;
    }

    function add($executor, $task, $id_author)
    {
        $query = 'insert into tasks (id_executor, name, id_author) values(:id_executor, :name, :author)';

        return $this
                    ->db
                    ->insertData($query, ['id_executor' => $executor, 'name' => $task, 'author' => $id_author]);
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
//        $query = 'select id_task, name, id_author from tasks where id_author = :id_author';

        $query = 'select tasks.id_task, tasks.name, users.name as fio_executor, tasks.data_end from tasks, users
                    where tasks.id_executor=users.id_user and tasks.id_author = :id_author
                    order by tasks.data_end';
        
        return $this
                    ->db
                    ->getList($query, ['id_author' => $id_author]);
    }
}
