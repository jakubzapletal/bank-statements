<?php

namespace JakubZapletal\Component\BankStatement\Statement\Transaction;

use JakubZapletal\Component\BankStatement\Statement\BankAccount;

class Transaction implements TransactionInterface
{
    /**
     * @var BankAccount
     */
    protected $counterBankAccount;

    /**
     * @var string
     */
    protected $receiptId;

    /**
     * @var float
     */
    protected $debit;

    /**
     * @var float
     */
    protected $credit;

    /**
     * @var int
     */
    protected $variableSymbol;

    /**
     * @var int
     */
    protected $constantSymbol;

    /**
     * @var int
     */
    protected $specificSymbol;

    /**
     * @var string
     */
    protected $note;

    /**
     * @var \DateTime
     */
    protected $dateCreated;

    /**
     * Only for backward compatibility
     * @deprecated
     *
     * @return string
     */
    public function getCounterAccountNumber()
    {
        if ($this->counterBankAccount === null) {
            return null;
        }
        return $this->counterBankAccount->getFormatted();
    }

    /**
     * @return BankAccount
     */
    public function getCounterBankAccount()
    {
        return $this->counterBankAccount;
    }

    /**
     * @param BankAccount $counterBankAccount
     *
     * @return $this
     */
    public function setCounterBankAccount(BankAccount $counterBankAccount)
    {
        $this->counterBankAccount = $counterBankAccount;

        return $this;
    }

    /**
     * @return int
     */
    public function getConstantSymbol()
    {
        return $this->constantSymbol;
    }

    /**
     * @param $constantSymbol
     *
     * @return $this
     */
    public function setConstantSymbol($constantSymbol)
    {
        $this->constantSymbol = (int)$constantSymbol;

        return $this;
    }

    /**
     * @return float
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * @param $credit
     *
     * @return $this
     */
    public function setCredit($credit)
    {
        $this->credit = (float)$credit;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param \DateTime $dateCreated
     *
     * @return $this
     */
    public function setDateCreated(\DateTime $dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * @return float
     */
    public function getDebit()
    {
        return $this->debit;
    }

    /**
     * @param $debit
     *
     * @return $this
     */
    public function setDebit($debit)
    {
        $this->debit = (float)$debit;

        return $this;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param $note
     *
     * @return $this
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return string
     */
    public function getReceiptId()
    {
        return $this->receiptId;
    }

    /**
     * @param $receiptId
     *
     * @return $this
     */
    public function setReceiptId($receiptId)
    {
        $this->receiptId = $receiptId;

        return $this;
    }

    /**
     * @return int
     */
    public function getSpecificSymbol()
    {
        return $this->specificSymbol;
    }

    /**
     * @param $specificSymbol
     *
     * @return $this
     */
    public function setSpecificSymbol($specificSymbol)
    {
        $this->specificSymbol = (int)$specificSymbol;

        return $this;
    }

    /**
     * @return int
     */
    public function getVariableSymbol()
    {
        return $this->variableSymbol;
    }

    /**
     * @param $variableSymbol
     *
     * @return $this
     */
    public function setVariableSymbol($variableSymbol)
    {
        $this->variableSymbol = (int)$variableSymbol;

        return $this;
    }
}
