<?php

namespace JakubZapletal\Component\BankStatement\Parser;

use DateTimeImmutable;
use Exception;
use JakubZapletal\Component\BankStatement\Statement\Statement;
use JakubZapletal\Component\BankStatement\Statement\Transaction\AdditionalInformation;
use JakubZapletal\Component\BankStatement\Statement\Transaction\Transaction;

/**
 * The ABO format is commonly used for exchanging financial messages in the Czech Republic and Slovakia
 *
 * @see https://www.csob.cz/portal/documents/10710/1927786/format-gpc.pdf
 *
 * Class Statement
 * @package JakubZapletal\Component\BankStatement\Parser
 */
class ABOParser extends Parser
{
    const LINE_TYPE_STATEMENT   = 'statement';
    const LINE_TYPE_TRANSACTION = 'transaction';
    const LINE_TYPE_ADDITIONAL_INFORMATION = 'additionalInformation';
    const LINE_TYPE_MESSAGE_START = 'messageStart';
    const LINE_TYPE_MESSAGE_END = 'messageEnd';

    const POSTING_CODE_DEBIT           = 1;
    const POSTING_CODE_CREDIT          = 2;
    const POSTING_CODE_DEBIT_REVERSAL  = 4;
    const POSTING_CODE_CREDIT_REVERSAL = 5;

    const POSTING_CODE_MAP = [
      1  => self::POSTING_CODE_DEBIT,
      2  => self::POSTING_CODE_CREDIT,
      4  => self::POSTING_CODE_DEBIT_REVERSAL,
      5  => self::POSTING_CODE_CREDIT_REVERSAL
    ];

    const POSTING_CODE_MAP_ALT = [
      1  => self::POSTING_CODE_DEBIT,
      2  => self::POSTING_CODE_CREDIT,
      3  => self::POSTING_CODE_DEBIT_REVERSAL,
      4  => self::POSTING_CODE_CREDIT_REVERSAL
    ];

    const BANKS_WITH_ALT_POSTING_CODE = [
      '0300', // Česká spořitelna
    ];

    const BANKS_WITH_CURRENCY_CODE_IN_TRANSACTION = [
      '0300', // ČSOB
      '2010', // FIO
    ];

    private const CURRENCIES = [
        '00036' => 'AUD',
        '00124' => 'CAD',
        '00156' => 'CNY',
        '00203' => 'CZK',
        '00208' => 'DKK',
        '00978' => 'EUR',
        '00826' => 'GBP',
        '00191' => 'HRK',
        '00348' => 'HUF',
        '00756' => 'CHF',
        '00392' => 'JPY',
        '00578' => 'NOK',
        '00985' => 'PLN',
        '00946' => 'RON',
        '00643' => 'RUB',
        '00752' => 'SEK',
        '00949' => 'TRY',
        '00840' => 'USD',
    ];

    /**
     * @param string $filePath
     *
     * @return Statement
     * @throws \RuntimeException
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
     * @return Statement
     */
    protected function parseFileObject(\SplFileObject $fileObject)
    {
        $this->statement = $this->getStatementClass();
        /** @var Transaction|null $transaction */
        $transaction = null;

        foreach ($fileObject as $line) {
            if ($fileObject->valid()) {
                switch ($this->getLineType($line)) {
                    case self::LINE_TYPE_STATEMENT:
                        $this->parseStatementLine($line);
                        break;
                    case self::LINE_TYPE_TRANSACTION:
                        if ($transaction) {
                            $this->statement->addTransaction($transaction);
                        }
                        $transaction = $this->parseTransactionLine($line);
                        break;
                    case self::LINE_TYPE_ADDITIONAL_INFORMATION:
                        $additionalInformation = $this->parseAdditionalInformationLine($line);
                        $transaction->setAdditionalInformation($additionalInformation);
                        break;
                    case self::LINE_TYPE_MESSAGE_START:
                        $messageStart = rtrim(substr($line, 3));
                        $transaction->setMessageStart($messageStart);
                        break;
                    case self::LINE_TYPE_MESSAGE_END:
                        $messageEnd = rtrim(substr($line, 3));
                        $transaction->setMessageEnd($messageEnd);
                        break;
                }
            }
        }

        if ($transaction) {
            $this->statement->addTransaction($transaction);
        }

        return $this->statement;
    }

