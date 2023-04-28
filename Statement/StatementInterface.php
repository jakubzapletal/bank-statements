<?php

namespace JakubZapletal\Component\BankStatement\Statement;

use JakubZapletal\Component\BankStatement\Statement\Transaction\TransactionInterface;

/**
 * Interface StatementInterface
 * @package JakubZapletal\Component\BankStatement\Statement
 */
interface StatementInterface
{
    /**
     * @return float
     */
    public function getBalance();

    /**
     * @param $balance
     *
     * @return $this
     */
    public function setBalance($balance);

    /**
     * @return float
     */
    public function getCreditTurnover();

    /**
     * @param $creditTurnover
     *
     * @return $this
     */
    public function setCreditTurnover($creditTurnover);

    /**
     * @return \DateTimeImmutable|null
     */
    public function getDateCreated();

    /**
     * @param \DateTimeImmutable $dateCreated
     *
     * @return $this
     */
    public function setDateCreated(\DateTimeImmutable $dateCreated);

    /**
     * @return string
     */
    public function getFiller();

    /**
     * @param string $filler
     *
     * @return $this
     */
    public function setFiller(string $filler);

    /**
     * @return string
     */
    public function getSerialNumber();

    /**
     * @param $serialNumber
     *
     * @return $this
     */
    public function setSerialNumber($serialNumber);

    /**
     * @return float
     */
    public function getDebitTurnover();

    /**
     * @param $debitTurnover
     *
     * @return $this
     */
    public function setDebitTurnover($debitTurnover);

    /**
     * @return string
     */
    public function getAccountNumber();

    /**
     * @param $accountNumber
     *
     * @return $this
     */
    public function setAccountNumber($accountNumber);

    /**
     * @return TransactionInterface[]
     */
    public function getTransactions();

    /**
     * @param TransactionInterface $transaction
     *
     * @return $this
     */
    public function addTransaction(TransactionInterface $transaction);

    /**
     * @param TransactionInterface $transaction
     *
     * @return $this
     */
    public function removeTransaction(TransactionInterface $transaction);
}
