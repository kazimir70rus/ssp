<?php

namespace ssp\models;

Class Doks
{
    private $db;


    function __construct($db)
    {
        $this->db = $db;
    }


    // формируем список файлов прикрипленных к задаче,
    // сразу формируем признак возможности удаления задачи
    function getList($id_task, $id_user, $printed = false)
    {
        $query = '
            select
                id_dok,
                uploaddoks.id_author,
                filename,
                if((uploaddoks.id_author = :id_user) and id_condition not in (6, 7, 4), 1, NULL) as enable_rm '
                . (($printed) ? ', printed' : '') . '
            from
                uploaddoks join tasks using (id_task)
            where
                id_task = :id_task
            order by
                filename';

        return $this->db->getList($query, ['id_task' => $id_task, 'id_user' => $id_user]);
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
    function addDoks($id_tasks, $id_user)
    {
        if (count($_FILES['userfile']) == 0) {
            return;
        }

        $uploaddir = 'attachdoks/' . $id_tasks[0];

        if (!file_exists($uploaddir)) {
            mkdir($uploaddir);
        }

        $paths = [];
        foreach ($_FILES['userfile']['error'] as $key => $error) {
            if ($error == UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['userfile']['tmp_name'][$key];
                // basename() может спасти от атак на файловую систему;
                // может понадобиться дополнительная проверка/очистка имени файла
                $name = basename($_FILES['userfile']['name'][$key]);
                $full_path = "${uploaddir}/${name}";

                if (move_uploaded_file($tmp_name, $full_path)) {
                    $this->addDok($id_tasks[0], $id_user, $name);
                    $paths[] = ['full_path' => $full_path, 'name' => $name];
                }
            }
        }

        if (count($id_tasks) == 1) {
            return count($paths);
        }

        for ($i = 1; $i < count($id_tasks); ++$i) {

            $uploaddir = 'attachdoks/' . $id_tasks[$i];

            if (!file_exists($uploaddir)) {
                mkdir($uploaddir);
            }

            foreach($paths as $path) {
                if (copy($path['full_path'], $uploaddir . '/' . $path['name'])) {
                    $this->addDok($id_tasks[$i], $id_user, $path['name']);
                }
            }
        }

        return count($paths);
    }


    // удаляет документ
    function removeDok($id_dok, $id_user)
    {
        // заранее узнаем информацию о файле, задача при этом должна быть в определенном состоянии
        $query = '
            select
                id_task, filename
            from
                uploaddoks join tasks using (id_task)
            where
                id_dok = :id_dok
                and id_author = :id_user
                and id_condition not in (6, 7, 4)
        ';

        $result = $this->db->getRow($query, ['id_dok' => $id_dok, 'id_user' => $id_user]);

        if (!count($result)) {
            return false;
        }

        $query = '
            delete from
                uploaddoks
            where
                id_dok = :id_dok
                and id_author = :id_user
        ';

        if ($this->db->updateData($query, ['id_dok' => $id_dok, 'id_user' => $id_user]) > 0) {
            // из базы файл удален, удаляем его физически
            $fullpath = 'attachdoks/' . $result['id_task'] . '/' . $result['filename'];

            if (file_exists($fullpath)) {
                unlink($fullpath);
            }

            // добавим запись в историю
            $event = [
                'id_task'   => $result['id_task'],
                'id_action' => 24,
                'comment'   => $result['filename'],
                'id_user'   => $id_user,
            ];

            (new \ssp\models\Event($this->db))->add($event);
        }
    }


    // меняем статус печати
    function changePrintStatus($id_dok, $status)
    {
        $query = '
            update uploaddoks
                set printed = :status
            where
                id_dok = :id_dok
        ';

        return $this->db->updateData($query, ['id_dok' => $id_dok, 'status' => $status]);
    }


    // возвращает id_task к которому относится этот документ
    function getIdTask($id_dok)
    {
        $query = 'select id_task from uploaddoks where id_dok = :id_dok';

        $result = $this->db->getRow($query, ['id_dok' => $id_dok]);

        return (int)$result['id_task'];
    }
}


