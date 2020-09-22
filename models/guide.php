<?php

namespace ssp\models;

Class Guide
{
    private $db;


    function __construct($db)
    {
        $this->db = $db;
    }


    // возвращает список видов результата
    function getTypeResults()
    {
        $query = 'select id_result, name from type_result order by name';

        return $this->db->getList($query);
    }


    // добавляет вид результата
    function addResult($name)
    {
        $query = 'insert into type_result (name) values (:name)';

        return $this->db->insertData($query, ['name' => $name]);
    }


    // обновляет вид результата
    function updateResult($id_result, $name)
    {
        $query = 'update type_result set name = :name where id_result = :id_result';

        return $this->db->updateData($query, ['name' => $name, 'id_result' => $id_result]);
    }


    // возвращает список видов результата
    function getTypeReports()
    {
        $query = 'select id_report, name from type_report order by name';

        return $this->db->getList($query);
    }


    // добавляет вид результата
    function addReport($name)
    {
        $query = 'insert into type_report (name) values (:name)';

        return $this->db->insertData($query, ['name' => $name]);
    }


    // обновляет вид результата
    function updateReport($id_report, $name)
    {
        $query = 'update type_report set name = :name where id_report = :id_report';

        return $this->db->updateData($query, ['name' => $name, 'id_report' => $id_report]);
    }
}