    /**
     * @param string $line
     * @throws Exception
     * @return string|null
     */
    /** @noinspection PhpInconsistentReturnPointsInspection */
    protected function getLineType($line)
    {
        /**
         * All messages (lines with code 078 and 079) are valid only
         * for domestic payments where the line is just a message.
         * For foreign payments those lines contain different values. This is not implemented.
         */

        switch (substr($line, 0, 3)) {
            case '074':
                return self::LINE_TYPE_STATEMENT;
            case '075':
                return self::LINE_TYPE_TRANSACTION;
            case '076':
                return self::LINE_TYPE_ADDITIONAL_INFORMATION;
            case '078':
                return self::LINE_TYPE_MESSAGE_START;
            case '079':
                return self::LINE_TYPE_MESSAGE_END;
        }

        return null;
    }

    /**
     * No.| Name               | F/V | Pos | Len | Content | Implemented
     * ---|--------------------|-----|-----|-----|---------|
     * 1  | Record type        |  F  | 1   | 3   | 074     | Y
     * 2  | Account number     |  F  | 4   | 16  | int     | Y
     * 3  | Account owner name |  F  | 20  | 20  | string  | N
     * 4  | Date from          |  F  | 40  | 6   | ddmmyy  | Y
     * 5  | Old balance        |  F  | 46  | 14  | int     | N
     * 6  | Old balance sign   |  F  | 60  | 1   | + or -  | N
     * 7  | New balance        |  F  | 61  | 14  | int     | Y
     * 8  | New balance sign   |  F  | 75  | 1   | + or -  | Y
     * 9  | Debit sum          |  F  | 76  | 14  | int     | Y
     * 10 | Debit sign         |  F  | 90  | 1   | + or -  | Y
     * 11 | Credit sum         |  F  | 91  | 14  | int     | Y
     * 12 | Credit sign        |  F  | 105 | 1   | + or -  | Y
     * 13 | Statement number   |  F  | 106 | 3   | int     | Y
     * 14 | Date to            |  F  | 109 | 6   | ddmmyy  | Y
     * 15 | Filler             |  F  | 115 | 13  | (space) | Y
     *
     * @param string $line
     */
    protected function parseStatementLine($line)
    {
        # Account number
        $accountNumber = substr($line, 3, 6) . '-' . substr($line, 9, 6) . '/' . substr($line, 15, 4);
        $this->statement->setAccountNumber($accountNumber);

        # Date last balance
        $date = substr($line, 39, 6);
        $dateLastBalance = \DateTimeImmutable::createFromFormat('dmyHis', $date . '120000');
        $this->statement->setDateLastBalance($dateLastBalance);

        # Last balance
        $lastBalance = (int) ltrim(substr($line, 45, 14), '0') / 100;
        $lastBalanceSign = substr($line, 59, 1);
        if ($lastBalanceSign === '-') {
            $lastBalance *= -1;
        }
        $this->statement->setLastBalance($lastBalance);

        # Balance
        $balance = (int) ltrim(substr($line, 60, 14), '0') / 100;
        $balanceSign = substr($line, 74, 1);
        if ($balanceSign === '-') {
            $balance *= -1;
        }
        $this->statement->setBalance($balance);

        # Debit turnover
        $debitTurnover = (int) ltrim(substr($line, 75, 14), '0') / 100;
        $debitTurnoverSign = substr($line, 89, 1);
        if ($debitTurnoverSign === '-') {
            $debitTurnover *= -1;
        }
        $this->statement->setDebitTurnover($debitTurnover);

        # Credit turnover
        $creditTurnover = (int) ltrim(substr($line, 90, 14), '0') / 100;
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
        $dateCreated = \DateTimeImmutable::createFromFormat('dmyHis', $date . '120000');
        $this->statement->setDateCreated($dateCreated);
    }

