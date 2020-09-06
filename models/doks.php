<?php

namespace ssp\models;

Class Doks
{
    private $db;


    function __construct($db)
    {
        $this->db = $db;
    }


    // формируем список файлов прикрипленных к задаче
    function getList($id_task)
    {
        $query = 'select id_dok, id_author, filename from uploaddoks where id_task = :id_task order by filename';

        return $this->db->getList($query, ['id_task' => $id_task]);
    }


    // добавление файла к задаче
    function addDok($id_task, $id_author, $filename)
    {
        $query = '  insert into uploaddoks (id_task, id_author, filename)
                    values (:id_task, :id_author, :filename)';

        return $this->db->insertData($query, [
                                                'id_task'   => $id_task,
                                                'id_author' => $id_author,
                                                'filename'  => $filename,
                                             ]);
    }
}

