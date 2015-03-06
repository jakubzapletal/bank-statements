<?php

namespace JakubZapletal\Component\BankStatement\Tests\Statement\Transaction;

use JakubZapletal\Component\BankStatement\Statement\BankAccount;
use JakubZapletal\Component\BankStatement\Statement\Transaction\Transaction;

class TransactionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Transaction
     */
    protected $transaction;

    protected function setUp()
    {
        $this->transaction = new Transaction();
    }

    public function testCounterAccountNumber()
    {
        $counterAccountNumber = '123456/7890';
        $bankAccount = new BankAccount();
        $bankAccount->setFormatted($counterAccountNumber);
        $this->transaction->setCounterBankAccount($bankAccount);
        $this->assertEquals($counterAccountNumber, $this->transaction->getCounterAccountNumber());
    }

    public function testConstantSymbol()
    {
        $constantSymbol = '308';

        $this->transaction->setConstantSymbol($constantSymbol);
        $this->assertEquals($constantSymbol, $this->transaction->getConstantSymbol());
    }

    public function testCredit()
    {
        $credit = 123.45;

        $this->transaction->setCredit($credit);
        $this->assertEquals($credit, $this->transaction->getCredit());
    }

    public function testDateCreated()
    {
        $date = new \DateTime('2014-05-28');

        $this->transaction->setDateCreated($date);
        $this->assertEquals($date, $this->transaction->getDateCreated());
    }

    /**
     * @expectedException \Exception
     */
    public function testDateCreatedException()
    {
        $this->transaction->setDateCreated('2014-05-28');
    }

    public function testDebit()
    {
        $debit = 123.45;

        $this->transaction->setDebit($debit);
        $this->assertEquals($debit, $this->transaction->getDebit());
    }

    public function testNote()
    {
        $note = 'Some note';

        $this->transaction->setNote($note);
        $this->assertEquals($note, $this->transaction->getNote());
    }

    public function testReceiptId()
    {
        $receiptId = 12;

        $this->transaction->setReceiptId($receiptId);
        $this->assertEquals($receiptId, $this->transaction->getReceiptId());
    }

    public function testSpecificSymbol()
    {
        $specificSymbol = 12345;

        $this->transaction->setSpecificSymbol($specificSymbol);
        $this->assertEquals($specificSymbol, $this->transaction->getSpecificSymbol());
    }

    public function testVariableSymbol()
    {
        $variableSymbol = 12345;

        $this->transaction->setVariableSymbol($variableSymbol);
        $this->assertEquals($variableSymbol, $this->transaction->getVariableSymbol());
    }
}
 