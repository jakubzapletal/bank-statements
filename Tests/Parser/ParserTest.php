<?php

namespace JakubZapletal\Component\BankStatement\Tests\Parser;

use JakubZapletal\Component\BankStatement\Parser\Parser;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Parser
     */
    protected $parser;

    protected function setUp()
    {
        $this->parser = new Parser();
    }

    /**
     * @expectedException \Exception
     */
    public function testParseException()
    {
        $this->parser->parse('file.tmp');
    }
}
 