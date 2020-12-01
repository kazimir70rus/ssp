<?php
require_once '../configuration/db.php';
require_once '../module/db.php';
require_once '../models/task.php';
require_once '../models/guide.php';


Class GuideTest extends \PHPUnit\Framework\TestCase
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

    public function testgetTypeReports()
    {
        $this->assertIsArray($this->guide->getTypeReports());
    }

    public function testgetTypeResults()
    {
        $this->assertIsArray($this->guide->getTypeResults());

        $this->assertIsArray($this->guide->getTypeResults('rtry'));

        $this->assertSame(0, count($this->guide->getTypeResults('rtry')));
    }

    public function testgetNameTypeResult()
    {
        $this->assertIsString($this->guide->getNameTypeResult(1));

        $this->assertIsString($this->guide->getNameTypeResult(0));
    }
}

