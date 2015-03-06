<?php

namespace JakubZapletal\Component\BankStatement\Parser;

use JakubZapletal\Component\BankStatement\Statement\BankAccount;
use JakubZapletal\Component\BankStatement\Statement\Statement;
use JakubZapletal\Component\BankStatement\Statement\Transaction\Transaction;

/**
 * The ABO format is commonly used for exchanging financial messages in the Czech Republic and Slovakia
 *
 * @see https://github.com/jakubzapletal/bank-statements/blob/master/doc/abo.md
 *
 * Class Statement
 * @package JakubZapletal\Component\BankStatement\Parser
 */
class ABOParser extends Parser
{
    const LINE_TYPE_STATEMENT = 'statement';
    const LINE_TYPE_TRANSACTION = 'transaction';

    const POSTING_CODE_DEBIT = 1;
    const POSTING_CODE_CREDIT = 2;
    const POSTING_CODE_DEBIT_REVERSAL = 4;
    const POSTING_CODE_CREDIT_REVERSAL = 5;

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
    protected function parseFileObject(\SplFileObject $fileObject)
    {
        $this->statement = $this->getStatementClass();

        foreach ($fileObject as $line) {
            if ($fileObject->valid()) {
                switch ($this->getLineType($line)) {
                    case self::LINE_TYPE_STATEMENT:
                        $this->parseStatementLine($line);
                        break;
                    case self::LINE_TYPE_TRANSACTION:
                        $transaction = $this->parseTransactionLine($line);
                        $this->statement->addTransaction($transaction);
                        break;
                }
            }
        }

        return $this->statement;
    }

    /**
     * @param string $line
     * @throws \Exception
     */
    /** @noinspection PhpInconsistentReturnPointsInspection */
    protected function getLineType($line)
    {
        switch (substr($line, 0, 3)) {
            case '074':
                return self::LINE_TYPE_STATEMENT;
            case '075':
                return self::LINE_TYPE_TRANSACTION;
        }

        return null;
    }

    /**
     * Sequence No. | Name | F/V | Minimum Length | Maximum Length | Content | Comment
     * -------------|------|-----|----------------|----------------|---------|--------
     * 1  | Type of record              | F | 3  | 074                  |
     * 2  | Client account number       | F | 16 | NNNNNNNNNNNNNNNN     | 1
     * 3  | Abbreviated client name     | F | 20 | AAAAAAAAAAAAAAAAAAAA |
     * 4  | Old balance name            | F | 6  | ddmmyy               |
     * 5  | Old balance                 | F | 14 | NNNNNNNNNNNNNN       | 5
     * 6  | Old balance sign            | F | 1  | (plus) or (minus)    | 2
     * 7  | New balance                 | F | 14 | NNNNNNNNNNNNNN       | 5
     * 8  | New balance sign            | F | 1  | (plus) or (minus)    | 2
     * 9  | Transactions – debit        | F | 14 | NNNNNNNNNNNNNN       | 5
     * 10 | Sign of debit transactions  | F | 1  | (plus) or (minus)    | 3
     * 11 | Transactions – credit       | F | 14 | NNNNNNNNNNNNNN       | 5
     * 12 | Sign of credit transactions | F | 1  | (plus) or (minus)    | 3
     * 13 | Statement sequence number   | F | 3  | NNN                  |
     * 14 | Posting date                | F | 6  | ddmmyy               |
     * 15 | Filler                      | F | 14 | (space)              | 4
     * 16 | End-of-record character     | F | 2  | CR LF                |
     *
     * @see https://github.com/jakubzapletal/bank-statements/blob/master/doc/abo.md
     *
     * @param string $line
     */
    protected function parseStatementLine($line)
    {
        # Account number
        $this->statement->setBankAccount($this->parseBankAccount($line));

        # Date last balance
        $this->statement->setDateLastBalance($this->parseDateLastBalance($line));

        # Last balance
        $this->statement->setLastBalance($this->parseLastBalance($line));

        # Balance
        $this->statement->setBalance($this->parseBalance($line));

        # Debit turnover
        $this->statement->setDebitTurnover($this->parseDebitTurnover($line));

        # Credit turnover
        $this->statement->setCreditTurnover($this->parseCreditTurnover($line));

        # Serial number
        $this->statement->setSerialNumber($this->parseSerialNumber($line));

        # Date created
        $this->statement->setDateCreated($this->parseDateCreated($line));
    }

