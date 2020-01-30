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

}
