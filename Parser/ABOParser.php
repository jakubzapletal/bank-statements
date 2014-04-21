<?php

namespace JakubZapletal\Component\BankStatement\Parser;

use JakubZapletal\Component\BankStatement\Statement\ABOStatement;
use JakubZapletal\Component\BankStatement\Statement\Transaction\Transaction;

class ABOParser extends Parser
{
    const LINE_TYPE_STATEMENT = 'statement';
    const LINE_TYPE_TRANSACTION = 'transaction';

    /**
     * @var ABOStatement
     */
    protected $statement;

    /**
     * @param string $filePath
     * @return ABOStatement
     * @throws \Exception
     */
    public function parse($filePath)
    {
        $file = new \SplFileObject($filePath);

        $this->statement = $this->getStatementClass();

        # Currency code
        $filename = $file->getBasename('.' . $file->getExtension());
        $this->statement->setCurrencyCode(substr($filename, -3));

        foreach ($file as $line) {
            if ($file->valid()) {
                switch ($this->getLineType($line)) {
                    case self::LINE_TYPE_STATEMENT:
                        $this->parseStatementLine($line);
                        break;
                    case self::LINE_TYPE_TRANSACTION:
                        $transaction = $this->parseTransactionLine($line);
                        $this->statement->addTransaction($transaction);
                        break;
                    default:
                        break;
                }
            }
        }

        return $this->statement;
    }

    /**
     * @return ABOStatement
     */
    protected function getStatementClass()
    {
        return new ABOStatement();
    }

    /**
     * @param string $line
     * @throws \Exception
     */
    protected function getLineType($line)
    {
        switch (substr($line, 0, 3)) {
            case '074':
                return self::LINE_TYPE_STATEMENT;
            case '075':
                return self::LINE_TYPE_TRANSACTION;
            default:
                return false;
        }
    }

    /**
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
        $accountingCode = substr($line, 60, 1);
        switch ($accountingCode) {
            case '1':
                $transaction->setDebit($amount);
                break;
            case '2':
                $transaction->setCredit($amount);
                break;
            case '4':
                $transaction->setDebit($amount * (-1));
                break;
            case '5':
                $transaction->setCredit($amount * (-1));
                break;
        }

        # Variable symbol
        $variableSymbol = ltrim(substr($line, 61, 10), '0');
        $transaction->setVariableSymbol($variableSymbol);

        # Constant symbol
        $constantSymbol = ltrim(substr($line, 77, 4), '0');
        $transaction->setConstantSymbol($constantSymbol);

        # Account number
        $accountNumber = ltrim(substr($line, 19, 16), '0');
        $codeOfBank = substr($line, 73, 4);
        $transaction->setAccountNumber($accountNumber . '/' . $codeOfBank);

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