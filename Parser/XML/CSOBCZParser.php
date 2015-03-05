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
        $content = str_replace('<?xml version="1.0" encoding="windows-1250"?>', '<?xml version="1.0" encoding="utf-8"?>', $content);

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

        if ($crawler !== null) {
            $this->parseStatementNode($crawler);

            $parser = $this;

            $crawler->filter('FINSTA05')->each(function (Crawler $node) use ($parser) {
                $parser->parseAndAddTransaction($node);
            });

        }

        return $this->statement;
    }

    /**
     * Used in Closure for purpose of PHP 5.3 compatibility
     *
     * @param Crawler $node
     */
    public function parseAndAddTransaction(Crawler $node)
    {
        $transaction = $this->parseTransactionNode($node);
        $this->statement->addTransaction($transaction);
    }

    /**
     * Tag | Type | Max L | M/O | Description
     * ----|------|-------|-----|------------
     * S28_CISLO_VYPISU | N | 5    | M | Order ID
     * S25_CISLO_UCTU   | C | 34   | M | Account number of client
     * SHORTNAME        | C | 35   | O | Short name of client
     * S60_CD_INDIK     | C | 1    | M | Post code, C - positive, D - negative
     * S60_DATUM        | D | 10   | M | Date of last statement
     * S60_MENA         | C | 3    | M | ISO code of account currency
     * S60_CASTKA       | N | 17.2 | M | Last balance
     * SUMA_KREDIT      | N | 17.2 | O | Credit turnover
     * SUMA_DEBIT       | N | 17.2 | O | Debit turnover
     * S62_CD_INDIK     | C | 1    | M | C/D indication of balance
     * S62_DATUM        | D | 10   | M | Date of balance
     * S62_CASTKA       | N | 17.2 | M | Amount of balance
     * S64_CD_INDIK     | C | 1    | O | C/D indication of available balance
     * S64_DATUM        | D | 10   | O | Date of available balance
     * S64_CASTKA       | N | 17.2 | O | Amount of available balance
     * FREKVENCE        | C | 1    | M | Frequency of statement, codes: D - daily, W, M, Q, H, Y
     * STAT_AST         | C | 1    | M | Status of statement: 9 - original, 7 - duplicate
     * TYPE_ACC_TXT     | C | 35   | M | Type of account
     * IR_START         | N | 9.3  | O | Origin interest rate
     * IR_END           | N | 9.3  | O | New interest rate
     * FREKV_TXT        | C | 20   | O | Frequency of statement by text
     *
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
     * Tag | Type | Max L | M/O | Description
     * ----|------|-------|-----|------------
     * S28_POR_CISLO  | N | 6    | M | Order
     * REF_TRANS_SYS  | C | 17   | O | Reference number of bank
     * TYP_CC         | N | 2    | O | Type of message by Clearing center
     * FACAERQ        | C | 35   | O | ID of origin oder
     * OPDIRC         | C | 3    | O | Operative code: DDT - collection, TRF - payment
     * S61_DATUM      | D | 10   | O | Date of foreign currency
     * S61_DINPUT     | D | 10   | O | Date of oder
     * DPROCD         | D | 10   | O | Date of process
     * DPROCOTHER     | D | 10   | O | Date of outcome
     * S61_CD_INDIK   | C | 2    | M | Sign of transaction
     * S61_MENA       | C | 3    | O | ISO code of currency
     * S61_CASTKA     | N | 17.2 | M | Amount
     * S61_TRANSAKCE  | C | 4    | O | Type of transaction
     * S61_REFERENCE  | C | 16   | O | Transaction reference
     * S61_POST_ORIG  | C | 8    | O | Code of description
     * S61_POST_NAR   | C | 30   | O | Description of article
     * DOM_ZAHR       | C | 3    | M | Distinction of trans.
     * S86_SPECSYMOUR | C | 10   | O | Specific symbol
     * S86_VARSYMOUR  | C | 10   | O | Variable symbol
     * S86_KONSTSYM   | C | 10   | O | Constant symbol
     * PART_BANK_ID   | C | 35   | O | Code of bank counter account
     * PART_ACCNO     | C | 35   | O | Account number
     * PART_ACC_ID    | C | 35   | O | Name of counter-account
     * S86_SPECSYMPAR | C | 10   | O | Specific symbol of counter-side
     * S86_VARSYMPAR  | C | 10   | O | Variable symbol of counter-side
     * PART_ID1_1     | C | 70   | O | Info for receiver
     * PART_ID1_2     | C | 70   | O | Info for receiver
     * PART_ID2_1     | C | 70   | O | Info for receiver
     * PART_ID2_2     | C | 70   | O | Info for receiver
     * PART_MSG_1     | C | 70   | O | Client message
     * PART_MSG_2     | C | 70   | O | Client message
     * ORIG_AMOUNT    | N | 17.2 | O | Amount
     * RATE           | N | 10.6 | O | Rate
     * ORIG_CURR      | C | 3    | O | ISO code of transaction currency
     * ACC_BAL        | N | 17.2 | O | Balance after move
     * S62M_CD_INDIK  | C | 1    | O | C/D indication balance after move
     * GRP_TR         | N | 3    | O | Code of transaction group
     * REMARK         | C | 35   | O | Note
     * S64_ BALAVL    | N | 16.2 | O | Available balance
     * S64_CD_IND     | C | 1    | O | C/D indication of balance
     * S64_ TIME      | T | 6    | O | Time of balance
     *
     * @param Crawler $crawler
     *
     * @return \JakubZapletal\Component\BankStatement\Statement\Transaction\Transaction
     */
    protected function parseTransactionNode(Crawler $crawler = null)
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
        $notes = array();
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
