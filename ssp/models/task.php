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

}
