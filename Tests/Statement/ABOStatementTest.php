<?php

namespace JakubZapletal\Component\BankStatement\Tests\Statement;

use JakubZapletal\Component\BankStatement\Statement\ABOStatement;

class ABOStatementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ABOStatement
     */
    protected $statement;

    protected function setUp()
    {
        $this->statement = new ABOStatement();
    }

    public function testLastBalance()
    {
        $balance = 123.45;

        $this->statement->setLastBalance($balance);
        $this->assertEquals($balance, $this->statement->getLastBalance());
    }

    public function testDateLastBalance()
    {
        $date = new \DateTime('2014-05-28');

        $this->statement->setDateLastBalance($date);
        $this->assertEquals($date, $this->statement->getDateLastBalance());
    }

    /**
     * @expectedException \Exception
     */
    public function testDateLastBalanceException()
    {
        $this->statement->setDateLastBalance('2014-05-28');
    }
}
 