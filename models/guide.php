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
    function getTypeResults($seek_str = '')
    {
        $seek_str = htmlspecialchars(trim($seek_str));

        while (strpos($seek_str, '  ') !== false) {
            $seek_str = str_replace('  ', ' ', $seek_str);
        }

        $keywords = explode(' ', $seek_str);
        $seek_str = implode('%', $keywords);
        $seek_str = '%' . $seek_str . '%';

        $query = 'select id_result, name from type_result where name like :seek_str and visible = 1 order by name limit 5';

        return $this->db->getList($query, ['seek_str' => $seek_str]);
    }


    // возвращает id вида результата, и при неоходимости создает запись
    function getIdTypeResult($name)
    {
        $query = 'select id_result from type_result where name = :name';

        $result = $this->db->getRow($query, ['name' => $name]);

        if (is_array($result)) {

            return $result['id_result'];
        } else {

            return $this->addResult($name);
        }
    }


    // возвращает наименоване типа результата
    function getNameTypeResult($id_result)
    {
        $query = 'select name from type_result where id_result = :id_result';

        $result = $this->db->getRow($query, ['id_result' => $id_result]);

        if (is_array($result)) {

            return $result['name'];
        } else {

            return '';
        }
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

