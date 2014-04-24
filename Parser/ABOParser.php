<?php

namespace JakubZapletal\Component\BankStatement\Parser;

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
    const LINE_TYPE_STATEMENT   = 'statement';
    const LINE_TYPE_TRANSACTION = 'transaction';

    const POSTING_CODE_DEBIT           = 1;
    const POSTING_CODE_CREDIT          = 2;
    const POSTING_CODE_DEBIT_REVERSAL  = 4;
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
        $accountNumber = ltrim(substr($line, 3, 16), '0');
        $this->statement->setAccountNumber($accountNumber);

        # Date last balance
        $date = substr($line, 39, 6);
        $dateLastBalance = \DateTime::createFromFormat('dmyHis', $date . '120000');
        $this->statement->setDateLastBalance($dateLastBalance);

        # Last balance
        $lastBalance = ltrim(substr($line, 45, 14), '0') / 100;
        $lastBalanceSign = substr($line, 59, 1);
        if ($lastBalanceSign === '-') {
            $lastBalance *= -1;
        }
        $this->statement->setLastBalance($lastBalance);

        # Balance
        $balance = ltrim(substr($line, 60, 14), '0') / 100;
        $balanceSign = substr($line, 74, 1);
        if ($balanceSign === '-') {
            $balance *= -1;
        }
        $this->statement->setBalance($balance);

        # Debit turnover
        $debitTurnover = ltrim(substr($line, 75, 14), '0') / 100;
        $debitTurnoverSign = substr($line, 89, 1);
        if ($debitTurnoverSign === '-') {
            $debitTurnover *= -1;
        }
        $this->statement->setDebitTurnover($debitTurnover);

        # Credit turnover
        $creditTurnover = ltrim(substr($line, 90, 14), '0') / 100;
        $creditTurnoverSign = substr($line, 104, 1);
        if ($creditTurnoverSign === '-') {
            $creditTurnover *= -1;
        }
        $this->statement->setCreditTurnover($creditTurnover);

        # Serial number
        $serialNumber = substr($line, 105, 3) * 1;
        $this->statement->setSerialNumber($serialNumber);

        # Date created
        $date = substr($line, 108, 6);
        $dateCreated = \DateTime::createFromFormat('dmyHis', $date . '120000');
        $this->statement->setDateCreated($dateCreated);
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
        $receiptId = ltrim(substr($line, 35, 13), '0');
        $transaction->setReceiptId($receiptId);

        # Debit / Credit
        $amount = ltrim(substr($line, 48, 12), '0') / 100;
        $postingCode = substr($line, 60, 1);
        switch ($postingCode) {
            case self::POSTING_CODE_DEBIT:
                $transaction->setDebit($amount);
                break;
            case self::POSTING_CODE_CREDIT:
                $transaction->setCredit($amount);
                break;
            case self::POSTING_CODE_DEBIT_REVERSAL:
                $transaction->setDebit($amount * (-1));
                break;
            case self::POSTING_CODE_CREDIT_REVERSAL:
                $transaction->setCredit($amount * (-1));
                break;
        }

        # Variable symbol
        $variableSymbol = ltrim(substr($line, 61, 10), '0');
        $transaction->setVariableSymbol($variableSymbol);

        # Constant symbol
        $constantSymbol = ltrim(substr($line, 77, 4), '0');
        $transaction->setConstantSymbol($constantSymbol);

        # Counter account number
        $counterAccountNumber = ltrim(substr($line, 19, 16), '0');
        $codeOfBank = substr($line, 73, 4);
        $transaction->setCounterAccountNumber($counterAccountNumber . '/' . $codeOfBank);

        # Specific symbol
        $specificSymbol = ltrim(substr($line, 81, 10), '0');
        $transaction->setSpecificSymbol($specificSymbol);

        # Note
        $note = rtrim(substr($line, 97, 20));
        $transaction->setNote($note);

        # Date created
        $date = substr($line, 122, 6);
        $dateCreated = \DateTime::createFromFormat('dmyHis', $date . '120000');
        $transaction->setDateCreated($dateCreated);

        return $transaction;
    }
}