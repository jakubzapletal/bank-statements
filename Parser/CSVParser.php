<?php

namespace JakubZapletal\Component\BankStatement\Parser;

use JakubZapletal\Component\BankStatement\Statement\Statement;

abstract class CSVParser extends Parser
{
    /**
     * @param string $filePath
     *
     * @return Statement
     * @throws \RuntimeException
     */
    public function parseFile($filePath)
    {
        $fileObject = new \SplFileObject($filePath);

        return $this->parseFileObject($fileObject);
    }

    /**
     * @param string $content
     *
     * @return Statement
     * @throws \InvalidArgumentException
     */
    public function parseContent($content)
    {
        if (is_string($content) === false) {
            throw new \InvalidArgumentException('Argument "$content" isn\'t a string type');
        }

        $fileObject = new \SplTempFileObject();
        $fileObject->fwrite($content);

        return $this->parseFileObject($fileObject);
    }

    /**
     * @param \SplFileObject $fileObject
     *
     * @return Statement
     */
    abstract protected function parseFileObject(\SplFileObject $fileObject);
}
