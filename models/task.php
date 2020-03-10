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
        $this->db->beginTransaction();

        $query = 'insert into tasks
            (name, id_author, data_begin, data_end)
            values(:name, :author, :data_begin, :data_end)';

        $id_task = $this
                        ->db
                        ->insertData($query, [
                                                'name'          => $task,
                                                'author'        => $id_author,
                                                'data_begin'    => $data_beg,
                                                'data_end'      => $data_end,
                                            ]);
        if ($id_task != -1) {
            $error = false;
            $query = 'insert into
                            task_users (id_task, id_user, id_tip)
                       values
                            (:id_task, :id_executor, 1),
                            (:id_task, :id_client, 2),
                            (:id_task, :id_iniciator, 3),
                            (:id_task, :id_controller, 4)';
            $result = $this
                            ->db
                            ->insertData($query, [
                                                    'id_executor'   => $id_users['executor'],
                                                    'id_iniciator'  => $id_users['iniciator'],
                                                    'id_client'     => $id_users['client'],
                                                    'id_controller' => $id_users['controller'],
                                                    'id_task'       => $id_task,
                                                ]);
            if ($result == -1) {
                $error = true;
            }
        } else {
            $error = true;
        }

        if ($error) {
            $this->db->rollBack();
            return false;
        } else {
            $this->db->commit();
            return true;
        }
    }
/*
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
 */   
    function getListTip($id_user, $id_tip, $limit = 10)
    {
        $query = 'select id_task, name, data_end from task_users join tasks using (id_task) where id_user = :id_user and id_tip = :id_tip order by data_end desc limit ' . $limit;

        return $this
                    ->db
                    ->getList($query, ['id_user' => $id_user, 'id_tip' => $id_tip]);
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

    function getInfo($id_task)
    {
        $query = 'select
                        data_end,
                        data_begin,
                        id_task,
                        tasks.name as task_name,
                        (select name from users join task_users using (id_user) where id_task = :id_task and id_tip = 1) as executor,
                        (select name from users join task_users using (id_user) where id_task = :id_task and id_tip = 3) as iniciator,
                        (select name from users join task_users using (id_user) where id_task = :id_task and id_tip = 2) as client,
                        (select name from users join task_users using (id_user) where id_task = :id_task and id_tip = 4) as controller,
                        data_execut,
                        data_client,
                        if(data_end<curdate() and data_execut is Null, "просрочено", "норм") as primet,
                        c.name as state
                    from
                        tasks
                        join `condition` as c using (id_condition)
                    where
                        id_task = :id_task
                    order by
                        data_end desc';

        return $this
                    ->db
                    ->getRow($query, ['id_task' => $id_task]);
    }

    function getAction($id_task, $id_user)
    {
        $query = 'select id_action, name from actions where id_tip in 
            (select id_tip from task_users where id_task = :id_task and id_user = :id_user)';

        return $this
                    ->db
                    ->getList($query, ['id_task' => $id_task, 'id_user' => $id_user]);
    }
}
