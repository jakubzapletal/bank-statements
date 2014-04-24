<?php

namespace JakubZapletal\Component\BankStatement\Tests\Parser;

class CSVParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $parserClassName = '\JakubZapletal\Component\BankStatement\Parser\CSVParser';

    public function testParseFile()
    {
        $fileObject = new \SplFileObject(tempnam(sys_get_temp_dir(), 'test_'), 'w+');

        $parserMock = $this->getMockForAbstractClass($this->parserClassName);
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

    /**
     * @expectedException \RuntimeException
     */
    public function testParseFileException()
    {
        $parserMock = $this->getMockForAbstractClass($this->parserClassName);
        $parserMock->parseFile('test.file');
    }

    public function testParseContent()
    {
        $content = 'test';

        $parserMock = $this->getMockForAbstractClass($this->parserClassName);
        $parserMock
            ->expects($this->once())
            ->method('parseFileObject')
            ->with($this->isInstanceOf('\SplFileObject'))
            ->will($this->returnValue($content))
        ;

        $this->assertEquals(
            $content,
            $parserMock->parseContent($content)
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testParseContentException()
    {
        $parserMock = $this->getMockForAbstractClass($this->parserClassName);
        $parserMock->parseContent(123);
    }
}
 