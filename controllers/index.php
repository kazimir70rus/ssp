<?php

$task = new \ssp\models\Task($db);

function checkExpired($list_tasks, $task)
{
    foreach ($list_tasks as &$one_task) {

        // просмотриваем задачи в состоянии "выполняется" и "новая"
        if (((int)$one_task['id_condition'] == 1) || ((int)$one_task['id_condition'] == 9)) {
            $dt_end = DateTime::createFromFormat('Y-m-d H:i', $one_task['data_end'] . ' 00:00');
            $dt_end->add(new DateInterval('P1DT8H'));
            $dt_now = DateTime::createFromFormat('Y-m-d H:i', date('Y-m-d H:i'));

            if ($dt_end < $dt_now) {
                // задача просрочена

                // вычисляем разницу
                $interval = date_diff($dt_end, $dt_now);
                $interval_in_days = (int)$interval->format('%r%a');

                for ($i = 0; $i <= $interval_in_days; ++$i) {
                    if ($task->moveExpiredTask($one_task['id_task'], $dt_end->format('Y-m-d'))) {
                        $one_task['data_end'] = $dt_end->format('Y-m-d');
                    }
                    $dt_end->add(new DateInterval('P1D'));
                }
            }
        }
    }
    unset($one_task);

    return $list_tasks;
}

$list_tasks_executor = checkExpired($task->getListTip($id_user->getValue(), 1), $task);
$list_tasks_for_control = checkExpired($task->getTaskForControl($id_user->getValue()), $task);

require_once 'views/index.php';

