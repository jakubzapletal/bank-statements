<?php

namespace JakubZapletal\Component\BankStatement\Statement\Transaction;

class Transaction implements TransactionInterface
{
    /**
     * @var string
     */
    protected $accountNumber;

    /**
     * @var int
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
     * @return string
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * @param string $accountNumber
     *
     * @return $this
     */
    public function setAccountNumber($accountNumber)
    {
        $this->accountNumber = $accountNumber;

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
     * @param int $constantSymbol
     *
     * @return $this
     */
    public function setConstantSymbol($constantSymbol)
    {
        $this->constantSymbol = $constantSymbol;

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
     * @param float $credit
     *
     * @return $this
     */
    public function setCredit($credit)
    {
        $this->credit = (float) $credit;

        return $this;
    }

    /**
     * @return \DateTime
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
     * @param float $debit
     *
     * @return $this
     */
    public function setDebit($debit)
    {
        $this->debit = (float) $debit;

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
     * @param string $note
     *
     * @return $this
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return int
     */
    public function getReceiptId()
    {
        return $this->receiptId;
    }

    /**
     * @param int $receiptId
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
     * @param int $specificSymbol
     *
     * @return $this
     */
    public function setSpecificSymbol($specificSymbol)
    {
        $this->specificSymbol = $specificSymbol;

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
     * @param int $variableSymbol
     *
     * @return $this
     */
    public function setVariableSymbol($variableSymbol)
    {
        $this->variableSymbol = $variableSymbol;

        return $this;
    }
} 