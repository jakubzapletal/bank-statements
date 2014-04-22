<?php

namespace JakubZapletal\Component\BankStatement\Tests\Parser;

use JakubZapletal\Component\BankStatement\Parser\Parser;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var \SplFileObject
     */
    protected $file;

    protected function setUp()
    {
        $this->parser = new Parser();

        $this->file = new \SplFileObject(tempnam(sys_get_temp_dir(), 'test_'), 'w+');
    }

    public function testParse()
    {
        $statement = $this->parser->parse($this->file->getRealPath());

        $this->assertInstanceOf(
            '\JakubZapletal\Component\BankStatement\Statement\Statement',
            $statement
        );

        $this->assertSame($statement, $this->parser->getStatement());
    }

    /**
     * @expectedException \Exception
     */
    public function testParseException()
    {
        $this->parser->parse('file.tmp');
    }

}
 