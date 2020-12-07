<?php
require_once '../configuration/db.php';
require_once '../module/db.php';
require_once '../models/task.php';
require_once '../models/guide.php';


Class TaskTest extends \PHPUnit\Framework\TestCase
{
    protected $db;
    protected $guide;
    protected $task;


    protected function setUp(): void
    {
        $this->db = new \ssp\module\Db(new \ssp\configuration\DB);
        $this->task = new \ssp\models\Task($this->db);
        $this->guide = new \ssp\models\Guide($this->db);
    }

    public function testgetDateEnd()
    {
        $this->assertIsString($this->task->getDateEnd(3));
    }

    public function testiniciatorIsClient()
    {
        // изпользуем заранее заданные задачи
        $this->assertSame(true, $this->task->iniciatorIsClient(1800));
        $this->assertSame(false, $this->task->iniciatorIsClient(1802));
    }
}

