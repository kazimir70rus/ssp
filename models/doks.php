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


    // добавление документа к задаче
    function addDoks($id_task, $id_user)
    {
        if (count($_FILES['userfile']) > 0) {

            $uploaddir = 'attachdoks/' . $id_task;

            if (!file_exists($uploaddir)) {
                mkdir($uploaddir);
            }

            foreach ($_FILES['userfile']['error'] as $key => $error) {
                if ($error == UPLOAD_ERR_OK) {
                    $tmp_name = $_FILES['userfile']['tmp_name'][$key];
                    // basename() может спасти от атак на файловую систему;
                    // может понадобиться дополнительная проверка/очистка имени файла
                    $name = basename($_FILES['userfile']['name'][$key]);
                    if (move_uploaded_file($tmp_name, "${uploaddir}/${name}")) {
                        $this->addDok($id_task, $id_user, $name);
                    }
                }
            }
        }
    }
}

