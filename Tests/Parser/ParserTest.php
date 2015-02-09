<?php

namespace JakubZapletal\Component\BankStatement\Tests\Parser;

use JakubZapletal\Component\BankStatement\Tests\TestCase;
use JakubZapletal\Component\BankStatement\Parser\Parser;
use JakubZapletal\Component\BankStatement\Statement\Statement;


class ParserTest extends TestCase
{
    /**
     * @var Parser
     */
    protected $parser;

    protected function setUp()
    {
        $this->parser = $this->getMockForAbstractClass('\JakubZapletal\Component\BankStatement\Parser\Parser');
    }

    public function testGetStatement()
    {
        $reflectionParser = new \ReflectionClass($this->parser);
        $property = $reflectionParser->getProperty('statement');
        $property->setAccessible(true);
        $property->setValue($this->parser, new Statement());

        $this->assertInstanceOf(
            '\JakubZapletal\Component\BankStatement\Statement\Statement',
            $this->parser->getStatement()
        );
    }

    public function testGetStatementClass()
    {
        $reflectionParser = new \ReflectionClass($this->parser);
        $method = $reflectionParser->getMethod('getStatementClass');
        $method->setAccessible(true);

        $this->assertInstanceOf(
            '\JakubZapletal\Component\BankStatement\Statement\Statement',
            $method->invoke($this->parser)
        );
    }

    public function testGetTransactionClass()
    {
        $reflectionParser = new \ReflectionClass($this->parser);
        $method = $reflectionParser->getMethod('getTransactionClass');
        $method->setAccessible(true);

        $this->assertInstanceOf(
            '\JakubZapletal\Component\BankStatement\Statement\Transaction\Transaction',
            $method->invoke($this->parser)
        );
    }
}
 