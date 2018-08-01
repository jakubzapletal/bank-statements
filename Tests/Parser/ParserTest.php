<?php

namespace JakubZapletal\Component\BankStatement\Tests\Parser;

use JakubZapletal\Component\BankStatement\Parser\Parser;
use JakubZapletal\Component\BankStatement\Statement\Statement;
use JakubZapletal\Component\BankStatement\Statement\Transaction\Transaction;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    /**
     * @var Parser
     */
    protected $parser;

    protected function setUp()
    {
        $this->parser = $this->getMockForAbstractClass(Parser::class);
    }

    public function testGetStatement()
    {
        $reflectionParser = new \ReflectionClass($this->parser);
        $property = $reflectionParser->getProperty('statement');
        $property->setAccessible(true);
        $property->setValue($this->parser, new Statement());

        $this->assertInstanceOf(
            Statement::class,
            $this->parser->getStatement()
        );
    }

    public function testGetStatementClass()
    {
        $reflectionParser = new \ReflectionClass($this->parser);
        $method = $reflectionParser->getMethod('getStatementClass');
        $method->setAccessible(true);

        $this->assertInstanceOf(
            Statement::class,
            $method->invoke($this->parser)
        );
    }

    public function testGetTransactionClass()
    {
        $reflectionParser = new \ReflectionClass($this->parser);
        $method = $reflectionParser->getMethod('getTransactionClass');
        $method->setAccessible(true);

        $this->assertInstanceOf(
            Transaction::class,
            $method->invoke($this->parser)
        );
    }
}
