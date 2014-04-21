<?php

namespace JakubZapletal\Component\BankStatement\Parser;

use JakubZapletal\Component\BankStatement\Statement\Statement;
use JakubZapletal\Component\BankStatement\Statement\Transaction\Transaction;

class Parser implements ParserInterface
{
    /**
     * @var Statement
     */
    protected $statement;

    /**
     * @param string $filePath
     *
     * @return Statement
     */
    public function parse($filePath){}

    /**
     * @return Statement
     */
    public function getStatement()
    {
        return $this->statement;
    }

    /**
     * Get a new instance of statement class
     *
     * @return Statement
     */
    protected function getStatementClass()
    {
        return new Statement();
    }

    /**
     * Get a new instance of transaction class
     *
     * @return Transaction
     */
    protected function getTransactionClass()
    {
        return new Transaction();
    }
} 