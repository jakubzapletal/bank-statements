<?php

namespace JakubZapletal\Component\BankStatement\Statement\Transaction;

use JakubZapletal\Component\BankStatement\Statement\BankAccount;

interface TransactionInterface
{
    /**
     * @deprecated
     *
     * @return string
     */
    public function getCounterAccountNumber();

    /**
     * @return BankAccount
     */
    public function getCounterBankAccount();

    /**
     * @param BankAccount $counterBankAccount
     *
     * @return $this
     */
    public function setCounterBankAccount(BankAccount $counterBankAccount);

    /**
     * @return int
     */
    public function getConstantSymbol();

    /**
     * @param $constantSymbol
     *
     * @return $this
     */
    public function setConstantSymbol($constantSymbol);

    /**
     * @return float
     */
    public function getCredit();

    /**
     * @param $credit
     *
     * @return $this
     */
    public function setCredit($credit);

    /**
     * @return \DateTime|null
     */
    public function getDateCreated();

    /**
     * @param \DateTime $dateCreated
     *
     * @return $this
     */
    public function setDateCreated(\DateTime $dateCreated);

    /**
     * @return float
     */
    public function getDebit();

    /**
     * @param $debit
     *
     * @return $this
     */
    public function setDebit($debit);

    /**
     * @return string
     */
    public function getNote();

    /**
     * @param $note
     *
     * @return $this
     */
    public function setNote($note);

    /**
     * @return string
     */
    public function getReceiptId();

    /**
     * @param $receiptId
     *
     * @return $this
     */
    public function setReceiptId($receiptId);

    /**
     * @return int
     */
    public function getSpecificSymbol();

    /**
     * @param $specificSymbol
     *
     * @return $this
     */
    public function setSpecificSymbol($specificSymbol);

    /**
     * @return int
     */
    public function getVariableSymbol();

    /**
     * @param $variableSymbol
     *
     * @return $this
     */
    public function setVariableSymbol($variableSymbol);
}
