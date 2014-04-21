<?php

namespace JakubZapletal\Component\BankStatement\Parser;

use JakubZapletal\Component\BankStatement\Statement\StatementInterface;

interface ParserInterface
{
    /**
     * @param string $filePath
     *
     * @return StatementInterface
     */
    public function parse($filePath);

    /**
     * @return StatementInterface
     */
    public function getStatement();
}