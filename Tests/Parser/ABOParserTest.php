<?php

namespace JakubZapletal\Component\BankStatement\Tests\Parser;

use DateTimeImmutable;
use JakubZapletal\Component\BankStatement\Parser\ABOParser;
use JakubZapletal\Component\BankStatement\Statement\Statement;
use JakubZapletal\Component\BankStatement\Statement\Transaction\Transaction;
use PHPUnit\Framework\TestCase;

class ABOParserTest extends TestCase
{
    /**
     * @var string
     */
    protected $parserClassName = ABOParser::class;

    public function testParseFile()
    {
        $fileObject = new \SplFileObject(tempnam(sys_get_temp_dir(), 'test_'), 'w+');

        $parserMock = $this->createPartialMock($this->parserClassName, array('parseFileObject'));
        $parserMock
            ->expects($this->once())
            ->method('parseFileObject')
            ->with($this->equalTo($fileObject))
            ->will($this->returnArgument(0))
        ;

        $this->assertSame(
            $fileObject->getRealPath(),
            $parserMock->parseFile($fileObject->getRealPath())->getRealPath()
        );
    }

    public function testParseFileException()
    {
        $this->expectException(\RuntimeException::class);
        $parser = new ABOParser();
        $parser->parseFile('test.file');
    }

    public function testParseContent()
    {
        $content = 'test';

        $parserMock = $this->createPartialMock($this->parserClassName, array('parseFileObject'));
        $parserMock
            ->expects($this->once())
            ->method('parseFileObject')
            ->with($this->isInstanceOf(\SplFileObject::class))
            ->will($this->returnValue($content))
        ;

        $this->assertEquals(
            $content,
            $parserMock->parseContent($content)
        );
    }

