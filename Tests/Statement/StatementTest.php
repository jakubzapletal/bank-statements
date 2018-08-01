<?php

namespace JakubZapletal\Component\BankStatement\Tests\Statement;

use JakubZapletal\Component\BankStatement\Statement\Statement;
use PHPUnit\Framework\TestCase;

class StatementTest extends TestCase
{
    /**
     * @var Statement
     */
    protected $statement;

    protected function setUp()
    {
        $this->statement = new Statement();
    }

    public function testBalance()
    {
        $balance = 123.45;

        $this->statement->setBalance($balance);
        $this->assertEquals($balance, $this->statement->getBalance());
    }

    public function testCreditTurnover()
    {
        $creditTurnover = 123.45;

        $this->statement->setCreditTurnover($creditTurnover);
        $this->assertEquals($creditTurnover, $this->statement->getCreditTurnover());
    }

    public function testDateCreated()
    {
        $date = new \DateTime('2014-05-28');

        $this->statement->setDateCreated($date);
        $this->assertEquals($date, $this->statement->getDateCreated());
    }

    public function testDateCreatedException()
    {
        if (!class_exists('\TypeError')) {
            $this->expectException('\Exception');
        } else {
            $this->expectException('\TypeError');
        }

        $this->statement->setDateCreated('2014-05-28');
    }

    public function testSerialNumber()
    {
        $serialNumber = 12;

        $this->statement->setSerialNumber($serialNumber);
        $this->assertEquals($serialNumber, $this->statement->getSerialNumber());
    }

    public function testDebitTurnover()
    {
        $debitTurnover = 123.45;

        $this->statement->setDebitTurnover($debitTurnover);
        $this->assertEquals($debitTurnover, $this->statement->getDebitTurnover());
    }

    public function testAccountNumber()
    {
        $accountNumber = '123456789';

        $this->statement->setAccountNumber($accountNumber);
        $this->assertEquals($accountNumber, $this->statement->getAccountNumber());
    }

    public function testParsedAccountNumber()
    {
        $accountNumber = '1231234567890/0100';
        $this->statement->setAccountNumber($accountNumber);
        $parsedAccountNumber = $this->statement->getParsedAccountNumber();
        $this->assertEquals('123', $parsedAccountNumber['prefix']);
        $this->assertEquals('1234567890', $parsedAccountNumber['number']);
        $this->assertEquals('0100', $parsedAccountNumber['bankCode']);

        $accountNumber = '123-1234567890/0100';
        $this->statement->setAccountNumber($accountNumber);
        $parsedAccountNumber = $this->statement->getParsedAccountNumber();
        $this->assertEquals('123', $parsedAccountNumber['prefix']);
        $this->assertEquals('1234567890', $parsedAccountNumber['number']);
        $this->assertEquals('0100', $parsedAccountNumber['bankCode']);

        $accountNumber = '123456789/0100';
        $this->statement->setAccountNumber($accountNumber);
        $parsedAccountNumber = $this->statement->getParsedAccountNumber();
        $this->assertNull($parsedAccountNumber['prefix']);
        $this->assertEquals('123456789', $parsedAccountNumber['number']);
        $this->assertEquals('0100', $parsedAccountNumber['bankCode']);

        $accountNumber = '1231234567890';
        $this->statement->setAccountNumber($accountNumber);
        $parsedAccountNumber = $this->statement->getParsedAccountNumber();
        $this->assertEquals('123', $parsedAccountNumber['prefix']);
        $this->assertEquals('1234567890', $parsedAccountNumber['number']);
        $this->assertNull($parsedAccountNumber['bankCode']);
    }

    public function testTransactions()
    {
        $transactionMock_1 = $this->createMock('JakubZapletal\Component\BankStatement\Statement\Transaction\Transaction');
        $transactionMock_1
            ->expects($this->any())
            ->method('getReceiptId')
            ->will($this->returnValue(11))
        ;

        $transactionMock_2 = $this->createMock('JakubZapletal\Component\BankStatement\Statement\Transaction\Transaction');
        $transactionMock_2
            ->expects($this->any())
            ->method('getReceiptId')
            ->will($this->returnValue(22))
        ;

        $this->statement
            ->addTransaction($transactionMock_1)
            ->addTransaction($transactionMock_2)
            ->addTransaction($transactionMock_2)
        ;

        $this->assertCount(2, $this->statement->getTransactions());

        $this->assertEquals(2, $this->statement->count());

        $this->statement->rewind();
        $this->assertEquals(11, $this->statement->current()->getReceiptId());
        $this->assertSame(0, $this->statement->key());

        $this->assertEquals(22, $this->statement->next()->getReceiptId());

        $this->assertFalse($this->statement->next());
        $this->assertFalse($this->statement->valid());

        $this->statement->removeTransaction($transactionMock_2);
        $this->assertCount(1, $this->statement->getTransactions());
    }

    public function testAddTransactionException()
    {
        $stdClassMock = $this->createMock('\stdClass');

        if (!class_exists('\TypeError')) {
            $this->expectException('\Exception');
        } else {
            $this->expectException('\TypeError');
        }

        $this->statement->addTransaction($stdClassMock);
    }

    public function testRemoveTransactionException()
    {
        $transactionMock = $this->createMock('JakubZapletal\Component\BankStatement\Statement\Transaction\Transaction');

        $this->statement->addTransaction($transactionMock);

        $stdClassMock = $this->createMock('\stdClass');

        if (!class_exists('\TypeError')) {
            $this->expectException('\Exception');
        } else {
            $this->expectException('\TypeError');
        }

        $this->statement->removeTransaction($stdClassMock);
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

    public function testDateLastBalanceException()
    {
        if (!class_exists('\TypeError')) {
            $this->expectException('\Exception');
        } else {
            $this->expectException('\TypeError');
        }

        $this->statement->setDateLastBalance('2014-05-28');
    }
}
