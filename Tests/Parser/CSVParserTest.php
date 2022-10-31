<?php

namespace JakubZapletal\Component\BankStatement\Tests\Parser;

use JakubZapletal\Component\BankStatement\Parser\CSVParser;
use PHPUnit\Framework\TestCase;

class CSVParserTest extends TestCase
{
    /**
     * @var string
     */
    protected $parserClassName = CSVParser::class;

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

    public function testParseFileException()
    {
        $this->expectException(\RuntimeException::class);
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
        $parserMock = $this->getMockForAbstractClass($this->parserClassName);
        $parserMock->parseContent(123);
    }
}
