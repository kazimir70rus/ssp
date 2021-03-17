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

    protected function tearDown(): void
    {
    }

    public function testgetDateEnd()
    {
        // возвращает строку
        $this->assertIsString($this->task->getDateEnd(3));

        // возвращает эта строка является датой
        $this->assertNotFalse(DateTime::createfromFormat('Y-m-d', $this->task->getDateEnd(3)));

        // при несуществующей задаче, возвращает false 
        $this->assertFalse($this->task->getDateEnd(1));
    }

    public function testiniciatorIsClient()
    {
        // изпользуем заранее заданные задачи
        $this->assertSame(true, $this->task->iniciatorIsClient(1800));

        $this->assertSame(false, $this->task->iniciatorIsClient(1802));
    }

    public function testgetRepetition()
    {
        // изпользуем заранее заданные задачи
        $this->assertSame(2, $this->task->getRepetition(2003));

        $this->assertSame(1, $this->task->getRepetition(1800));
    }

    public function testgetTip()
    {
        $this->assertIsArray($this->task->getTip(1800, 71));

        $this->assertIsArray($this->task->getTip(1800, 1));

        $this->assertSame([2, 3, 5], $this->task->getTip(1800, 71));

        $this->assertSame([4], $this->task->getTip(1800, 75));
    }

    public function testcheckTip()
    {
        $this->assertSame(1, $this->task->checkTip(1800, 71, 2));

        $this->assertSame(0, $this->task->checkTip(1800, 71, 1));

        $this->assertSame(0, $this->task->checkTip(1800, 11, 1));
    }

    public function testcheckAccess()
    {
        $this->assertSame(true, $this->task->checkAccess(1800, 71));

        $this->assertSame(false, $this->task->checkAccess(1, 70));
    }

    public function testgetIdPeriodic()
    {
        $this->assertSame(0, $this->task->getIdPeriodic(1800));

        $this->assertSame(326, $this->task->getIdPeriodic(2003));
    }
}

