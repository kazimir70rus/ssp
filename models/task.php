<?php

namespace ssp\models;

define('NEW_TASK', 9);
define('TASK_CANCEL' , 7);
define('TASK_END', 6);

Class Task
{
    private $db;


    function __construct($db)
    {
        $this->db = $db;
    }


    function add($task_info, $id_periodic = 0)
    {
        $this->db->beginTransaction();

        $query = '  insert into tasks
                        (name, data_begin, data_end, penalty, id_result, id_report, id_periodic)
                    values
                        (:name, :data_begin, :data_end, :penalty, :id_result, :id_report, :id_periodic)';

        $id_task = $this
                        ->db
                        ->insertData($query, [
                                                'name'        => $task_info['name'],
                                                'data_begin'  => $task_info['data_beg'],
                                                'data_end'    => $task_info['data_end'],
                                                'penalty'     => $task_info['penalty'],
                                                'id_result'   => $task_info['id_result'],
                                                'id_report'   => $task_info['id_report'],
                                                'id_periodic' => $id_periodic,
                                            ]);

        if ($id_task > 0) {
            $error = false;
            $query = 'insert into
                            task_users (id_task, id_user, id_tip)
                       values
                            (:id_task, :id_executor, 1),
                            (:id_task, :id_client, 2),
                            (:id_task, :id_iniciator, 3),
                            (:id_task, :id_controller, 4),
                            (:id_task, :id_author, 5)
                            ';
            $result = $this
                            ->db
                            ->insertData($query, [
                                                    'id_executor'   => $task_info['executor'],
                                                    'id_iniciator'  => $task_info['iniciator'],
                                                    'id_client'     => $task_info['client'],
                                                    'id_controller' => $task_info['controller'],
                                                    'id_author'     => $task_info['author'],
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
            return $id_task;
        }
    }


    // добавляем в реест приодических задач
    function addPeriodic($task_info)
    {
        $query = '  insert into periodic
                        (id_author, id_iniciator, id_controller, id_executor, id_client, 
                         name, date_from, date_to, penalty, id_result, id_report)
                    values
                        (:id_author, :id_iniciator, :id_controller, :id_executor, :id_client, 
                         :name, :date_from, :date_to, :penalty, :id_result, :id_report)';

        $result = $this
                    ->db
                    ->insertData($query, [
                                            'id_author'     => $task_info['author'],
                                            'id_iniciator'  => $task_info['iniciator'],
                                            'id_controller' => $task_info['controller'],
                                            'id_executor'   => $task_info['executor'],
                                            'id_client'     => $task_info['client'],
                                            'name'          => $task_info['name'],
                                            'date_from'     => $task_info['date_from'],
                                            'date_to'       => $task_info['date_to'],
                                            'penalty'       => $task_info['penalty'],
                                            'id_result'     => $task_info['id_result'],
                                            'id_report'     => $task_info['id_report'],
                                        ]);

        if ($result < 1) {
            error_log($this->db->errInfo[1]);
        }

        return $result;
    }


    // формирует список задач для главной странице
    function getTasksForControl($id_user, $data)
    {
        // проверим просроченные задачи
        $this->checkExpired($id_user);

        // формируем строку-шаблон для поиска
        $seek_str = trim($data['seek_str']);

        while (strpos($seek_str, '  ') !== false) {
            $seek_str = str_replace('  ', ' ', $seek_str);
        }

        $seek_str_a = explode(' ', $seek_str);
        $seek_str = implode('%', $seek_str_a);
        $seek_str = '%' . $seek_str . '%';

        switch ((int)$data['filter']) {
            case 1:
                $filter = ' and id_condition = ' . NEW_TASK;
                break;
            case 2:
                $filter = ' and charges_penalty > 0 ';
                break;
            case 3:
                $filter = ' and id_condition in (3, 5, 2) ';
                break;
            case 4:
                $filter = ' and id_condition in (' . TASK_CANCEL . ', ' . TASK_END .') ';
                break;
            default:
                $filter = ' and id_condition not in (' . TASK_CANCEL . ', ' . TASK_END .') ';
                break;
        }

        $is_executor = (int)$data['is_executor'] ? ' and id_tip = 1 ' : ' and id_tip != 1 ';
        $query = 'select distinct
                    id_task,
                    tasks.name as name, 
                    data_end, 
                    conditions.name as `condition`, 
                    tasks.id_condition as id_condition, 
                    charges_penalty,
                    if(id_periodic = 0, "Р", "П") as periodicity
                  from 
                    task_users 
                    join tasks using (id_task) 
                    left join (
								select
									id_task, sum(penalty) as charges_penalty
								from
									penaltys
								group by
									id_task
                    ) as s_penalty using (id_task)
                    join conditions using (id_condition)
                  where 
                    id_user = :id_user 
                    ' . $is_executor . ' 
                    ' . $filter . ' 
                    and tasks.name like :seek_str
                  order by 
                    data_end, 
                    charges_penalty desc';

        return $this
                    ->db
                    ->getList($query, ['id_user' => $id_user, 'seek_str' => $seek_str]);
    }


    function getInfo($id_task)
    {
        $query = 'select
                        date_format(data_end, "%d-%m-%Y") as data_end,
                        date_format(data_begin, "%d-%m-%Y") as data_begin,
                        id_task,
                        tasks.name as task_name,
                        (select name from users join task_users using (id_user) where id_task = :id_task and id_tip = 1) as executor,
                        (select name from users join task_users using (id_user) where id_task = :id_task and id_tip = 3) as iniciator,
                        (select name from users join task_users using (id_user) where id_task = :id_task and id_tip = 2) as client,
                        (select name from users join task_users using (id_user) where id_task = :id_task and id_tip = 4) as controller,
                        date_format(data_execut, "%d-%m-%Y %H:%i") as data_execut,
                        date_format(data_client, "%d-%m-%Y %H:%i") as data_client,
                        c.name as state,
                        penalty,
                        (select sum(penalty) from penaltys where id_task = :id_task) as charges_penalty,
                        type_report.name as report_name,
                        type_result.name as result_name
                    from
                        tasks
                        join conditions as c using (id_condition)
                        left join type_report using (id_report)
                        left join type_result using (id_result)
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
        // возыращает сведения о возможных действиях над этой задачей этим пользователем

        $query = '
            select distinct
                id_action, name, need_dt, change_penalty
            from
                enable_actions join actions using (id_action)
            where
                enable_actions.id_condition = (select id_condition from tasks where id_task = :id_task)
                and id_tip in (select id_tip from task_users where id_task = :id_task and id_user = :id_user)';

        return $this
                    ->db
                    ->getList($query, ['id_task' => $id_task, 'id_user' => $id_user]);
    }


    function updateCondition($event)
    {
        // изменение состояния задачи
        $query = '
            update tasks
                set id_condition = (select id_condition from actions where id_action = :id_action)
            where
                id_task = :id_task
                and id_condition in 
                    (select id_condition from enable_actions where id_action = :id_action and id_tip in 
                        (select id_tip from task_users where id_task = :id_task and id_user = :id_user))';

        $result = $this
                    ->db
                    ->updateData($query, [
                                            'id_task'   => $event['id_task'],
                                            'id_action' => $event['id_action'],
                                            'id_user'   => $event['id_user'],
                                        ]);
        
        if ($result > 0) {

            // изменение параметров задачи
            // если id_action = 5, то вставляем последнюю дату из истории
            if ($event['id_action'] == 5) {
                // нужно узнать на какую дату перенести
                $dt = $this->getRequiredDate($event['id_task']);
                // переносим
                $this->changeDateEnd($event['id_task'], $dt['dt_wish']);
            }

            // разрешить перенос, изменим штрафные баллы
            if ($event['id_action'] == 3) {
                $this->changePenalty($event['id_task'], $event['penalty']);
            }

            if ($event['id_action'] == 12) {
                $this->changeDateExec($event['id_task']);
            }

            if ($event['id_action'] == 13) {
                $this->changeDateClient($event['id_task']);
            }

            $events = new \ssp\models\Event($this->db);

            return $events->add($event);
        }

        return -1;
    }


    // возвращаем историю событий у задачи
    function getHistoryActions($id_task)
    {
        $query = '
            select
                dt_create, dt_wish, users.name as user, actions.name as action, comment
            from
                events
                join users using (id_user)
                join actions using (id_action)
            where
                id_task = :id_task
            order by
                id_event desc';

        return $this->db->getList($query, ['id_task' => $id_task]);
    }


    // изменяем срок завершения задачи
    function changeDateEnd($id_task, $dt_end)
    {
        $query = '
            update tasks
                set data_end = :dt_end
            where
                id_task = :id_task 
                and data_end != :dt_end';

        return $this->db->updateData($query, ['id_task' => $id_task, 'dt_end' => $dt_end]);
    }


    // запрашиваем желаемую дату переноса
    function getRequiredDate($id_task)
    {
        $query = '  select
                        dt_wish
                    from
                        events
                    where
                        id_task = :id_task
                        and dt_wish is not null
                    order by
                        id_event desc
                    limit 1';

        return $this->db->getRow($query, ['id_task' => $id_task]);
    }


    function changeDateExec($id_task)
    {

        $timestamp = date('Y-m-d H:i');

        $query = '
            update tasks
                set data_execut = :timestamp
            where
                id_task = :id_task';

        return $this->db->updateData($query, ['id_task' => $id_task, 'timestamp' => $timestamp]);
    }


    function changeDateClient($id_task)
    {
        $timestamp = date('Y-m-d H:i');

        $query = '
            update tasks
                set data_client = :timestamp
            where
                id_task = :id_task';

        return $this->db->updateData($query, ['id_task' => $id_task, 'timestamp' => $timestamp]);
    }


    function getShortDetail($id_task, $id_user)
    {
        // информацию о задаче может редактировать только инициатор или контроллер,
        // и только в сотоянии - новая
        $query = '
            select
                name, data_begin, data_end, penalty,
                (select id_user from task_users where id_task = :id_task and id_tip = 1) as id_executor,
                (select id_user from task_users where id_task = :id_task and id_tip = 2) as id_client,
                (select id_user from task_users where id_task = :id_task and id_tip = 3) as id_iniciator,
                (select id_user from task_users where id_task = :id_task and id_tip = 4) as id_controller
            from
                tasks
            where
                tasks.id_task = :id_task
                and id_condition = 10
            having 
                id_iniciator = :id_user
                or id_controller = :id_user';

        return $this->db->getRow($query, ['id_task' => $id_task, 'id_user' => $id_user]);
    }


    function changeEditToNew($id_task, $id_user)
    {
        $query = '
            update tasks join task_users using (id_task)
                set id_condition = 9
            where
                id_task = :id_task
                and id_condition = 10
                and id_user = :id_user
                and id_tip in (5)';

        return $this->db->updateData($query, ['id_task' => $id_task, 'id_user' => $id_user]);
    }


    function saveAfterEdit($task_info)
    {
        $this->db->beginTransaction();

        $query = '
            update tasks join task_users using (id_task)
                set id_condition = 9,
                name = :name,
                data_begin = :data_beg,
                data_end = :data_end,
                penalty = :penalty
            where
                id_task = :id_task
                and id_condition = 10
                and id_user = :id_user
                and id_tip in (5)';

        $result1 = $this
                        ->db
                        ->updateData($query, [
                                                'id_task'    => $task_info['id_task'],
                                                'id_user'    => $task_info['id_user'],
                                                'name'       => $task_info['name'],
                                                'data_beg'   => $task_info['data_beg'],
                                                'data_end'   => $task_info['data_end'],
                                                'penalty'    => $task_info['penalty'],
                                             ]);

        $query = '  update task_users
                        set id_user = :id_executor
                    where
                        id_task = :id_task
                        and id_tip = 1';
        $result2 = $this->db->updateData($query, [
                                                    'id_task'     => $task_info['id_task'],
                                                    'id_executor' => $task_info['id_executor'],
                                                 ]);

        $query = '  update task_users
                        set id_user = :id_client
                    where
                        id_task = :id_task
                        and id_tip = 2';
        $result3 = $this->db->updateData($query, [
                                                    'id_task'   => $task_info['id_task'],
                                                    'id_client' => $task_info['id_client'],
                                                 ]);

        $query = '  update task_users
                        set id_user = :id_controller
                    where
                        id_task = :id_task
                        and id_tip = 4';
        $result4 = $this->db->updateData($query, [
                                                    'id_task'       => $task_info['id_task'],
                                                    'id_controller' => $task_info['id_controller'],
                                                ]);

        if (($result1 == -1) || ($result2 == -1) || ($result3 == -1) || ($result4 == -1)) {
            $this->db->rollBack();
            return false;
        } else {
            $this->db->commit();
            return true;
        }
    }


    // устанавливаем состояние "выполняется" для задачи, если пользователь исполнитель и задача в статусе "новая"
    function checkAndSetExec($id_task, $id_user)
    {
        $query = '  update
                        tasks join task_users using (id_task)
                    set
                        id_condition = 1
                    where
                        id_task = :id_task
                        and id_condition = 9
                        and id_tip = 1
                        and id_user = :id_user';
        
        return $this->db->updateData($query, ['id_task' => $id_task, 'id_user' => $id_user]);
    }


    // увеличиваем кол-во штрафных баллов
    function changePenalty($id_task, $penalty)
    {
        $query = '  update
                        tasks
                    set
                        penalty = penalty + :penalty
                    where
                        id_task = :id_task';

        return $this->db->updateData($query, ['id_task' => $id_task, 'penalty' => $penalty]);
    }


    // сдвигает просроченную задачу, с начислением штрафных баллов
    function moveExpiredTask($id_task, $new_dt_end)
    {
        // т.к. будем менять данные в нескольких используем транзакции
        $this->db->beginTransaction();
        $result = false;

        // начислить штрафы
        if ($this->accruePenalty($id_task)) {
            // перенести задачу

            if ($this->changeDateEnd($id_task, $new_dt_end) > 0) {

                // создаем событие "перенос просроченной задачи" (18) админом (11)
                $event = [
                    'id_task'   => $id_task,
                    'id_action' => 18,
                    'comment'   => '',
                    'id_user'   => 11,
                ];
                
                $events = new \ssp\models\Event($this->db);

                // записать событие
                if ($events->add($event) > 0) {
                    $result = true;
                }
            }
        }
        // завершаем транзакцию
        if ($result) {
            $this->db->commit();
            return true;
        } else {
            $this->db->rollBack();
            return false;
        }
    }


    // начисление штрафных баллов за просроченную задачу
    // начисление производим на учетную запись исполнителя
    function accruePenalty($id_task)
    {
        
        $query = '  insert into penaltys (id_task, id_user, penalty)
                    values (
                        :id_task, 
                        (select id_user from task_users where id_tip = 1 and id_task = :id_task), 
                        (select penalty from tasks where id_task = :id_task))';

        return $this->db->updateData($query, ['id_task' => $id_task]);
    }
    

    //ищет просроченные задачи у заданного пользователя и переносит их
    function checkExpired($id_user)
    {
        $query = 'select distinct 
                    id_task, 
                    data_end, 
                    id_condition 
                  from 
                    task_users 
                    join tasks using (id_task) 
                  where 
                    id_user = :id_user';

        $list_tasks = $this->db->getList($query, ['id_user' => $id_user]);

        $dt_now = \DateTime::createFromFormat('Y-m-d H:i', date('Y-m-d H:i'));

        foreach ($list_tasks as $one_task) {
            // просмотриваем задачи в состоянии "выполняется" и "новая"
            if (((int)$one_task['id_condition'] == 1) || ((int)$one_task['id_condition'] == 9)) {
                $dt_end = \DateTime::createFromFormat('Y-m-d H:i', $one_task['data_end'] . ' 00:00');
                $dt_end->add(new \DateInterval('P1DT8H'));

                if ($dt_end < $dt_now) {
                    // задача просрочена

                    // вычисляем разницу
                    $interval = date_diff($dt_end, $dt_now);
                    $interval_in_days = (int)$interval->format('%r%a');

                    for ($i = 0; $i <= $interval_in_days; ++$i) {
                        $this->moveExpiredTask($one_task['id_task'], $dt_end->format('Y-m-d'));
                        $dt_end->add(new \DateInterval('P1D'));
                    }
                }
            }
        }
    }


    // возвращает истину есть инициатор и потребитель одно лицо, и фальш в противном случае
    function executorIsClient($id_task, $id_user)
    {
        $query = 'select count(id_user) as cnt from task_users
            where id_task = :id_task and id_user = :id_user and id_tip in (2, 3)';

        $result = $this->db->getRow($query, ['id_task' => $id_task, 'id_user' => $id_user]);

        if ($result) {
            if ((int)$result['cnt'] == 2) {
                return true;
            }
        }

        return false;
    }
}

