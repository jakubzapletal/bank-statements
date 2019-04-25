<?php

namespace JakubZapletal\Component\BankStatement\Statement\Transaction;

interface TransactionInterface
{
    /**
     * @return string
     */
    public function getCounterAccountNumber();

    /**
     * @param $counterAccountNumber
     *
     * @return $this
     */
    public function setCounterAccountNumber($counterAccountNumber);

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

    public function getCurrency(): string;

    public function setCurrency(string $currency): void;

    public function getAdditionalInformation(): ?AdditionalInformation;

    public function setAdditionalInformation(?AdditionalInformation $additionalInformation): void;

    public function getMessageStart(): ?string;

    public function setMessageStart(?string $messageStart): void;

    public function getMessageEnd(): ?string;

    public function setMessageEnd(?string $messageStart): void;
}
