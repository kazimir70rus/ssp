<?php

namespace ssp\models;

define('NEW_TASK', 9);
define('TASK_CANCEL', 7);
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

        $timestamp = date('Y-m-d H:i');

        $query = '  insert into tasks
                        (name, data_begin, data_end, penalty, id_result, id_report, id_periodic, data_create)
                    values
                        (:name, :data_begin, :data_end, :penalty, :id_result, :id_report, :id_periodic, :data_create)';

        $id_task = $this
                        ->db
                        ->insertData($query, [
                                                'name'        => $task_info['name'],
                                                'data_begin'  => $task_info['data_begin'],
                                                'data_end'    => $task_info['data_end'],
                                                'penalty'     => $task_info['penalty'],
                                                'id_result'   => $task_info['id_result'],
                                                'id_report'   => $task_info['id_report'],
                                                'id_periodic' => $id_periodic,
                                                'data_create' => $timestamp,
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
                                                    'id_executor'   => $task_info['id_executor'],
                                                    'id_iniciator'  => $task_info['id_iniciator'],
                                                    'id_client'     => $task_info['id_client'],
                                                    'id_controller' => $task_info['id_controller'],
                                                    'id_author'     => $task_info['id_author'],
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
        $query = '
            insert into periodic
                (id_author, id_iniciator, id_controller, id_executor, id_client, name, date_from, 
                date_to, penalty, id_result, id_report, repetition, custom_interval)
            values
                (:id_author, :id_iniciator, :id_controller, :id_executor, :id_client, :name, 
                :date_from, :date_to, :penalty, :id_result, :id_report, :repetition, :interval)';

        $result = $this
                    ->db
                    ->insertData($query, [
                                            'id_author'     => $task_info['id_author'],
                                            'id_iniciator'  => $task_info['id_iniciator'],
                                            'id_controller' => $task_info['id_controller'],
                                            'id_executor'   => $task_info['id_executor'],
                                            'id_client'     => $task_info['id_client'],
                                            'name'          => $task_info['name'],
                                            'date_from'     => $task_info['date_from'],
                                            'date_to'       => $task_info['date_to'],
                                            'penalty'       => $task_info['penalty'],
                                            'id_result'     => $task_info['id_result'],
                                            'id_report'     => $task_info['id_report'],
                                            'repetition'    => $task_info['repetition'],
                                            'interval'      => $task_info['interval'],
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

        // проверим потребителей
        $this->penaltyClient($id_user);

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
                $filter = ' and id_condition in (' . NEW_TASK . ', 19) ';
                break;
            case 2:
                $filter = ' and charges_penalty > 0 ';
                break;
            case 3:
                $filter = ' and id_condition in (3, 5, 2, 11, 16, 17, 18) ';
                break;
            case 4:
                $filter = ' and id_condition in (' . TASK_CANCEL . ', ' . TASK_END .') ';
                break;
            default:
                $filter = ' and id_condition not in (' . TASK_CANCEL . ', ' . TASK_END .') ';
                break;
        }

        // сортировка по исполнителю, нужна только для контролируемых задач
        if ((int)$data['is_executor']) {
            $is_executor = ' id_user = :id_user and id_tip = 1 ';
        } else {
            $is_executor = '
                task_users.id_task in (
                        select id_task from task_users where id_user = :id_user and id_tip in (2, 3, 4)
                )';
            if ((int)$data['id_executor']) {
                $is_executor .= ' and id_tip = 1  and id_user = ' . (int)$data['id_executor'] . ' ';
            } else {
                $is_executor .= ' and id_tip != 1 ';
            }
        }

        // сортировка по датам
        $date_from = \DateTime::createFromFormat('Y-m-d', $data['date_from']);
        $date_to = \DateTime::createFromFormat('Y-m-d', $data['date_to']);

        if ($date_from) {
            $date_seek = ' and data_end = "' . $date_from->format('Y-m-d') . '" ';
            if ($date_to) {
                $date_seek = ' and data_end between "' . $date_from->format('Y-m-d') . '" and "' . $date_to->format('Y-m-d') . '" ';
            }
        } else {
            $date_seek = '';
        }

        $query = 'select distinct
                    id_task,
                    tasks.name as name,
                    date_format(data_end, "%d-%m-%Y") as data_end,
                    data_end as date_end,
                    conditions.name as `condition`,
                    tasks.id_condition as id_condition,
                    (
                        select
                            sum(penalty)
                        from
                            penaltys as p
                        where
                            p.id_task = tasks.id_task
                            and p.id_user = (select id_user from task_users as t where t.id_task = tasks.id_task and id_tip = 1)
                    ) as penalty_executor,
                    (
                        select
                            sum(penalty)
                        from
                            penaltys as p
                        where
                            p.id_task = tasks.id_task
                            and p.id_user = (select id_user from task_users as t where t.id_task = tasks.id_task and id_tip = 2)
                    ) as penalty_client,
                    if(id_periodic = 0, "Р", "П") as periodicity,
                    (select name from task_users join users using (id_user)
                       where id_tip = 1 and task_users.id_task = tasks.id_task
                    ) as name_executor,
                    (select name from task_users join users using (id_user)
                       where id_tip = 2 and task_users.id_task = tasks.id_task
                    ) as name_client,
                    penalty,
                    date_format(data_create, "%d-%m-%Y %H:%i") as data_create
                  from
                    task_users
                    join tasks using (id_task)
                    join conditions using (id_condition)
                  where
                    ' . $is_executor . '
                    ' . $filter . '
                    ' . $date_seek . '
                    and tasks.name like :seek_str
                  order by
                    date_end,
                    penalty_executor desc,
                    penalty_client desc';

        return $this
                    ->db
                    ->getList($query, ['id_user' => $id_user, 'seek_str' => $seek_str]);
    }


    // начисление штрафных баллов за просроченную задачу
    // по умолчанию начисление производим на учетную запись исполнителя
    function accruePenalty($id_task, $dt, $id_tip = 1)
    {
        $query = '
            insert into penaltys (id_task, id_user, penalty, dt)
            values(
                :id_task,
                (select id_user from task_users where id_tip = :id_tip and id_task = :id_task),
                (select penalty from tasks where id_task = :id_task),
                :dt
            )
        ';

        return $this->db->insertData($query, ['id_task' => $id_task, 'id_tip' => $id_tip, 'dt' => $dt]);
    }


    // начисление штрафных баллов потребителю за затягивание сроков принятия задачи
    // если с момента отчета испонителя прошло более 2 дня и 8 часов,
    // а потребитель не подтвердил, начисление ему штрафа
    function penaltyClient($id_user)
    {
        // список задач которые ожидают принятия потребителем
        $query = '
            select 
                id_task, date_format(data_execut, "%Y-%m-%d") as data_execut, id_user, penalty
            from
                tasks
                join task_users using (id_task)
            where
                id_task in (select id_task from task_users where id_user = :id_user)
                and id_condition = 3
                and id_tip = 2
        ';

        $tasks = $this->db->getList($query, ['id_user' => $id_user]);

        $current_dt = \DateTime::createFromFormat('Y-m-d H:i', date('Y-m-d H:i'));

        // подготавливаем переменные для записи в историю событий
        $events = new \ssp\models\Event($this->db);
        $event = [
            'id_action' => 22,
            'comment'   => '',
            'id_user'   => 11,
        ];

        foreach ($tasks as $one) {

            $event['id_task'] = $one['id_task'];

            $data_execut = \DateTime::createFromFormat('Y-m-d', $one['data_execut']);

            // отработаем попадания на выходные
            switch ($data_execut->format('N')) {
                case '4':
                    $data_execut->add(new \DateInterval('P4DT8H'));
                    break;
                case '5':
                    $data_execut->add(new \DateInterval('P3DT8H'));
                    break;
                default:
                    $data_execut->add(new \DateInterval('P2DT8H'));
                    break;
            }

            if ($data_execut < $current_dt) {
                // потребитель затягивает сроки
                if ($this->accruePenalty($one['id_task'], $current_dt->format('Y-m-d'), 2) > 0) {
                    // добавляем событие в историю
                    $event['dt_create'] = $current_dt->format('Y-m-d');
                    $events->add($event);
                }
            }
        }
    }


    function getInfo($id_task)
    {
        $query = '
            select
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
                (
                    select
                        sum(penalty)
                    from
                        penaltys as p
                    where
                        p.id_task = tasks.id_task
                        and p.id_user = (select id_user from task_users as t where t.id_task = tasks.id_task and id_tip = 1)
                ) as penalty_executor,
                (
                    select
                        sum(penalty)
                    from
                        penaltys as p
                    where
                        p.id_task = tasks.id_task
                        and p.id_user = (select id_user from task_users as t where t.id_task = tasks.id_task and id_tip = 2)
                ) as penalty_client,
                type_report.name as report_name,
                type_result.name as result_name,
                if(id_periodic = 0, "Разовая", "Периодическая") as periodicity,
                if(id_periodic = 0, 1, (select repetition from periodic where periodic.id_periodic = tasks.id_periodic)) as repetition,
                if(id_periodic = 0, 1, (select custom_interval from periodic where periodic.id_periodic = tasks.id_periodic)) as custom_interval,
                date_format(data_create, "%d-%m-%Y %H:%i") as data_create
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


    // возвращает дейсвтия по умолчанию
    function getDefaultAction($id_task, $id_user)
    {
        $query = '
            select distinct
                id_action, name, need_dt, change_penalty
            from
                enable_actions join actions using (id_action)
            where
                enable_actions.id_condition = (select id_condition from tasks where id_task = :id_task)
                and id_tip in (select id_tip from task_users where id_task = :id_task and id_user = :id_user)';

        return $this->db->getList($query, ['id_task' => $id_task, 'id_user' => $id_user]);
    }


    // проверка нужен ли файл, для закрытия задачи
    function isDokNeed($id_task)
    {
        $query = 'select need_file from tasks join type_report using (id_report) where id_task = :id_task';

        $data = $this->db->getRow($query, ['id_task' => $id_task]);

        if ((int)$data['need_file'] === 0) {
            return false;
        }

        return true;
    }


    // проверим файлы уже загружены
    function isDokLoad($id_task, $id_user)
    {
        // проверим были ли переносы
        $query = '
            select
                id_event
            from
                events
            where
                id_task = :id_task and id_action = 15
            order by
                id_event desc
            limit 1
        ';
        $data = $this->db->getRow($query, ['id_task' => $id_task]);

        if (is_array($data) && count($data)) {

            $query = '
                select
                    count(*) as cnt
                from
                    uploaddoks
                where
                    id_task = :id_task
                    and id_author = :id_user
                    and filename in (
                        select comment from events where id_action = 21 and id_event > :id_event)
            ';
            $data = $this->db->getRow($query, ['id_task' => $id_task, 'id_user' => $id_user, 'id_event' => $data['id_event']]);
        } else {

            // для закрытия задачи необходимо наличие файла под авторством исполнителя
            $query = 'select count(*) as cnt from uploaddoks where id_task = :id_task and id_author = :id_user';
            $data = $this->db->getRow($query, ['id_task' => $id_task, 'id_user' => $id_user]);
        }

        if ((int)$data['cnt'] > 0) {

            return true;
        }

        return false;
    }


    function getAction($id_task, $id_user)
    {
        // возвращает сведения о возможных действиях над этой задачей этим пользователем
        $result = $this->getDefaultAction($id_task, $id_user);

        // для выполнения некоторых действий нужно выполнение определенных условий
        // либо какие действия нужно удалить
        
        $iniciator_is_client = $this->iniciatorIsClient($id_task);

        foreach ($result as $index => $action) {

            // список действий которые нужно проконтроллировать на наличие файла
            if (
                ((int)$action['id_action'] === 1) ||
                ((int)$action['id_action'] === 25) ||
                ((int)$action['id_action'] === 28) ||
                ((int)$action['id_action'] === 12)
               ) {

                if ($this->isDokNeed($id_task) && (!$this->isDokLoad($id_task, $id_user))) {
                    unset($result[$index]);
                }
            }

            // перенос по запросу исполнителя: 25 (инициатор и потребитель разные) и 28 (инициатор и потребитель один и тот же)
            // нужно удалить из списка лишнее действие
            if (((int)$action['id_action'] === 25) && $iniciator_is_client) {
                unset($result[$index]);
            }
                
            if (((int)$action['id_action'] === 28) && !$iniciator_is_client) {
                unset($result[$index]);
            }

            // перенос по запросу инициатора: 32 (инициатор и потребитель разные) и 23 (инициатор и потребитель один и тот же)
            if (((int)$action['id_action'] === 32) && $iniciator_is_client) {
                unset($result[$index]);
            }
                
            if (((int)$action['id_action'] === 23) && !$iniciator_is_client) {
                unset($result[$index]);
            }

        }

        return array_values($result);
    }


    // удаление задачи
    function deleteTask($id_task)
    {
        // узнаем тип задачи
        $query = 'select id_periodic from tasks where id_task = :id_task';

        $result = $this->db->getRow($query, ['id_task' => $id_task]);

        if (count($result) > 0) {

            if ((int)$result['id_periodic'] == 0) {

                return $this->delOneTimeTask($id_task);
            }

            $result = $this->delPeriodicTask($id_task);

            return $result;
        }

        return false;
    }


    function updateCondition($event)
    {
        // проверим на удаление
        if ((int)$event['id_action'] == 20) {

            return $this->deleteTask($event['id_task']);
        }

        // изменение состояния задачи
        $query = '
            update tasks
                set id_condition = 
                    if((select id_condition from actions where id_action = :id_action) = 12,
                        id_condition, (select id_condition from actions where id_action = :id_action))
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

        if (($result > 0) || ((int)$event['id_action'] == 17) || ((int)$event['id_action'] == 23)) {

            // изменение параметров задачи
            // перенос срока задачи 
            if (
                ($event['id_action'] === 5)  ||
                ($event['id_action'] === 31) ||
                ($event['id_action'] === 33) ||
                ($event['id_action'] === 23) 
               ) {
                // переносим
                $this->changeDateEnd($event['id_task'], $event['dt']);
            }

            // увеличим штрафные баллы если они не равны нулю
            if (isset($event['penalty']) && (int)$event['penalty'] > 0) {
                $this->changePenalty($event);
            }

            if ($event['id_action'] == 12) {
                $this->changeDateExec($event['id_task']);

                // проверить, если инициатор и потребитель одно лицо,
                // то изменить состояние задачи на 11 (подтверждение инициатором или контроллером)
                if ($this->iniciatorIsClient($event['id_task'])) {
                    $query = 'update tasks set id_condition = 11 where id_task = :id_task';
                    $this->db->updateData($query, ['id_task' => $event['id_task']]);
                }
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
                date_format(dt_create, "%d-%m-%Y") as dt_create,
                date_format(dt_wish, "%d-%m-%Y") as dt_wish,
                users.name as user, actions.name as action, comment
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
        ';

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
                name, data_begin, data_end, penalty, id_result, id_report,
                (select id_user from task_users where id_task = :id_task and id_tip = 1) as id_executor,
                (select id_user from task_users where id_task = :id_task and id_tip = 2) as id_client,
                (select id_user from task_users where id_task = :id_task and id_tip = 3) as id_iniciator,
                (select id_user from task_users where id_task = :id_task and id_tip = 4) as id_controller,
                if(id_periodic = 0, 1, (select repetition from periodic where periodic.id_periodic = tasks.id_periodic)) as repetition,
                if(id_periodic = 0, "", (select date_to from periodic where periodic.id_periodic = tasks.id_periodic)) as date_to
            from
                tasks
            where
                tasks.id_task = :id_task
                and id_condition in (9, 10)
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
                data_begin = :data_begin,
                data_end = :data_end,
                penalty = :penalty,
                id_result = :id_result,
                id_report = :id_report
            where
                id_task = :id_task
                and id_condition in (9, 10) 
                and id_user = :id_author
                and id_tip in (5)';

        $result1 = $this
                        ->db
                        ->updateData($query, [
                                                'id_task'    => $task_info['id_task'],
                                                'id_author'  => $task_info['id_author'],
                                                'name'       => $task_info['name'],
                                                'data_begin' => $task_info['data_begin'],
                                                'data_end'   => $task_info['data_end'],
                                                'penalty'    => $task_info['penalty'],
                                                'id_result'  => $task_info['id_result'],
                                                'id_report'  => $task_info['id_report'],
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
        $query = '
            update
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
    // также проверим на id_action, т.к. не все действия могут увеличивать штраф
    function changePenalty($event)
    {
        $query = '
            update
                tasks
            set
                penalty = penalty + :penalty
            where
                id_task = :id_task
                and :id_action in (select id_action from actions where change_penalty = 1)
        ';

        return $this->db->updateData($query, ['id_task' => $event['id_task'], 'penalty' => $event['penalty'], 'id_action' => $event['id_action']]);
    }


    // сдвигает просроченную задачу, с начислением штрафных баллов
    function moveExpiredTask($id_task, $new_dt_end)
    {
        // т.к. будем менять данные в нескольких используем транзакции
        $this->db->beginTransaction();
        $result = false;

        // начислить штрафы
        if ($this->accruePenalty($id_task, $new_dt_end)) {

            // перенести задачу
            if ($this->changeDateEnd($id_task, $new_dt_end) > 0) {

                // создаем событие "перенос просроченной задачи" (18) админом (11)
                $event = [
                    'id_task'   => $id_task,
                    'id_action' => 18,
                    'comment'   => '',
                    'id_user'   => 11,
                    'dt_create' => $new_dt_end,
                ];

                $events = new \ssp\models\Event($this->db);

                // добавить событие в журнал
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


    //ищет просроченные задачи у заданного пользователя и переносит их
    function checkExpired($id_user)
    {
        $query = '
            select distinct
                id_task,
                data_end,
                id_condition
            from
                task_users
                join tasks using (id_task)
            where
                id_user = :id_user
        ';

        $list_tasks = $this->db->getList($query, ['id_user' => $id_user]);

        $dt_now = \DateTime::createFromFormat('Y-m-d H:i', date('Y-m-d H:i'));

        foreach ($list_tasks as $one_task) {
            // просмотриваем задачи в состоянии "выполняется" и "новая"
            if (
                ((int)$one_task['id_condition'] == 1) ||
                ((int)$one_task['id_condition'] == 19) ||
                ((int)$one_task['id_condition'] == 9)
               ) {
                // текущий срок задачи
                $dt_end = \DateTime::createFromFormat('Y-m-d H:i', $one_task['data_end'] . ' 00:00');

                // если срок выпадает на пятницу, то? ничего, далее эта ситуация будет обработана
                $dt_end->add(new \DateInterval('P1DT8H'));

                if ($dt_end < $dt_now) {
                    // задача просрочена

                    while ($dt_end <= $dt_now) {

                        if (($dt_end->format('N') != '6') && ($dt_end->format('N') != '7')) {
                            // если выпадает на будни, то переносим с начислением штрафных баллов
                            $this->moveExpiredTask($one_task['id_task'], $dt_end->format('Y-m-d'));
                        }

                        $dt_end->add(new \DateInterval('P1D'));
                    }
                }
            }
        }
    }


    // возвращает истину если инициатор и потребитель одно лицо, и фальш в противном случае
    function iniciatorIsClient($id_task)
    {
        $query = '
            select
                count(distinct id_user) as cnt
            from
                task_users
            where
                id_task = :id_task and id_tip in (2, 3)
        ';

        $result = $this->db->getRow($query, ['id_task' => $id_task]);

        if (is_array($result) && ((int)$result['cnt'] === 1)) {

            return true;
        }

        return false;
    }


    // проверям доступ пользователя к задаче
    function checkAccess($id_task, $id_user)
    {
        $role = $this->getTip($id_task, $id_user);

        if (count($role) > 0) {
            return true;
        }

        return false;
    }


    // возвращает роль или роли пользователя в задаче
    function getTip($id_task, $id_user)
    {
        $query = '
            select
                id_tip
            from
                task_users
            where
                id_task = :id_task
                and id_user = :id_user
        ';

        $result = $this->db->getList($query, ['id_task' => $id_task, 'id_user' => $id_user]);

        if (is_array($result) && count($result)) {
            // т.к. ролей может быть несколько сформируем массив ролей
            $role = [];

            foreach ($result as $row) {
                $role[] = (int)$row['id_tip'];
            }

            return $role;
        } else {

            return [];
        }
    }


    function createPeriodicTasks($task_template)
    {
        // граница интервалов
        $dt_st = \DateTime::createFromFormat('Y-m-d', $task_template['date_from']);
        $dt_en = \DateTime::createFromFormat('Y-m-d', $task_template['date_to']);

        $days = 0;
        $offset = 0;
        // формируем строку для интервала
        switch ($task_template['repetition']) {
            case 2:
                $interval = 'P1D';
                break;
            case 3:
                $interval = 'P7D';
                break;
            case 4:
                $interval = 'P1M';
                $offset = (int)$dt_st->format('j') - 28;

                if ($offset > 0) {
                    $dt_st->sub(new \DateInterval('P' . $offset . 'D'));
                }

                break;
            case 7:
                $interval = 'P3M';
                $offset = (int)$dt_st->format('j') - 28;

                if ($offset > 0) {
                    $dt_st->sub(new \DateInterval('P' . $offset . 'D'));
                }

                break;
            case 5:
                $interval = 'P1Y';
                break;
            case 6:
                $days = (int)($task_template['period'] ?? 30);
                $days = ($days < 1) ? 30 : $days;
                $interval = 'P' . $days . 'D';
                break;
        }

        // записываем в таблицу периодических задач
        $task_template['interval'] = $days;
        $id_periodic = $this->addPeriodic($task_template);

        $dt_curr = \DateTime::createFromFormat('Y-m-d', $dt_st->format('Y-m-d'));

        $id_tasks = [];

        while ($dt_curr <= $dt_en) {

            // если задача ежедневная и выпадает на выходные, то ее не добавляем
            if (!(($task_template['repetition'] == 2) && (($dt_curr->format('N') == '6') || ($dt_curr->format('N') == '7')))) {
                
                $dt_echo = \DateTime::createFromFormat('Y-m-d', $dt_curr->format('Y-m-d'));

                switch ($offset) {
                    case 3:
                        $dm = (int)$dt_echo->format('t') - 28;
                        $dt_echo->add(new \DateInterval('P' . $dm . 'D'));
                        break;
                    
                    case 2:
                        $dm = ((int)$dt_echo->format('t') === 28) ? 0 : 2;
                        $dt_echo->add(new \DateInterval('P' . $dm . 'D'));
                        break;
                    
                    case 1:
                        $dm = ((int)$dt_echo->format('t') === 28) ? 0 : 1;
                        $dt_echo->add(new \DateInterval('P' . $dm . 'D'));
                        break;

                    default:
                        break;
                }

                $task_template['data_begin'] = \ssp\module\Datemod::dateNoWeekends($dt_echo->format('Y-m-d'));
                $task_template['data_end'] = $task_template['data_begin'];

                // добавляем задачу
                $id_task = $this->add($task_template, $id_periodic);

                if ($id_task) {
                    $id_tasks[] = $id_task;
                }
            }

            $dt_curr->add(new \DateInterval($interval));
        }

        return $id_tasks;
    }


    // возвращает период повторений для указанной задачи
    function getRepetition($id_task)
    {
        $query = 'select
                        if(repetition is null, 1, repetition) as repetition
                  from
                        tasks left join periodic using (id_periodic)
                  where
                        id_task = :id_task';

        $result = $this->db->getRow($query, ['id_task' => $id_task]);

        if ($result != -1) {
            return $result['repetition'];
        } else {
            return false;
        }
    }


    // удалени разовой задачи
    function delOneTimeTask($id_task)
    {
        $this->db->beginTransaction();
        $result = false;

        $query = 'delete from task_users where id_task = :id_task';

        if ($this->db->updateData($query, ['id_task' => $id_task]) != -1) {

            $query = 'delete from events where id_task = :id_task';

            if ($this->db->updateData($query, ['id_task' => $id_task]) != -1) {

                $query = 'delete from penaltys where id_task = :id_task';

                if ($this->db->updateData($query, ['id_task' => $id_task]) != -1) {

                    $query = 'delete from tasks where id_task = :id_task and id_condition in (9, 10) and data_end >= :cur_date';

                    if ($this->db->updateData($query, ['id_task' => $id_task, 'cur_date' => date('Y-m-d')]) != -1) {

                        $result = true;
                    }
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


    // удаление периодической задачи и ее потомков
    function delPeriodicTask($id_task)
    {
        $this->db->beginTransaction();
        $result = false;

        $cur_date = date('Y-m-d');

        $query = '
            delete from
                task_users
            where
                id_task in (
                    select
                        id_task
                    from
                        tasks
                    where
                        data_end >= :cur_date and
                        id_condition in (9, 10) and
                        id_periodic = (select id_periodic from tasks where id_task = :id_task))';

        if ($this->db->updateData($query, ['id_task' => $id_task, 'cur_date' => $cur_date]) != -1) {

            $query = '
                delete from
                    events
                where
                    id_task in (
                        select
                            id_task
                        from
                            tasks
                        where
                            data_end > :cur_date and
                            id_condition in (9, 10) and
                            id_periodic = (select id_periodic from tasks where id_task = :id_task))';

            if ($this->db->updateData($query, ['id_task' => $id_task, 'cur_date' => $cur_date]) != -1) {

                $query = '
                    delete from
                        periodic
                    where
                        id_periodic = (select id_periodic from tasks where id_task = :id_task)';

                if ($this->db->updateData($query, ['id_task' => $id_task]) != -1) {

                    $query = '
                        delete from
                            penaltys
                        where
                            id_task in (
                                select id_task from tasks where id_periodic = (
                                    select id_periodic from tasks where id_task = :id_task
                                )
                            )
                    ';

                    if ($this->db->updateData($query, ['id_task' => $id_task]) != -1) {

                        $query = '
                            delete from
                                tasks
                            where
                                data_end > :cur_date and
                                id_condition in (9, 10) and
                                id_periodic = (select id_periodic from tasks where id_task = :id_task)';

                        if ($this->db->updateData($query, ['id_task' => $id_task, 'cur_date' => $cur_date]) != -1) {

                            $result = true;
                        }
                    }
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


    // получение списка исполнителей у задач, находящихся под контролем у конкретного пользователя
    function getExesForControl($id_user)
    {
        $query = '
            select
                distinct task_users.id_user as id_user, name
            from
                task_users join users using (id_user)
            where
                id_tip = 1
                and id_task in (
                    select id_task from task_users where id_user = :id_user and id_tip in (2, 3, 4)
                )';

        return $this->db->getList($query, ['id_user' => $id_user]);
    }


    function getReport($id_user)
    {
        $query = '
            select distinct
                if(id_periodic = 0, "Р", "П") as periodicity,
                date_format(data_create, "%d-%m-%Y") as data_create,
                date_format(data_create, "%H:%i") as time_create,
                date_format(data_end, "%d-%m-%Y") as data_end,
                tasks.name as name, 
                conditions.name as `condition`,
                (select name from task_users join users using (id_user)
                   where id_tip = 1 and task_users.id_task = tasks.id_task
                ) as name_executor,
                (select name from task_users join users using (id_user)
                   where id_tip = 2 and task_users.id_task = tasks.id_task
                ) as name_client,
                (select name from task_users join users using (id_user)
                   where id_tip = 3 and task_users.id_task = tasks.id_task
                ) as name_iniciator,
                (select name from task_users join users using (id_user)
                   where id_tip = 4 and task_users.id_task = tasks.id_task
                ) as name_controller,
                type_result.name as name_result,
                type_report.name as name_report,
                penalty,
                date_format(data_execut, "%d-%m-%Y") as data_execut,
                date_format(data_client, "%d-%m-%Y") as data_client,
                charges_penalty,
                data_end as date_end
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
                join type_report using (id_report)
                join type_result using (id_result)
              where
                task_users.id_user = :id_user
              order by
                date_end,
                charges_penalty desc
        ';

        return $this->db->getList($query, ['id_user' => $id_user]);
    }
}

