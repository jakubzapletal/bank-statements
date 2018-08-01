<?php

namespace JakubZapletal\Component\BankStatement\Tests\Parser;

use PHPUnit\Framework\TestCase;

class XMLParserTest extends TestCase
{
    /**
     * @var string
     */
    protected $parserClassName = '\JakubZapletal\Component\BankStatement\Parser\XMLParser';

    public function testParseFile()
    {
        $content = 'test';

        $fileObject = new \SplFileObject(tempnam(sys_get_temp_dir(), 'test_'), 'w+');
        $fileObject->fwrite($content);

        $parserMock = $this->getMockForAbstractClass(
            $this->parserClassName,
            array(),
            '',
            true,
            true,
            true,
            array('parseContent')
        );
        $parserMock
            ->expects($this->once())
            ->method('parseContent')
            ->with($this->equalTo($content))
            ->will($this->returnArgument(0))
        ;

        $this->assertSame(
            $content,
            $parserMock->parseFile($fileObject->getRealPath())
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testParseFileException()
    {
        $parserMock = $this->getMockForAbstractClass($this->parserClassName);
        $parserMock->parseFile('test.file');
    }

    public function testParseContent()
    {
        $content = 'test';

        $crawlerMock = $this->createMock('\Symfony\Component\DomCrawler\Crawler');
        $crawlerMock
            ->expects($this->once())
            ->method('addXmlContent')
            ->with($this->equalTo($content))
            ->will($this->returnValue(0))
        ;

        $parserMock = $this->getMockForAbstractClass(
            $this->parserClassName,
            array(),
            '',
            true,
            true,
            true,
            array('getCrawlerClass')
        );
        $parserMock
            ->expects($this->once())
            ->method('getCrawlerClass')
            ->will($this->returnValue($crawlerMock))
        ;
        $parserMock
            ->expects($this->once())
            ->method('parseCrawler')
            ->with($this->equalTo($crawlerMock))
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
    public function testParseContentExceptionNotString()
    {
        $parserMock = $this->getMockForAbstractClass($this->parserClassName);
        $parserMock->parseContent(123);
    }

    public function testGetCrawlerClass()
    {
        $parserMock = $this->getMockForAbstractClass($this->parserClassName);

        $reflectionParser = new \ReflectionClass($parserMock);
        $method = $reflectionParser->getMethod('getCrawlerClass');
        $method->setAccessible(true);

        $this->assertInstanceOf(
            '\Symfony\Component\DomCrawler\Crawler',
            $method->invoke($parserMock)
        );
    }
}
