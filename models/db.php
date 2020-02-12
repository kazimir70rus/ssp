<?php

namespace ssp\models;

Class Db
{
    public $errInfo = [];

    private $db;

    function __construct($config)
    {
        $dsn = "mysql:host={$config['srv']};dbname={$config['db']};charset=utf8";
        $this->db = new \PDO($dsn, $config['user'], $config['pass']);

        return $this;
    }

    function getList($query, $params = [])
    {
        $stmt = $this
                    ->db
                    ->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    function getRow($query, $params = [])
    {
        $stmt = $this
                    ->db
                    ->prepare($query);
        $stmt->execute($params);

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    function insertData($query, $params = [])
    {
        $stmt = $this
                    ->db
                    ->prepare($query);

        if ($stmt->execute($params)) {

            return $this
                        ->db
                        ->lastInsertId();
        } else {
            $this->errInfo = $stmt->errorInfo();

            return -1;
        }
    }

    function updateData($query, $params = [])
    {
        $stmt = $this
                    ->db
                    ->prepare($query);

        if ($stmt->execute($params)) {

            return $stmt->rowCount();
        } else {
            $this->errInfo = $stmt->errorInfo();

            return -1;
        }
    }
}