    /**
     * Sequence No. | Name | F/V | Minimum Length | Maximum Length | Content | Comment
     * -------------|------|-----|----------------|----------------|---------|--------
     * 1  | Type of record          | F  | 3  | 075
     * 2  | Client account number   | F  | 16 | NNNNNNNNNNNNNNNN     | 1
     * 3  | Counter-account number  | F  | 16 | NNNNNNNNNNNNNNNN     | 1,2
     * 4  | Document number         | F  | 13 | AAAAAAAAAAAAA        | 3
     * 5  | Amount                  | F  | 12 | NNNNNNNNNNNN         | 10
     * 6  | Posting code            | F  | 1  | N                    | 4
     * 7  | V-symbol                | F  | 10 | NNNNNNNNNN           |
     * 8  | K-symbol.               | F  | 10 | NNNNNNNNNN           | 5
     * 9  | S-symbol                | F  | 10 | NNNNNNNNNN           |
     * 10 | Value                   | F  | 6  | ddmmyy               | 6
     * 11 | Additional detail       | F  | 20 | AAAAAAAAAAAAAAAAAAAA | 7
     * 12 | Change of item code     | F  | 1  | A                    | 8
     * 13 | Type of data            | F  | 4  | rmoo                 | 9
     * 14 | Due date                | F  | 6  | ddmmyy               |
     * 15 | End-of-record character | F  | 2  | CR LF                |
     *
     * @see https://github.com/jakubzapletal/bank-statements/blob/master/doc/abo.md
     *
     * @param string $line
     *
     * @return Transaction
     */
    protected function parseTransactionLine($line)
    {
        $transaction = $this->getTransactionClass();

        # Receipt ID
        $transaction->setReceiptId($this->parseReceiptId($line));

        # Debit / Credit
        switch ($this->parsePostingCode($line)) {
            case self::POSTING_CODE_DEBIT:
                $transaction->setDebit($this->parseAmount($line));
                break;
            case self::POSTING_CODE_CREDIT:
                $transaction->setCredit($this->parseAmount($line));
                break;
            case self::POSTING_CODE_DEBIT_REVERSAL:
                $transaction->setDebit($this->parseAmount($line) * (-1));
                break;
            case self::POSTING_CODE_CREDIT_REVERSAL:
                $transaction->setCredit($this->parseAmount($line) * (-1));
                break;
        }

        # Variable symbol
        $transaction->setVariableSymbol($this->parseVariableSymbol($line));

        # Constant symbol
        $transaction->setConstantSymbol($this->parseConstantSymbol($line));

        # Counter account number
        $transaction->setCounterBankAccount($this->parseCounterBankAccount($line));

        # Specific symbol
        $transaction->setSpecificSymbol($this->parseSpecificSymbol($line));

        # Note
        $transaction->setNote($this->parseNote($line));

        # Date created
        $transaction->setDateCreated($this->parseTransactionDateCreated($line));

        return $transaction;
    }

    /**
     * @param $line
     * @return BankAccount
     */
    protected function parseBankAccount($line)
    {
        $prefix = ltrim(substr($line, 3, 6), '0');
        $number = ltrim(substr($line, 9, 10), '0');
        return new BankAccount($prefix, $number);
    }

    /**
     * @param $line
     * @return \DateTime
     */
    protected function parseDateLastBalance($line)
    {
        $date = substr($line, 39, 6);
        return \DateTime::createFromFormat('dmyHis', $date . '120000');
    }

    /**
     * @param $line
     * @return float|int
     */
    protected function parseLastBalance($line)
    {
        $lastBalance = ltrim(substr($line, 45, 14), '0') / 100;
        $lastBalanceSign = substr($line, 59, 1);
        if ($lastBalanceSign === '-') {
            $lastBalance *= -1;
        }
        return $lastBalance;
    }

    /**
     * @param $line
     * @return float|int
     */
    protected function parseBalance($line)
    {
        $balance = ltrim(substr($line, 60, 14), '0') / 100;
        $balanceSign = substr($line, 74, 1);
        if ($balanceSign === '-') {
            $balance *= -1;
        }
        return $balance;
    }

    /**
     * @param $line
     * @return float|int
     */
    protected function parseDebitTurnover($line)
    {
        $debitTurnover = ltrim(substr($line, 75, 14), '0') / 100;
        $debitTurnoverSign = substr($line, 89, 1);
        if ($debitTurnoverSign === '-') {
            $debitTurnover *= -1;
        }
        return $debitTurnover;
    }

    /**
     * @param $line
     * @return float|int
     */
    protected function parseCreditTurnover($line)
    {
        $creditTurnover = ltrim(substr($line, 90, 14), '0') / 100;
        $creditTurnoverSign = substr($line, 104, 1);
        if ($creditTurnoverSign === '-') {
            $creditTurnover *= -1;
        }
        return $creditTurnover;
    }

    /**
     * @param $line
     * @return string
     */
    protected function parseSerialNumber($line)
    {
        return substr($line, 105, 3) * 1;
    }

    /**
     * @param $line
     * @return \DateTime
     */
    protected function parseDateCreated($line)
    {
        $date = substr($line, 108, 6);
        return \DateTime::createFromFormat('dmyHis', $date . '120000');
    }

    /**
     * @param $line
     * @return string
     */
    protected function parseReceiptId($line)
    {
        return ltrim(substr($line, 35, 13), '0');
    }

    /**
     * @param $line
     * @return float
     */
    protected function parseAmount($line)
    {
        return ltrim(substr($line, 48, 12), '0') / 100;
    }

    /**
     * @param $line
     * @return int
     */
    protected function parsePostingCode($line)
    {
        return intval(substr($line, 60, 1));
    }

    /**
     * @param $line
     * @return string
     */
    protected function parseVariableSymbol($line)
    {
        return ltrim(substr($line, 61, 10), '0');
    }

    /**
     * @param $line
     * @return string
     */
    protected function parseConstantSymbol($line)
    {
        return ltrim(substr($line, 77, 4), '0');
    }

    /**
     * @param $line
     * @return BankAccount
     */
    protected function parseCounterBankAccount($line)
    {
        $prefix = ltrim(substr($line, 19, 6), '0');
        $number = ltrim(substr($line, 25, 10), '0');
        $bankCode = ltrim(substr($line, 73, 4), '0');
        return new BankAccount($prefix, $number, $bankCode);
    }

    /**
     * @param $line
     * @return string
     */
    protected function parseSpecificSymbol($line)
    {
        return ltrim(substr($line, 81, 10), '0');
    }

    /**
     * @param $line
     * @return string
     */
    protected function parseNote($line)
    {
        return rtrim(substr($line, 97, 20));
    }

    /**
     * @param $line
     * @return \DateTime
     */
    protected function parseTransactionDateCreated($line)
    {
        $date = substr($line, 122, 6);
        return \DateTime::createFromFormat('dmyHis', $date . '120000');
    }
}
