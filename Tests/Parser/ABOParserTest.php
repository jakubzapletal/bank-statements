<?php

namespace JakubZapletal\Component\BankStatement\Tests\Parser;

use JakubZapletal\Component\BankStatement\Parser\ABOParser;

class ABOParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ABOParser
     */
    protected $parser;

    /**
     * @var \SplFileObject
     */
    protected $filePositive;

    /**
     * @var \SplFileObject
     */
    protected $fileNegative;

    protected function setUp()
    {
        $this->parser = new ABOParser();

        $this->filePositive = new \SplFileObject(tempnam(sys_get_temp_dir(), 'test_'), 'w+');
        $this->filePositive->fwrite(
            '0740000000000012345Test s.r.o.         01011400000000100000+00000000080000+00000000060000+00000000040000+002010214              ' . PHP_EOL
        );
        $this->filePositive->fwrite(
            '0750000000000012345000000000015678900000000020010000000400002000000001100100000120000000013050114Tran 1              01102050114' . PHP_EOL
        );
        $this->filePositive->fwrite(
            '0750000000000012345000000000025678900000000020020000000600001000000002100200000220000000023070114Tran 2              01101070114' . PHP_EOL
        );

        $this->fileNegative = new \SplFileObject(tempnam(sys_get_temp_dir(), 'test_'), 'w+');
        $this->fileNegative->fwrite(
            '0740000000000012345Test s.r.o.         01011400000000100000-00000000080000-00000000060000-00000000040000-002010214              ' . PHP_EOL
        );
        $this->fileNegative->fwrite(
            '0750000000000012345000000000015678900000000020010000000400004000000001100100000120000000013050114Tran 1              01102050114' . PHP_EOL
        );
        $this->fileNegative->fwrite(
            '0750000000000012345000000000025678900000000020020000000600003000000002100200000220000000023070114Tran 2              01101070114' . PHP_EOL
        );
    }

    public function testParse()
    {
        # Positive statement
        $statement = $this->parser->parse($this->filePositive->getRealPath());

        $this->assertInstanceOf(
            '\JakubZapletal\Component\BankStatement\Statement\Statement',
            $statement
        );

        # Statement
        $this->assertSame($statement, $this->parser->getStatement());
        $this->assertEquals('12345', $statement->getAccountNumber());
        $this->assertEquals(new \DateTime('2014-01-01 12:00:00'), $statement->getDateLastBalance());
        $this->assertSame(1000.00, $statement->getLastBalance());
        $this->assertSame(800.00, $statement->getBalance());
        $this->assertSame(400.00, $statement->getCreditTurnover());
        $this->assertSame(600.00, $statement->getDebitTurnover());
        $this->assertEquals(2, $statement->getSerialNumber());
        $this->assertEquals(new \DateTime('2014-02-01 12:00:00'), $statement->getDateCreated());

        # Transactions
        $statement->rewind();
        $this->assertCount(2, $statement);

        $transaction = $statement->current();
        $this->assertEquals('156789/1000', $transaction->getCounterAccountNumber());
        $this->assertEquals(2001, $transaction->getReceiptId());
        $this->assertSame(400.00, $transaction->getCredit());
        $this->assertNull($transaction->getDebit());
        $this->assertEquals(11, $transaction->getVariableSymbol());
        $this->assertEquals(12, $transaction->getConstantSymbol());
        $this->assertEquals(13, $transaction->getSpecificSymbol());
        $this->assertEquals('Tran 1', $transaction->getNote());
        $this->assertEquals(new \DateTime('2014-01-05 12:00:00'), $transaction->getDateCreated());

        $transaction = $statement->next();
        $this->assertNull($transaction->getCredit());
        $this->assertSame(600.00, $transaction->getDebit());

        # Negative statement
        $statement = $this->parser->parse($this->fileNegative->getRealPath());

        # Statement
        $this->assertSame(-1000.00, $statement->getLastBalance());
        $this->assertSame(-800.00, $statement->getBalance());
        $this->assertSame(-400.00, $statement->getCreditTurnover());
        $this->assertSame(-600.00, $statement->getDebitTurnover());

        # Transactions
        $statement->rewind();

        $transaction = $statement->current();
        $this->assertSame(-400.00, $transaction->getCredit());

        $transaction = $statement->next();
        $this->assertSame(-600.00, $transaction->getDebit());
    }

    /**
     * @expectedException \Exception
     */
    public function testParseException()
    {
        $this->parser->parse('file.tmp');
    }
}
 