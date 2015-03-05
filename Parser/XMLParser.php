<?php

namespace JakubZapletal\Component\BankStatement\Parser;

use Symfony\Component\DomCrawler\Crawler;
use JakubZapletal\Component\BankStatement\Statement\Statement;

abstract class XMLParser extends Parser
{
    /**
     * @param string $filePath
     *
     * @return Statement
     * @throw \InvalidArgumentException
     */
    public function parseFile($filePath)
    {
        if (file_exists($filePath) === false) {
            throw new \InvalidArgumentException('File "' . $filePath . '" doesn\'t exists');
        }

        $content = file_get_contents($filePath);

        return $this->parseContent($content);
    }

    /**
     * @param string $content
     *
     * @return Statement
     * @throw \InvalidArgumentException
     *
     */
    public function parseContent($content)
    {
        if (is_string($content) === false) {
            throw new \InvalidArgumentException('Argument "$content" isn\'t a string type');
        }

        $crawler = $this->getCrawlerClass();
        $crawler->addXmlContent($content);

        return $this->parseCrawler($crawler);
    }

    /**
     * @param Crawler $crawler
     *
     * @return Statement
     */
    protected abstract function parseCrawler(Crawler $crawler);

    /**
     * @return Crawler
     */
    protected function getCrawlerClass()
    {
        return new Crawler();
    }
}