    /**
     * No.| Name                      | F/V | Pos | Len | Content | Implemented
     * ---|---------------------------|-----|-----|-----|---------|
     * 1  | Record Type               |  F  | 1   | 3   | 075     | Y
     * 2  | Client account number     |  F  | 4   | 16  | int     | N
     * 3  | Counter-account number    |  F  | 20  | 16  | int     | Y
     * 4  | Identification            |  F  | 36  | 13  | string  | Y
     * 5  | Amount                    |  F  | 49  | 12  | int     | Y
     * 6  | Posting code              |  F  | 61  | 1   | int     | Y
     * 7  | Variable symbol           |  F  | 62  | 10  | int     | Y
     * 8  | Filler                    |  F  | 72  | 2   | 00      | Y
     * 9  | Counter-account bank code |  F  | 74  | 4   | int     | Y
     * 10 | Constant symbol           |  F  | 78  | 4   | int     | Y
     * 11 | Specific symbol           |  F  | 82  | 10  | int     | Y
     * 12 | Date                      |  F  | 92  | 6   | ddmmyy  | Y
     * 13 | Note                      |  F  | 98  | 20  | string  | Y
     * 14 | Currency or data type     |  F  | 118 | 5   | int     | N
     * 15 | Posting date              |  F  | 123 | 4   | ddmmyy  | N
     *
     * @param string $line
     * @throws Exception
     * @return Transaction
     */
    protected function parseTransactionLine($line)
    {
        $transaction = $this->getTransactionClass();

        # Receipt ID
        $receiptId = ltrim(substr($line, 35, 13), '0');
        $transaction->setReceiptId($receiptId);

        # Debit / Credit
        $amount = (int) ltrim(substr($line, 48, 12), '0') / 100;
        $postingCode = substr($line, 60, 1);
        $postingCodeMap = in_array($this->statement->getAccountNumberBankCode(), self::BANKS_WITH_ALT_POSTING_CODE)
            ? self::POSTING_CODE_MAP_ALT
            : self::POSTING_CODE_MAP;
        switch ($postingCodeMap[$postingCode]) {
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
        $counterAccountNumber = substr($line, 19, 6) . '-' . substr($line, 25, 10);
        $codeOfBank = substr($line, 73, 4);
        $transaction->setCounterAccountNumber($counterAccountNumber . '/' . $codeOfBank);

        # Specific symbol
        $specificSymbol = ltrim(substr($line, 81, 10), '0');
        $transaction->setSpecificSymbol($specificSymbol);

        # Note
        $note = rtrim(substr($line, 97, 20));
        $transaction->setNote($note);

        # Currency
        if (in_array($this->statement->getAccountNumberBankCode(), self::BANKS_WITH_CURRENCY_CODE_IN_TRANSACTION)) {
            $currencyCode = substr($line, 117, 5);
            $currency = $this->findCurrencyByCode($currencyCode);
            $transaction->setCurrency($currency);
        }

        # Date created
        $date = substr($line, 122, 6);
        $dateCreated = \DateTimeImmutable::createFromFormat('dmyHis', $date . '120000');
        $transaction->setDateCreated($dateCreated);
        return $transaction;
    }

    /**
     * No.| Name               | F/V | Pos | Len | Content | Implemented
     * ---|------              |-----|-----|-----|---------|
     * 1  | Record type        |  F  | 1   | 3   | 076     | Y
     * 2  | Identification     |  F  | 4   | 26  | string  | Y
     * 3  | Deduction date     |  F  | 30  | 6   | ddmmyy  | Y
     * 4  | Counter-party Name |  F  | 36  | 13  | string  | Y
     *
     * @param string $line
     *
     * @return AdditionalInformation
     */
    protected function parseAdditionalInformationLine(string $line): AdditionalInformation
    {
        $additionalInformation =  $this->getAdditionalInformationClass();

        # Transfer identification number
        $transferIdentificationNumber = ltrim(substr($line, 3, 26), '0');
        $additionalInformation->setTransferIdentificationNumber($transferIdentificationNumber);

        # Deduction date
        $date = substr($line, 29, 6);
        $deductionDate = DateTimeImmutable::createFromFormat('dmyHis', $date . '120000');
        $additionalInformation->setDeductionDate($deductionDate);

        # Counter-party Name
        $counterPartyName = rtrim(substr($line, 35, 92));
        $additionalInformation->setCounterPartyName($counterPartyName);

        return $additionalInformation;
    }

    /**
     * @param string $currencyCode
     * @throws Exception
     * @return string
     */
    private function findCurrencyByCode(string $currencyCode): string
    {
        if (!array_key_exists($currencyCode, self::CURRENCIES)) {
            throw new Exception('Unknown currency with code ' . $currencyCode);
        }

        return self::CURRENCIES[$currencyCode];
    }
}
