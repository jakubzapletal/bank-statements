<?php

namespace JakubZapletal\Component\BankStatement\Parser\XML;

use JakubZapletal\Component\BankStatement\Parser\XMLParser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelector;

class CSOBCZParser extends XMLParser
{
    const POSTING_CODE_DEBIT           = 'D';
    const POSTING_CODE_CREDIT          = 'C';
    const POSTING_CODE_DEBIT_REVERSAL  = 'DR';
    const POSTING_CODE_CREDIT_REVERSAL = 'CR';

    /**
     * @see XMLParser::parseContent()
     */
    public function parseContent($content)
    {
        $content = iconv("WINDOWS-1250", "UTF-8//IGNORE", $content);

        return parent::parseContent($content);
    }

    /**
     * @param Crawler $crawler
     *
     * @return \JakubZapletal\Component\BankStatement\Statement\Statement
     */
    protected function parseCrawler(Crawler $crawler)
    {
        $this->statement = $this->getStatementClass();

        CssSelector::disableHtmlExtension();

        $crawler = $crawler->filter('FINSTA > FINSTA03');

        $this->parseStatementNode($crawler);

        $crawler->filter('FINSTA05')->each(function(Crawler $node){
            $transaction = $this->parseTransactionNode($node);
            $this->statement->addTransaction($transaction);
        });

        return $this->statement;
    }

    /**
     * @param Crawler $crawler
     */
    protected function parseStatementNode(Crawler $crawler)
    {
        # Account number
        $accountNumber = $crawler->filter('S25_CISLO_UCTU')->text();
        $this->statement->setAccountNumber($accountNumber);

        # Date last balance
        $date = $crawler->filter('S60_DATUM')->text();
        $dateLastBalance = \DateTime::createFromFormat('d.m.Y His', $date . ' 120000');
        $dateLastBalance->modify('-1 DAY');
        $this->statement->setDateLastBalance($dateLastBalance);

        # Last balance
        $lastBalance = abs(str_replace(',', '.', $crawler->filter('S60_CASTKA')->text()));
        $postingCode = $crawler->filter('S60_CD_INDIK')->text();
        switch ($postingCode) {
            case self::POSTING_CODE_DEBIT:
                $this->statement->setLastBalance($lastBalance * (-1));
                break;
            case self::POSTING_CODE_CREDIT:
                $this->statement->setLastBalance($lastBalance);
                break;
        }

        # Balance
        $balance = abs(str_replace(',', '.', $crawler->filter('S62_CASTKA')->text()));
        $postingCode = $crawler->filter('S62_CD_INDIK')->text();
        switch ($postingCode) {
            case self::POSTING_CODE_DEBIT:
                $this->statement->setBalance($balance * (-1));
                break;
            case self::POSTING_CODE_CREDIT:
                $this->statement->setBalance($balance);
                break;
        }

        # Debit turnover
        $debitTurnover = str_replace(',', '.', $crawler->filter('SUMA_DEBIT')->text());
        $debitTurnover = str_replace('=', '', $debitTurnover);
        $this->statement->setDebitTurnover($debitTurnover);

        # Credit turnover
        $creditTurnover = str_replace(',', '.', $crawler->filter('SUMA_KREDIT')->text());
        $creditTurnover = str_replace('=', '', $creditTurnover);
        $this->statement->setCreditTurnover($creditTurnover);

        # Serial number
        $serialNumber = $crawler->filter('S28_CISLO_VYPISU')->text();
        $this->statement->setSerialNumber($serialNumber);

        # Date created
        $date = $crawler->filter('S62_DATUM')->text();
        $dateCreated = \DateTime::createFromFormat('d.m.Y His', $date . ' 120000');
        $this->statement->setDateCreated($dateCreated);
    }

    /**
     * @param Crawler $crawler
     *
     * @return \JakubZapletal\Component\BankStatement\Statement\Transaction\Transaction
     */
    protected function parseTransactionNode(Crawler $crawler)
    {
        $transaction = $this->getTransactionClass();

        # Receipt ID
        $receiptId = $crawler->filter('REF_TRANS_SYS')->text();
        $transaction->setReceiptId($receiptId);

        # Debit / Credit
        $amount = abs(str_replace(',', '.', $crawler->filter('S61_CASTKA')->text()));
        $postingCode = $crawler->filter('S61_CD_INDIK')->text();
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
        $variableSymbol = $crawler->filter('S86_VARSYMOUR')->text();
        $transaction->setVariableSymbol($variableSymbol);

        # Constant symbol
        $constantSymbol = $crawler->filter('S86_KONSTSYM')->text();
        $transaction->setConstantSymbol($constantSymbol);

        # Counter account number
        $counterAccountNumber = $crawler->filter('PART_ACCNO')->text();
        $codeOfBank = $crawler->filter('PART_BANK_ID')->text();
        $transaction->setCounterAccountNumber($counterAccountNumber . '/' . $codeOfBank);

        # Specific symbol
        $specificSymbol = $crawler->filter('S86_SPECSYMOUR')->text();
        $transaction->setSpecificSymbol($specificSymbol);

        # Note
        $notes = [];
        $notes[] = rtrim($crawler->filter('PART_ID1_1')->text());
        $notes[] = rtrim($crawler->filter('PART_ID1_2')->text());
        $notes[] = rtrim($crawler->filter('PART_ID2_1')->text());
        $notes[] = rtrim($crawler->filter('PART_ID2_2')->text());
        foreach ($notes as $key => $note) {
            if (strlen($note) === 0) {
                unset($notes[$key]);
            }
        }
        $transaction->setNote(implode('; ', $notes));

        # Date created
        $date = $crawler->filter('DPROCD')->text();
        $dateCreated = \DateTime::createFromFormat('d.m.Y His', $date . ' 120000');
        $transaction->setDateCreated($dateCreated);

        return $transaction;
    }
} 