    public function testParseContentException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $parser = new ABOParser();
        $parser->parseContent(123);
    }

    public function testParseFileObject()
    {
        $parser = new ABOParser();

        $reflectionParser = new \ReflectionClass($this->parserClassName);
        $method = $reflectionParser->getMethod('parseFileObject');
        $method->setAccessible(true);

        # Positive statement
        $fileObject = new \SplFileObject(tempnam(sys_get_temp_dir(), 'test_'), 'w+');
        $fileObject->fwrite(
            '0741234561234567890Test s.r.o.         01011400000000100000+00000000080000+00000000060000' .
            '+00000000040000+002010214              ' . PHP_EOL
        );
        $fileObject->fwrite(
            '0750000000000012345000000000015678900000000020010000000400002000000001100100000120000000013050114' .
            'Tran 1              00203050114' . PHP_EOL
        );
        $fileObject->fwrite(
            '07600000000000000000000002001050114Protistrana s.r.o.' . PHP_EOL
        );
        $fileObject->fwrite(
            '078First line' . PHP_EOL
        );
        $fileObject->fwrite(
            '079Second line' . PHP_EOL
        );
        $fileObject->fwrite(
            '0750000000000012345000000000025678900000000020020000000600001000000002100200000220000000023070114' .
            'Tran 2              00203070114' . PHP_EOL
        );
        $statement = $method->invokeArgs($parser, array($fileObject));

        $this->assertInstanceOf(
            Statement::class,
            $statement
        );

        # Statement
        $this->assertSame($statement, $parser->getStatement());
        $this->assertEquals('123456-123456/7890', $statement->getAccountNumber());
        $this->assertEquals(new \DateTimeImmutable('2014-01-01 12:00:00'), $statement->getDateLastBalance());
        $this->assertSame(1000.00, $statement->getLastBalance());
        $this->assertSame(800.00, $statement->getBalance());
        $this->assertSame(400.00, $statement->getCreditTurnover());
        $this->assertSame(600.00, $statement->getDebitTurnover());
        $this->assertEquals(2, $statement->getSerialNumber());
        $this->assertEquals(new \DateTimeImmutable('2014-02-01 12:00:00'), $statement->getDateCreated());

        # Transactions
        $this->assertCount(2, $statement);

        $transactions = $statement->getIterator();

        /** @var Transaction $transaction */
        $transaction = $transactions->current();
        $this->assertEquals('000000-0000156789/1000', $transaction->getCounterAccountNumber());
        $this->assertEquals(2001, $transaction->getReceiptId());
        $this->assertSame(400.00, $transaction->getCredit());
        $this->assertNull($transaction->getDebit());
        $this->assertEquals(11, $transaction->getVariableSymbol());
        $this->assertEquals(12, $transaction->getConstantSymbol());
        $this->assertEquals(13, $transaction->getSpecificSymbol());
        $this->assertEquals('Tran 1', $transaction->getNote());
        $this->assertEquals(new \DateTimeImmutable('2014-01-05 12:00:00'), $transaction->getDateCreated());


        $this->assertEquals(2001, $transaction->getAdditionalInformation()->getTransferIdentificationNumber());
        $this->assertEquals(
            new DateTimeImmutable('2014-01-05 12:00:00'),
            $transaction->getAdditionalInformation()->getDeductionDate()
        );
        $this->assertEquals(
            'Protistrana s.r.o.',
            $transaction->getAdditionalInformation()->getCounterPartyName()
        );

        $this->assertEquals('First line', $transaction->getMessageStart());
        $this->assertEquals('Second line', $transaction->getMessageEnd());

        $transactions->next();
        $transaction = $transactions->current();
        $this->assertNull($transaction->getCredit());
        $this->assertSame(600.00, $transaction->getDebit());

        # Negative statement
        $fileObject = new \SplFileObject(tempnam(sys_get_temp_dir(), 'test_'), 'w+');
        $fileObject->fwrite(
            '0740000000000012345Test s.r.o.         01011400000000100000-00000000080000-00000000060000-00000000040000' .
            '-002010214              ' . PHP_EOL
        );
        $fileObject->fwrite(
            '0750000000000012345000000000015678900000000020010000000400005000000001100100000120000000013050114' .
            'Tran 1              00203050114' . PHP_EOL
        );
        $fileObject->fwrite(
            '0750000000000012345000000000025678900000000020020000000600004000000002100200000220000000023070114' .
            'Tran 2              00203070114' . PHP_EOL
        );
        $statement = $method->invokeArgs($parser, array($fileObject));

        # Statement
        $this->assertSame(-1000.00, $statement->getLastBalance());
        $this->assertSame(-800.00, $statement->getBalance());
        $this->assertSame(-400.00, $statement->getCreditTurnover());
        $this->assertSame(-600.00, $statement->getDebitTurnover());

        # Transactions
        $transactions = $statement->getIterator();

        $transaction = $transactions->current();
        $this->assertSame(-400.00, $transaction->getCredit());
        $this->assertEquals(null, $transaction->getCurrency());

        $transactions->next();
        $transaction = $transactions->current();
        $this->assertSame(-600.00, $transaction->getDebit());
        $this->assertEquals(null, $transaction->getCurrency());
    }

    public function testParseFileObjectWithCurrency()
    {
        $parser = new ABOParser();

        $reflectionParser = new \ReflectionClass($this->parserClassName);
        $method = $reflectionParser->getMethod('parseFileObject');
        $method->setAccessible(true);

        # Positive statement
        $fileObject = new \SplFileObject(tempnam(sys_get_temp_dir(), 'test_'), 'w+');
        $fileObject->fwrite(
            '0741234561234560300Test s.r.o.         01011400000000100000+00000000080000+00000000060000' .
            '+00000000040000+002010214              ' . PHP_EOL
        );
        $fileObject->fwrite(
            '0750000000000012345000000000015678900000000020010000000400002000000001100100000120000000013050114' .
            'Tran 1              00203050114' . PHP_EOL
        );

        $statement = $method->invokeArgs($parser, array($fileObject));

        # Transaction currency
        $transactions = $statement->getIterator();
        $transaction = $transactions->current();
        $this->assertEquals('CZK', $transaction->getCurrency());
    }
}
