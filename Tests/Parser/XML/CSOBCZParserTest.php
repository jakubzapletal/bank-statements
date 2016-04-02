<?php

namespace JakubZapletal\Component\BankStatement\Tests\Parser\XML;

use JakubZapletal\Component\BankStatement\Parser\XML\CSOBCZParser;
use Symfony\Component\DomCrawler\Crawler;

class CSOBCZParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $parserClassName = '\JakubZapletal\Component\BankStatement\Parser\XML\CSOBCZParser';

    public function testParseContent()
    {
        $text = 'ěščřžýáíéúůĚŠČŘŽÝÁÍÉÚŮ';
        $content = '<tag>' . $text . '</tag>';

        $parserMock = $this->getMock($this->parserClassName, array('parseCrawler'));
        $parserMock
            ->expects($this->once())
            ->method('parseCrawler')
            ->with($this->isInstanceOf('\Symfony\Component\DomCrawler\Crawler'))
            ->will($this->returnArgument(0))
        ;

        $this->assertEquals(
            $text,
            $parserMock->parseContent(iconv("UTF-8", "WINDOWS-1250", $content))->text()
        );
    }

    public function testParseCrawler()
    {
        $parser = new CSOBCZParser();

        $reflectionParser = new \ReflectionClass($this->parserClassName);
        $method = $reflectionParser->getMethod('parseCrawler');
        $method->setAccessible(true);

        # Positive statement
        $crawler = new Crawler();
        $content = '
            <FINSTA>
                <FINSTA03>
                    <S28_CISLO_VYPISU>2</S28_CISLO_VYPISU>
                    <S25_CISLO_UCTU>12345/0300</S25_CISLO_UCTU>
                    <S60_CD_INDIK>C</S60_CD_INDIK>
                    <S60_DATUM>01.01.2014</S60_DATUM>
                    <S60_CASTKA>1000,00</S60_CASTKA>
                    <SUMA_KREDIT>=400,00</SUMA_KREDIT>
                    <SUMA_DEBIT>=600,00</SUMA_DEBIT>
                    <S62_CD_INDIK>C</S62_CD_INDIK>
                    <S62_DATUM>01.02.2014</S62_DATUM>
                    <S62_CASTKA>800,00</S62_CASTKA>
                    <FINSTA05>
                        <REF_TRANS_SYS>2001</REF_TRANS_SYS>
                        <DPROCD>05.01.2014</DPROCD>
                        <S61_CD_INDIK>C</S61_CD_INDIK>
                        <S61_CASTKA>+400,00</S61_CASTKA>
                        <S86_SPECSYMOUR>13</S86_SPECSYMOUR>
                        <S86_VARSYMOUR>11</S86_VARSYMOUR>
                        <S86_KONSTSYM>12</S86_KONSTSYM>
                        <PART_ACCNO>156789</PART_ACCNO>
                        <PART_BANK_ID>1000</PART_BANK_ID>
                        <PART_ID1_1>Tran 1</PART_ID1_1>
                        <PART_ID1_2/>
                        <PART_ID2_1/>
                        <PART_ID2_2/>
                    </FINSTA05>
                    <FINSTA05>
                        <REF_TRANS_SYS>2002</REF_TRANS_SYS>
                        <DPROCD>07.01.2014</DPROCD>
                        <S61_CD_INDIK>D</S61_CD_INDIK>
                        <S61_CASTKA>+600,00</S61_CASTKA>
                        <S86_SPECSYMOUR>23</S86_SPECSYMOUR>
                        <S86_VARSYMOUR>21</S86_VARSYMOUR>
                        <S86_KONSTSYM>22</S86_KONSTSYM>
                        <PART_ACCNO>256789</PART_ACCNO>
                        <PART_BANK_ID>2000</PART_BANK_ID>
                        <PART_ID1_1>Tran 2</PART_ID1_1>
                        <PART_ID1_2/>
                        <PART_ID2_1/>
                        <PART_ID2_2/>
                    </FINSTA05>
                </FINSTA03>
            </FINSTA>
        ';
        $crawler->addXmlContent($content);

        $statement = $method->invokeArgs($parser, array($crawler));

        $this->assertInstanceOf(
            '\JakubZapletal\Component\BankStatement\Statement\Statement',
            $statement
        );

        # Statement
        $this->assertSame($statement, $parser->getStatement());
        $this->assertEquals('12345/0300', $statement->getAccountNumber());
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
        $crawler = new Crawler();
        $content = '
            <FINSTA>
                <FINSTA03>
                    <S28_CISLO_VYPISU>2</S28_CISLO_VYPISU>
                    <S25_CISLO_UCTU>12345/0300</S25_CISLO_UCTU>
                    <S60_CD_INDIK>D</S60_CD_INDIK>
                    <S60_DATUM>01.01.2014</S60_DATUM>
                    <S60_CASTKA>1000,00</S60_CASTKA>
                    <SUMA_KREDIT>=-400,00</SUMA_KREDIT>
                    <SUMA_DEBIT>=-600,00</SUMA_DEBIT>
                    <S62_CD_INDIK>D</S62_CD_INDIK>
                    <S62_DATUM>01.02.2014</S62_DATUM>
                    <S62_CASTKA>800,00</S62_CASTKA>
                    <FINSTA05>
                        <REF_TRANS_SYS>2001</REF_TRANS_SYS>
                        <DPROCD>05.01.2014</DPROCD>
                        <S61_CD_INDIK>CR</S61_CD_INDIK>
                        <S61_CASTKA>+400,00</S61_CASTKA>
                        <S86_SPECSYMOUR>13</S86_SPECSYMOUR>
                        <S86_VARSYMOUR>11</S86_VARSYMOUR>
                        <S86_KONSTSYM>12</S86_KONSTSYM>
                        <PART_ACCNO>156789</PART_ACCNO>
                        <PART_BANK_ID>1000</PART_BANK_ID>
                        <PART_ID1_1>Tran 1</PART_ID1_1>
                        <PART_ID1_2/>
                        <PART_ID2_1/>
                        <PART_ID2_2/>
                    </FINSTA05>
                    <FINSTA05>
                        <REF_TRANS_SYS>2002</REF_TRANS_SYS>
                        <DPROCD>07.01.2014</DPROCD>
                        <S61_CD_INDIK>DR</S61_CD_INDIK>
                        <S61_CASTKA>+600,00</S61_CASTKA>
                        <S86_SPECSYMOUR>23</S86_SPECSYMOUR>
                        <S86_VARSYMOUR>21</S86_VARSYMOUR>
                        <S86_KONSTSYM>22</S86_KONSTSYM>
                        <PART_ACCNO>256789</PART_ACCNO>
                        <PART_BANK_ID>2000</PART_BANK_ID>
                        <PART_ID1_1>Tran 2</PART_ID1_1>
                        <PART_ID1_2/>
                        <PART_ID2_1/>
                        <PART_ID2_2/>
                    </FINSTA05>
                </FINSTA03>
            </FINSTA>
        ';
        $crawler->addXmlContent($content);

        $statement = $method->invokeArgs($parser, array($crawler));

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
}
